<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocenteProfile;
use App\Models\AlumnoProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    private const USERS_PER_PAGE = 15;

    public function index(Request $request): View
    {
        $query = $this->baseUsersQuery();

        $this->applyUserFilters($request, $query);

        $users = $query->latest()->paginate(self::USERS_PER_PAGE)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function exportStudents(Request $request)
    {
        $validated = $request->validate([
            'format' => ['required', 'in:xlsx,csv,pdf'],
            'status' => ['nullable', 'in:0,1'],
        ]);

        $format    = $validated['format'];
        $timestamp = now()->format('Ymd_His');

        $query = $this->baseUsersQuery();
        $this->applyUserFilters($request, $query);

        $users = $query
            ->latest()
            ->get();

        $export = new StudentsExport($users);

        if ($format === 'pdf') {
            return response()->view('admin.users.print', [
                'users' => $users,
                'generatedAt' => now(),
            ]);
        }

        if ($format === 'csv') {
            return $export->downloadCsv("usuarios_filtrados_{$timestamp}.csv");
        }

        return $export->downloadXlsx("usuarios_filtrados_{$timestamp}.xlsx");
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
            'status'   => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        $user = User::create($validated);

        if ($user->isDocente()) {
            DocenteProfile::create([
                'user_id'          => $user->id,
                'title'            => $request->input('title'),
                'degree'           => $request->input('degree'),
                'specialty'        => $request->input('specialty'),
                'category'         => $request->input('category'),
                'years_of_service' => $request->input('years_of_service'),
                'bio'              => $request->input('bio'),
            ]);
        } elseif ($user->isAlumno()) {
            AlumnoProfile::create([
                'user_id'        => $user->id,
                'code'           => $request->input('code'),
                'promotion_year' => $request->input('promotion_year'),
                'program'        => $request->input('program'),
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
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

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

        return redirect()->route('admin.users.show', $user)
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

    private function applyUserFilters(Request $request, Builder $query): void
    {
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhereHas('alumnoProfile', function (Builder $profileQ) use ($search) {
                        $profileQ->where('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }
    }

    private function baseUsersQuery(): Builder
    {
        return User::query()
            ->with('alumnoProfile:id,user_id,code,promotion_year,program')
            ->select([
                'id', 'name', 'email', 'dni', 'phone',
                'avatar', 'role', 'status', 'created_at',
            ]);
    }
}
