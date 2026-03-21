<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\Week;
use App\Notifications\NewTaskPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'files.*'      => 'nullable|file|max:20480',
        ]);

        $data = $request->only(['title', 'description', 'instructions', 'due_date']);
        $data['week_id'] = $week->id;

        $task = Task::create($data);

        // Guardar múltiples archivos de tarea
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("tasks/{$course->id}", $filename, 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => $index,
                ]);
            }
        }

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
            'files.*'      => 'nullable|file|max:20480',
        ]);

        $task->update($request->only(['title', 'description', 'instructions', 'due_date']));

        // Si se suben nuevos archivos, reemplazar todos los archivos existentes
        if ($request->hasFile('files')) {
            // Eliminar archivos anteriores
            foreach ($task->taskFiles as $taskFile) {
                Storage::disk('public')->delete($taskFile->file_path);
                $taskFile->delete();
            }

            // También eliminar archivo legacy si existe
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
                $task->update(['file_path' => null]);
            }

            // Guardar nuevos archivos
            foreach ($request->file('files') as $index => $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("tasks/{$course->id}", $filename, 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => $index,
                ]);
            }
        }

        return back()->with('success', 'Tarea actualizada.');
    }

    public function destroy(Course $course, Week $week, Task $task)
    {
        $this->authorize('manage', $course);

        // Eliminar archivos de tarea
        foreach ($task->taskFiles as $taskFile) {
            Storage::disk('public')->delete($taskFile->file_path);
            $taskFile->delete();
        }

        // También eliminar archivo legacy si existe
        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
