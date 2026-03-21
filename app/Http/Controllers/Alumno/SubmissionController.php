<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    /**
     * Entregar una tarea (crear submission).
     */
    public function store(Request $request, Course $course, Task $task)
    {
        $user = auth()->user();

        // Verificar matrícula activa
        Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        // No se puede entregar si la tarea venció
        if ($task->isExpired()) {
            return back()->with('error', 'La fecha límite de esta tarea ya venció.');
        }

        // No se puede entregar si ya existe
        if (Submission::where('task_id', $task->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Ya tienes una entrega registrada para esta tarea.');
        }

        $request->validate([
            'files.*'  => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png|max:10240',
            'comments' => 'nullable|string|max:2000',
        ]);

        if (!$request->hasFile('files') && !$request->filled('comments')) {
            return back()->with('error', 'Debe adjuntar al menos un archivo o escribir un comentario.');
        }

        $data = [
            'task_id'      => $task->id,
            'user_id'      => $user->id,
            'comments'     => $request->comments,
            'submitted_at' => now(),
            'status'       => 'submitted',
        ];

        $submission = Submission::create($data);

        // Guardar múltiples archivos
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("submissions/{$task->id}", $filename, 'public');

                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => $index,
                ]);
            }
        }

        return back()->with('success', 'Tarea "' . $task->title . '" entregada exitosamente.');
    }

    /**
     * Actualizar una entrega existente.
     */
    public function update(Request $request, Course $course, Task $task, Submission $submission)
    {
        $user = auth()->user();

        // Verificar propiedad
        if ($submission->user_id !== $user->id) {
            abort(403);
        }

        // No se puede editar si ya fue calificada
        if ($submission->isGraded()) {
            return back()->with('error', 'No se puede editar una entrega ya calificada.');
        }

        // No se puede editar si la tarea venció
        if ($task->isExpired()) {
            return back()->with('error', 'La fecha límite de esta tarea ya venció.');
        }

        $request->validate([
            'files.*'  => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png|max:10240',
            'comments' => 'nullable|string|max:2000',
        ]);

        $data = [
            'comments'     => $request->comments,
            'submitted_at' => now(),
            'status'       => 'submitted',
        ];

        $submission->update($data);

        // Si se suben nuevos archivos, reemplazar todos los archivos existentes
        if ($request->hasFile('files')) {
            // Eliminar archivos anteriores
            foreach ($submission->submissionFiles as $submissionFile) {
                Storage::disk('public')->delete($submissionFile->file_path);
                $submissionFile->delete();
            }

            // También eliminar archivo legacy si existe
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
                $submission->update(['file_path' => null, 'original_filename' => null]);
            }

            // Guardar nuevos archivos
            foreach ($request->file('files') as $index => $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("submissions/{$task->id}", $filename, 'public');

                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => $index,
                ]);
            }
        }

        return back()->with('success', 'Entrega actualizada exitosamente.');
    }
}
