<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocenteProfile;
use App\Models\AlumnoProfile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('dni', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,docente,alumno',
            'dni'      => 'nullable|string|max:20|unique:users,dni',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create($validated);

        if ($user->isDocente()) {
            DocenteProfile::create(['user_id' => $user->id]);
        } elseif ($user->isAlumno()) {
            AlumnoProfile::create([
                'user_id' => $user->id,
                'program' => $request->program,
                'promotion_year' => $request->promotion_year,
                'code'    => $request->code,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load(['docenteProfile', 'alumnoProfile', 'coursesTaught', 'enrollments.course']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['docenteProfile', 'alumnoProfile']);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,docente,alumno',
            'dni'   => 'nullable|string|max:20|unique:users,dni,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = $request->password;
        }

        $user->update($validated);

        // Update profile
        if ($user->isDocente()) {
            $user->docenteProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['title', 'degree', 'specialty', 'category', 'years_of_service', 'bio'])
            );
        } elseif ($user->isAlumno()) {
            $user->alumnoProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['code', 'promotion_year', 'program'])
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puede eliminarse a sí mismo.');
        }

        $user->update(['status' => false]);
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario desactivado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);
        return response()->json([
            'status'  => $user->status,
            'message' => $user->status ? 'Usuario activado' : 'Usuario desactivado',
        ]);
    }
}
