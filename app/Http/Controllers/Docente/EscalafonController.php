<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\DocenteProfile;
use Illuminate\Http\Request;

class EscalafonController extends Controller
{
    public function show()
    {
        $user    = auth()->user();
        $profile = $user->docenteProfile ?? new DocenteProfile(['user_id' => $user->id]);
        return view('docente.escalafon.show', compact('user', 'profile'));
    }

    public function edit()
    {
        $user    = auth()->user();
        $profile = $user->docenteProfile ?? new DocenteProfile(['user_id' => $user->id]);
        return view('docente.escalafon.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'            => 'nullable|string|max:20',
            'degree'           => 'nullable|string|max:255',
            'specialty'        => 'nullable|string|max:255',
            'category'         => 'nullable|string|max:100',
            'years_of_service' => 'nullable|integer|min:0|max:60',
            'bio'              => 'nullable|string|max:2000',
        ]);

        auth()->user()->docenteProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only(['title', 'degree', 'specialty', 'category', 'years_of_service', 'bio'])
        );

        return redirect()->route('docente.escalafon.show')
            ->with('success', 'Perfil profesional actualizado exitosamente.');
    }
}
