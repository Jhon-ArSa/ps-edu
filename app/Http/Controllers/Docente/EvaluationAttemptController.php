<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use Illuminate\Http\Request;

class EvaluationAttemptController extends Controller
{
    // ── Listar intentos de una evaluación ─────────────────────────────────────

    public function index(Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);

        $evaluation->load('questions');

        $attempts = $evaluation->attempts()
            ->with(['student.alumnoProfile', 'answers'])
            ->whereIn('status', ['submitted', 'graded'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $submittedUserIds = $attempts->pluck('user_id');
        $pendingStudents  = $course->students()
            ->whereNotIn('users.id', $submittedUserIds)
            ->with('alumnoProfile')
            ->orderBy('name')
            ->get();

        $stats = [
            'total'         => $course->students()->count(),
            'submitted'     => $attempts->count(),
            'reviewed'      => $attempts->where('status', 'graded')->count(),
            'pending_review'=> $attempts->filter(fn ($a) => $a->status === 'submitted' && $a->hasUngradedShortAnswers())->count(),
        ];

        return view('docente.evaluations.attempts', compact(
            'course', 'evaluation', 'attempts', 'pendingStudents', 'stats'
        ));
    }

    // ── Revisar respuestas cortas ─────────────────────────────────────────────

    public function gradeShort(Request $request, Course $course, Evaluation $evaluation, EvaluationAttempt $attempt)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($attempt->evaluation_id === $evaluation->id, 404);

        $request->validate([
            'scores'            => 'required|array',
            'scores.*'          => 'nullable|numeric|min:0',
        ]);

        $attempt->load(['answers.question', 'evaluation.questions']);

        foreach ($request->scores as $answerId => $score) {
            $answer = $attempt->answers->firstWhere('id', $answerId);
            if (!$answer || $answer->question->type !== 'short') continue;

            $maxPoints = $answer->question->points;
            $score     = min((float) $score, $maxPoints);

            $answer->update([
                'score'      => $score,
                'is_correct' => $score >= $maxPoints,
            ]);
        }

        // Recalcular puntaje total
        $attempt->load('answers'); // refresh
        $totalPoints = $evaluation->questions->sum('points');
        $earnedPoints = $attempt->answers->whereNotNull('score')->sum('score');

        $normalScore = $totalPoints > 0
            ? round(($earnedPoints / $totalPoints) * $evaluation->max_score, 1)
            : 0.0;

        $attempt->update([
            'score'  => $normalScore,
            'status' => 'graded',
        ]);

        return back()->with('success', 'Intento de ' . $attempt->student->name . ' revisado correctamente.');
    }
}
