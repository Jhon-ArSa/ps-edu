<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\GradeItem;
use App\Models\Task;
use App\Models\Week;
use App\Notifications\NewTaskPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(Request $request, Course $course, Week $week)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date'     => 'nullable|date',
            'max_score'    => 'nullable|integer|min:1|max:1000',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:20480',
        ]);

        $data = $request->only(['title', 'description', 'instructions', 'due_date', 'max_score']);
        $data['week_id'] = $week->id;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store("tasks/{$course->id}", 'public');
        }

        $task = Task::create($data);

        // Registrar columna en la libreta de notas
        GradeItem::syncFromTask($task->load('week'));

        // Notificar a alumnos matriculados activos
        $students = $course->students()->get();
        if ($students->isNotEmpty()) {
            Notification::send($students, new NewTaskPublished(
                taskTitle:  $task->title,
                courseId:   $course->id,
                courseName: $course->name,
            ));
        }

        return back()->with('success', 'Tarea "' . $request->title . '" creada exitosamente.');
    }

    public function update(Request $request, Course $course, Week $week, Task $task)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date'     => 'nullable|date',
            'max_score'    => 'nullable|integer|min:1|max:1000',
        ]);

        $task->update($request->only(['title', 'description', 'instructions', 'due_date', 'max_score']));

        // Sincronizar nombre/max_score en la libreta de notas
        GradeItem::syncFromTask($task->load('week'));

        return back()->with('success', 'Tarea actualizada.');
    }

    public function destroy(Course $course, Week $week, Task $task)
    {
        $this->authorize('manage', $course);

        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        // Eliminar el ítem de la libreta de notas asociado a esta tarea
        GradeItem::where('type', GradeItem::TYPE_TASK)
            ->where('reference_id', $task->id)
            ->delete();

        $task->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
