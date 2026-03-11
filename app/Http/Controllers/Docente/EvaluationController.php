<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationOption;
use App\Models\GradeItem;
use App\Models\Week;
use App\Notifications\NewEvaluationAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluationController extends Controller
{
    // ── Crear evaluación en una semana ────────────────────────────────────────

    public function store(Request $request, Course $course, Week $week)
    {
        $this->authorize('manage', $course);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png|max:10240',
            'time_limit'   => 'nullable|integer|min:1|max:300',
            'opens_at'     => 'nullable|date',
            'closes_at'    => 'nullable|date|after_or_equal:opens_at',
            'max_score'    => 'nullable|numeric|min:1|max:100',
            'max_attempts' => 'nullable|integer|min:1|max:10',
            'show_results' => 'nullable|boolean',
        ]);

        $data['week_id']      = $week->id;
        $data['max_score']    = $data['max_score'] ?? 20.0;
        $data['max_attempts'] = $data['max_attempts'] ?? 1;
        $data['show_results'] = $request->boolean('show_results');

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('evaluations', 'public');
        }
        unset($data['file']);

        $evaluation = Evaluation::create($data);

        // Crear ítem en la libreta de notas
        GradeItem::syncFromEvaluation($evaluation);

        return redirect()
            ->route('docente.evaluations.show', [$course, $evaluation])
            ->with('success', 'Evaluación "' . $evaluation->title . '" creada. Ahora agrega las preguntas.');
    }

    // ── Gestionar evaluación (preguntas) ──────────────────────────────────────

    public function show(Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);

        $evaluation->load(['questions.options', 'attempts']);

        $stats = [
            'questions'   => $evaluation->questions->count(),
            'total_points'=> $evaluation->questions->sum('points'),
            'submitted'   => $evaluation->attempts->whereIn('status', ['submitted', 'graded'])->count(),
            'pending_grade'=> $evaluation->attempts->where(fn ($a) => $a->hasUngradedShortAnswers())->count(),
        ];

        return view('docente.evaluations.show', compact('course', 'evaluation', 'stats'));
    }

    // ── Actualizar evaluación ─────────────────────────────────────────────────

    public function update(Request $request, Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($evaluation->status === 'draft', 403, 'Solo se pueden editar evaluaciones en borrador.');

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'time_limit'   => 'nullable|integer|min:1|max:300',
            'opens_at'     => 'nullable|date',
            'closes_at'    => 'nullable|date|after_or_equal:opens_at',
            'max_score'    => 'nullable|numeric|min:1|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'show_results' => 'nullable|boolean',
        ]);

        $data['show_results'] = $request->boolean('show_results');
        $evaluation->update($data);

        // Sincronizar nombre en libreta de notas
        GradeItem::syncFromEvaluation($evaluation);

        return back()->with('success', 'Evaluación actualizada.');
    }

    // ── Eliminar evaluación ───────────────────────────────────────────────────

    public function destroy(Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($evaluation->status === 'draft', 403, 'Solo se puede eliminar una evaluación en borrador.');

        $evaluation->delete();

        return redirect()
            ->route('docente.courses.show', $course)
            ->with('success', 'Evaluación eliminada.');
    }

    // ── Actualizar archivo adjunto ────────────────────────────────────────────

    public function updateFile(Request $request, Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png|max:10240',
        ]);

        if ($evaluation->file_path) {
            Storage::disk('public')->delete($evaluation->file_path);
        }

        $path = $request->file('file')->store('evaluations', 'public');
        $evaluation->update(['file_path' => $path]);

        return back()->with('success', 'Archivo de evaluación actualizado.');
    }

    // ── Publicar / cerrar evaluación ──────────────────────────────────────────

    public function toggleStatus(Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($evaluation->questions()->exists(), 422, 'Debes agregar al menos una pregunta antes de publicar.');

        if ($evaluation->status === 'draft') {
            $evaluation->update(['status' => 'published']);

            // Notificar a alumnos matriculados
            $students = $course->students;
            foreach ($students as $student) {
                $student->notify(new NewEvaluationAvailable(
                    $evaluation->title,
                    $course->id,
                    $course->name
                ));
            }

            return back()->with('success', 'Evaluación publicada. Se notificó a ' . $students->count() . ' alumno(s).');
        }

        if ($evaluation->status === 'published') {
            $evaluation->update(['status' => 'closed']);
            return back()->with('success', 'Evaluación cerrada.');
        }

        return back()->with('error', 'No se puede cambiar el estado de esta evaluación.');
    }

    // ── Agregar pregunta (JSON) ────────────────────────────────────────────────

    public function addQuestion(Request $request, Course $course, Evaluation $evaluation)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($evaluation->status === 'draft', 403, 'No se pueden agregar preguntas a una evaluación publicada.');

        $data = $request->validate([
            'type'        => 'required|in:multiple_one,multiple_many,true_false,short',
            'text'        => 'required|string',
            'points'      => 'required|numeric|min:0.5|max:100',
            'explanation' => 'nullable|string',
            'options'     => 'required_unless:type,short|array|min:2',
            'options.*.text'       => 'required_with:options|string|max:500',
            'options.*.is_correct' => 'required_with:options|boolean',
        ]);

        $question = $evaluation->questions()->create([
            'type'        => $data['type'],
            'text'        => $data['text'],
            'points'      => $data['points'],
            'explanation' => $data['explanation'] ?? null,
            'order'       => $evaluation->questions()->max('order') + 1,
        ]);

        if ($question->type !== 'short' && !empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'text'       => $opt['text'],
                    'is_correct' => (bool) ($opt['is_correct'] ?? false),
                    'order'      => $i,
                ]);
            }
        }

        $question->load('options');

        return response()->json([
            'success'  => true,
            'question' => [
                'id'          => $question->id,
                'type'        => $question->type,
                'type_label'  => $question->type_label,
                'type_badge'  => $question->type_badge_class,
                'text'        => $question->text,
                'points'      => $question->points,
                'explanation' => $question->explanation,
                'order'       => $question->order,
                'options'     => $question->options->map(fn ($o) => [
                    'id'         => $o->id,
                    'text'       => $o->text,
                    'is_correct' => $o->is_correct,
                ])->toArray(),
            ],
        ]);
    }

    // ── Eliminar pregunta ─────────────────────────────────────────────────────

    public function removeQuestion(Course $course, Evaluation $evaluation, EvaluationQuestion $question)
    {
        $this->authorize('manage', $course);
        abort_unless($evaluation->week->course_id === $course->id, 404);
        abort_unless($question->evaluation_id === $evaluation->id, 404);
        abort_unless($evaluation->status === 'draft', 403, 'No se puede eliminar preguntas de una evaluación publicada.');

        $question->delete();

        return response()->json(['success' => true]);
    }
}
