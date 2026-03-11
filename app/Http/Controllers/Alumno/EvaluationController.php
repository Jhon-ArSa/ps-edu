<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AttemptAnswer;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluationController extends Controller
{
    // ── Instrucciones / estado de la evaluación ───────────────────────────────

    public function show(Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($course->students()->where('users.id', auth()->id())->exists(), 403, 'No estás matriculado en este curso.');

        $userId   = auth()->id();
        $attempts = $evaluation->attemptsFor($userId);
        $active   = $evaluation->activeAttemptFor($userId);

        return view('alumno.evaluations.show', compact(
            'course', 'evaluation', 'attempts', 'active'
        ));
    }

    // ── Iniciar nuevo intento ─────────────────────────────────────────────────

    public function start(Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($course->students()->where('users.id', auth()->id())->exists(), 403);
        abort_unless($evaluation->isOpen(), 403, 'Esta evaluación no está disponible en este momento.');

        $userId     = auth()->id();
        $usedAttempts = $evaluation->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        abort_unless($usedAttempts < $evaluation->max_attempts, 403, 'Has alcanzado el número máximo de intentos.');

        // Si ya hay un intento en progreso, redirigir a él
        $active = $evaluation->activeAttemptFor($userId);
        if ($active) {
            return redirect()->route('alumno.evaluations.take', [$course, $evaluation]);
        }

        $attempt = EvaluationAttempt::create([
            'evaluation_id'  => $evaluation->id,
            'user_id'        => $userId,
            'started_at'     => now(),
            'status'         => 'in_progress',
            'attempt_number' => $usedAttempts + 1,
        ]);

        return redirect()->route('alumno.evaluations.take', [$course, $evaluation]);
    }

    // ── Tomar la evaluación ───────────────────────────────────────────────────

    public function take(Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($course->students()->where('users.id', auth()->id())->exists(), 403);

        $attempt = $evaluation->activeAttemptFor(auth()->id());
        abort_unless($attempt, 404, 'No tienes un intento activo para esta evaluación.');

        // Si el temporizador expiró, auto-enviar
        if ($attempt->isTimerExpired()) {
            return $this->doSubmit(null, $course, $evaluation, $attempt, collect());
        }

        $evaluation->load('questions.options');
        $existingAnswers = $attempt->answers()->get()->keyBy('question_id');

        return view('alumno.evaluations.take', compact(
            'course', 'evaluation', 'attempt', 'existingAnswers'
        ));
    }

    // ── Enviar evaluación ─────────────────────────────────────────────────────

    public function submit(Request $request, Course $course, Evaluation $evaluation)
    {
        abort_unless($evaluation->week->course_id === $course->id, 404);

        $attempt = $evaluation->activeAttemptFor(auth()->id());
        abort_unless($attempt, 404, 'No tienes un intento activo.');

        $answers = collect($request->input('answers', []));
        return $this->doSubmit($request, $course, $evaluation, $attempt, $answers);
    }

    // ── Ver resultado ─────────────────────────────────────────────────────────

    public function result(Course $course, Evaluation $evaluation, EvaluationAttempt $attempt)
    {
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($attempt->user_id === auth()->id(), 403);
        abort_unless($attempt->evaluation_id === $evaluation->id, 404);
        abort_unless($attempt->isSubmitted(), 403, 'Este intento aún no ha sido enviado.');

        if (!$evaluation->show_results && !$attempt->isGraded()) {
            return view('alumno.evaluations.show', [
                'course'    => $course,
                'evaluation'=> $evaluation,
                'attempts'  => $evaluation->attemptsFor(auth()->id()),
                'active'    => null,
            ])->with('info', 'Tu evaluación fue recibida. Los resultados serán visibles cuando el docente los habilite.');
        }

        $evaluation->load('questions.options');
        $answerMap = $attempt->answers()->get()->keyBy('question_id');

        return view('alumno.evaluations.result', compact(
            'course', 'evaluation', 'attempt', 'answerMap'
        ));
    }

    // ── Lógica interna de envío ───────────────────────────────────────────────

    private function doSubmit(?Request $request, Course $course, Evaluation $evaluation, EvaluationAttempt $attempt, $answers)
    {
        $evaluation->load('questions.options');

        foreach ($evaluation->questions as $question) {
            $raw = $answers[$question->id] ?? null;

            $selectedOptions = null;
            $textAnswer      = null;

            if ($question->type === 'short') {
                $textAnswer = is_string($raw) ? trim($raw) : null;
            } else {
                $ids = is_array($raw) ? array_map('intval', $raw) : (is_numeric($raw) ? [(int)$raw] : []);
                $selectedOptions = $ids ?: null;
            }

            AttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                ['selected_options' => $selectedOptions, 'text_answer' => $textAnswer]
            );
        }

        // Guardar archivo adjunto del alumno (si envió uno)
        if ($request && $request->hasFile('file_answer')) {
            $file = $request->file('file_answer');
            if ($attempt->file_path) {
                Storage::disk('public')->delete($attempt->file_path);
            }
            $attempt->update([
                'file_path'         => $file->store("evaluation-answers/{$attempt->id}", 'public'),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        // Refrescar respuestas y calificar automáticamente
        $attempt->load('answers.question.options');
        EvaluationAttempt::autoGrade($attempt);
        $attempt->refresh();

        // Si no hay respuestas cortas, registrar en libreta de notas inmediatamente
        if (!$attempt->hasUngradedShortAnswers()) {
            Grade::recordFromAttempt($attempt);
        }

        return redirect()
            ->route('alumno.evaluations.result', [$course, $evaluation, $attempt])
            ->with('success', 'Evaluación enviada exitosamente.');
    }
}
