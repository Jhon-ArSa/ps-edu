<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load(['docenteProfile', 'alumnoProfile']);
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $validated = $request->validate([
                'name'  => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'dni'   => 'nullable|string|max:20|unique:users,dni,' . $user->id,
            ]);
            $user->update($validated);
        } else {
            // Docentes y alumnos solo pueden cambiar su teléfono
            $validated = $request->validate([
                'phone' => 'nullable|string|max:20',
            ]);
            $user->update($validated);
        }

        return back()->with('success', 'Perfil actualizado exitosamente.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto de perfil actualizada.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Contraseña actualizada exitosamente.');
    }
}
