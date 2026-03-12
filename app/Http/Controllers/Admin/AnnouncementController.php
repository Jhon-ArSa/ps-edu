<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::with('author')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,docente,alumno,admin',
            'published_at'=> 'nullable|date',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['author_id'] = auth()->id();
        unset($validated['image']);

        // Publicar inmediatamente si se solicitó y no se ingresó fecha manual
        if ($request->boolean('publish_now') && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create($validated);

        // Notificar si se publicó inmediatamente (published_at <= now)
        if ($announcement->isPublished()) {
            $query = User::where('status', true);
            if ($announcement->target_role !== 'all') {
                $query->where('role', $announcement->target_role);
            }
            $recipients = $query->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new NewAnnouncementPublished(
                    announcementTitle: $announcement->title,
                ));
            }
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado creado exitosamente.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,docente,alumno,admin',
            'published_at'=> 'nullable|date',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Guardar estado previo para detectar transición borrador → publicado
        $wasUnpublished = ! $announcement->isPublished();

        unset($validated['image']);

        // Publicar inmediatamente si se solicitó y no se ingresó fecha manual
        if ($request->boolean('publish_now') && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($request->boolean('remove_image') && $announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
            $validated['image_path'] = null;
        } elseif ($request->hasFile('image')) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($validated);

        // Notificar si se acaba de publicar (era borrador y ahora está publicado)
        if ($wasUnpublished && $announcement->fresh()->isPublished()) {
            $query = User::where('status', true);
            if ($announcement->target_role !== 'all') {
                $query->where('role', $announcement->target_role);
            }
            $recipients = $query->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new NewAnnouncementPublished(
                    announcementTitle: $announcement->title,
                ));
            }
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado actualizado exitosamente.');
    }

    public function intranet()
    {
        $announcements = Announcement::published()
            ->forRole('admin')
            ->latest('published_at')
            ->paginate(10);

        return view('admin.intranet', compact('announcements'));
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado eliminado exitosamente.');
    }
}
