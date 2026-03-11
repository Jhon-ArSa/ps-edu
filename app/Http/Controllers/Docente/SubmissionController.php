<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Ver todas las entregas de una tarea.
     */
    public function index(Course $course, Task $task)
    {
        $this->authorize('manage', $course);

        $task->load('week');

        $submissions = $task->submissions()
            ->with('student.alumnoProfile')
            ->orderByRaw("FIELD(status, 'submitted', 'graded', 'pending')")
            ->orderBy('submitted_at', 'desc')
            ->get();

        $submittedUserIds = $submissions->pluck('user_id');

        $pendingStudents = $course->students()
            ->whereNotIn('users.id', $submittedUserIds)
            ->with('alumnoProfile')
            ->orderBy('name')
            ->get();

        $stats = [
            'total'     => $course->students()->count(),
            'submitted' => $submissions->where('status', 'submitted')->count(),
            'graded'    => $submissions->where('status', 'graded')->count(),
            'pending'   => $pendingStudents->count(),
            'avg_score' => $submissions->where('status', 'graded')->avg('score'),
        ];

        return view('docente.courses.submissions', compact(
            'course', 'task', 'submissions', 'pendingStudents', 'stats'
        ));
    }

    /**
     * Calificar una entrega.
     */
    public function grade(Request $request, Course $course, Task $task, Submission $submission)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'score'    => 'required|numeric|min:0|max:' . $task->max_score,
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'score'     => $request->score,
            'feedback'  => $request->feedback,
            'status'    => 'graded',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Entrega de ' . $submission->student->name . ' calificada con ' . $request->score . '/' . $task->max_score . '.');
    }
}
