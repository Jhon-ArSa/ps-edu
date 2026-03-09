<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumItem;
use App\Models\Mention;
use App\Models\Program;
use Illuminate\Http\Request;

class MentionController extends Controller
{
    public function create(Program $program)
    {
        return view('admin.programs.mentions.create', compact('program'));
    }

    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'items'       => 'nullable|array',
            'items.*.semester_number' => 'required_with:items|integer|min:0|max:20',
            'items.*.course_name'     => 'required_with:items|string|max:255',
            'items.*.credits'         => 'nullable|integer|min:1|max:30',
            'items.*.is_elective'     => 'boolean',
        ]);

        $mention = $program->mentions()->create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'order'       => $program->mentions()->max('order') + 1,
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $i => $item) {
                CurriculumItem::create([
                    'program_id'      => $program->id,
                    'mention_id'      => $mention->id,
                    'semester_number'  => $item['semester_number'],
                    'course_name'     => $item['course_name'],
                    'credits'         => $item['credits'] ?? null,
                    'is_elective'     => !empty($item['is_elective']),
                    'order'           => $i,
                ]);
            }
        }

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Mención "' . $mention->name . '" creada exitosamente.');
    }

    public function edit(Program $program, Mention $mention)
    {
        $mention->load('curriculumItems');
        return view('admin.programs.mentions.edit', compact('program', 'mention'));
    }

    public function update(Request $request, Program $program, Mention $mention)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'items'       => 'nullable|array',
            'items.*.semester_number' => 'required_with:items|integer|min:0|max:20',
            'items.*.course_name'     => 'required_with:items|string|max:255',
            'items.*.credits'         => 'nullable|integer|min:1|max:30',
            'items.*.is_elective'     => 'boolean',
        ]);

        $mention->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
        ]);

        // Replace all curriculum items for this mention
        $mention->curriculumItems()->delete();

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $i => $item) {
                CurriculumItem::create([
                    'program_id'      => $program->id,
                    'mention_id'      => $mention->id,
                    'semester_number'  => $item['semester_number'],
                    'course_name'     => $item['course_name'],
                    'credits'         => $item['credits'] ?? null,
                    'is_elective'     => !empty($item['is_elective']),
                    'order'           => $i,
                ]);
            }
        }

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Mención "' . $mention->name . '" actualizada exitosamente.');
    }

    public function destroy(Program $program, Mention $mention)
    {
        $name = $mention->name;
        $mention->delete(); // cascade deletes curriculum_items

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Mención "' . $name . '" eliminada exitosamente.');
    }
}
