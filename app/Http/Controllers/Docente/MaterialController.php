<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Week;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function store(Request $request, Course $course, Week $week)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'type'        => 'required|in:file,link,video',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['type', 'title', 'description']);
        $data['week_id'] = $week->id;
        $data['order']   = $week->materials()->max('order') + 1;

        if ($request->type === 'file') {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,mp4|max:51200',
            ]);
            $data['file_path'] = $request->file('file')->store("materials/{$course->id}", 'public');
        } else {
            $request->validate(['url' => 'required|url|max:1000']);
            $data['url'] = $request->url;
        }

        Material::create($data);
        return back()->with('success', 'Material agregado exitosamente.');
    }

    public function update(Request $request, Course $course, Week $week, Material $material)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $material->update($request->only(['title', 'description']));
        return response()->json(['success' => true]);
    }

    public function destroy(Course $course, Week $week, Material $material)
    {
        $this->authorize('manage', $course);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();
        return back()->with('success', 'Material eliminado.');
    }

    public function reorder(Request $request, Course $course, Week $week)
    {
        $this->authorize('manage', $course);

        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $position => $materialId) {
            $week->materials()->where('id', $materialId)->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
