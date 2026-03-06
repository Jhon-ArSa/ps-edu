@extends('layouts.app')

@section('title', $user->name)

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="hover:text-primary-600">Usuarios</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-primary-100 shrink-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-primary-600 text-xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' :
                           ($user->role === 'docente' ? 'bg-violet-100 text-violet-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $user->status ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->status ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $user->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="text-gray-500 text-sm mt-0.5">{{ $user->email }}</p>
                <div class="flex gap-4 mt-2 text-sm text-gray-500">
                    @if($user->dni) <span>DNI: {{ $user->dni }}</span> @endif
                    @if($user->phone) <span>Tel: {{ $user->phone }}</span> @endif
                    <span>Desde: {{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            <a href="{{ route('admin.users.edit', $user) }}"
               class="shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>
    </div>

    {{-- Docente profile --}}
    @if($user->isDocente() && $user->docenteProfile)
    @php $dp = $user->docenteProfile; @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Perfil profesional</h3>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div><dt class="text-gray-400">Título</dt><dd class="font-medium text-gray-900">{{ $dp->title ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Grado</dt><dd class="font-medium text-gray-900">{{ $dp->degree ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Especialidad</dt><dd class="font-medium text-gray-900">{{ $dp->specialty ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Categoría</dt><dd class="font-medium text-gray-900">{{ $dp->category ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Años de servicio</dt><dd class="font-medium text-gray-900">{{ $dp->years_of_service ?? '—' }}</dd></div>
        </dl>
        @if($dp->bio)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <dt class="text-gray-400 text-sm mb-1">Biografía</dt>
            <dd class="text-sm text-gray-700">{{ $dp->bio }}</dd>
        </div>
        @endif
    </div>
    @endif

    {{-- Alumno profile --}}
    @if($user->isAlumno() && $user->alumnoProfile)
    @php $ap = $user->alumnoProfile; @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Datos académicos</h3>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div><dt class="text-gray-400">Código</dt><dd class="font-medium text-gray-900">{{ $ap->code ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Promoción</dt><dd class="font-medium text-gray-900">{{ $ap->promotion_year ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-400">Programa</dt><dd class="font-medium text-gray-900">{{ $ap->program ?? '—' }}</dd></div>
        </dl>
    </div>
    @endif

    {{-- Courses taught (docente) --}}
    @if($user->isDocente() && $user->coursesTaught->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Cursos a cargo</h3>
        <div class="space-y-2">
            @foreach($user->coursesTaught as $course)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $course->name }}</p>
                    <p class="text-xs text-gray-400">{{ $course->code }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Enrollments (alumno) --}}
    @if($user->isAlumno() && $user->enrollments->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Matrículas</h3>
        <div class="space-y-2">
            @foreach($user->enrollments as $enrollment)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $enrollment->course->name }}</p>
                    <p class="text-xs text-gray-400">{{ $enrollment->enrolled_at->format('d/m/Y') }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $enrollment->status === 'active' ? 'Activa' : 'Baja' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
