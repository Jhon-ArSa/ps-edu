@extends('layouts.app')

@section('title', 'Programas de Posgrado')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Programas de Posgrado</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Programas de Posgrado</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gestiona los programas académicos de la facultad</p>
        </div>
        <a href="{{ route('admin.programs.create') }}"
           class="btn-primary inline-flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo programa
        </a>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up delay-1">
        <div class="stat-card stat-card-blue group">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">programas registrados</p>
            </div>
        </div>
        <div class="stat-card stat-card-emerald group">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Activos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">en oferta actual</p>
            </div>
        </div>
        <div class="stat-card stat-card-amber group">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Inactivos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">suspendidos</p>
            </div>
        </div>
        <div class="stat-card stat-card-violet group">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Cursos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['courses'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">vinculados</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card animate-fade-in-up delay-2">
        <form method="GET" action="{{ route('admin.programs.index') }}" class="p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m21 21-4.35-4.35"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o código..."
                               class="form-input pl-10 w-full">
                    </div>
                </div>
                <select name="degree_type" class="form-select w-full sm:w-44">
                    <option value="">Todos los tipos</option>
                    <option value="maestria" {{ request('degree_type') === 'maestria' ? 'selected' : '' }}>Maestría</option>
                    <option value="doctorado" {{ request('degree_type') === 'doctorado' ? 'selected' : '' }}>Doctorado</option>
                    <option value="segunda_especialidad" {{ request('degree_type') === 'segunda_especialidad' ? 'selected' : '' }}>Segunda Especialidad</option>
                    <option value="diplomado" {{ request('degree_type') === 'diplomado' ? 'selected' : '' }}>Diplomado</option>
                </select>
                <select name="status" class="form-select w-full sm:w-36">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
                <button type="submit" class="btn-primary btn-sm">Filtrar</button>
                @if(request()->hasAny(['search', 'status', 'degree_type']))
                    <a href="{{ route('admin.programs.index') }}" class="btn-ghost btn-sm">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Programs grid --}}
    @if($programs->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 animate-fade-in-up delay-3">
        @foreach($programs as $program)
        <a href="{{ route('admin.programs.show', $program) }}"
           class="card group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
            {{-- Color accent bar --}}
            <div class="h-1.5 {{ $program->status === 'active' ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-gradient-to-r from-gray-300 to-gray-400' }}"></div>

            <div class="p-5">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-md font-mono">{{ $program->code }}</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                                {{ $program->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $program->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $program->status_label }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary-700 transition-colors leading-snug">{{ $program->name }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center shrink-0 group-hover:from-primary-100 group-hover:to-primary-200 transition-colors">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v7"/></svg>
                    </div>
                </div>

                {{-- Degree type badge --}}
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        {{ $program->degree_type_label }}
                    </span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">{{ $program->duration_years }}</span>
                    @if($program->total_credits)
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">{{ $program->total_credits }} créditos</span>
                    @endif
                </div>

                {{-- Description --}}
                @if($program->description)
                <p class="text-xs text-gray-400 line-clamp-2 mb-4">{{ $program->description }}</p>
                @endif

                {{-- Metrics --}}
                <div class="flex items-center gap-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="text-xs font-semibold text-gray-600">{{ $program->courses_count }}</span>
                        <span class="text-[10px] text-gray-400">cursos</span>
                    </div>
                    @if($program->active_courses_count)
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-xs font-semibold text-gray-600">{{ $program->active_courses_count }}</span>
                        <span class="text-[10px] text-gray-400">activos</span>
                    </div>
                    @endif
                    @if($program->mentions_count)
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="text-xs font-semibold text-gray-600">{{ $program->mentions_count }}</span>
                        <span class="text-[10px] text-gray-400">{{ $program->mentions_count === 1 ? 'mención' : 'menciones' }}</span>
                    </div>
                    @endif
                    @if($program->coordinator)
                    <div class="flex items-center gap-1.5 ml-auto">
                        <div class="w-5 h-5 rounded-md bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
                            <span class="text-amber-600 text-[8px] font-bold">{{ strtoupper(substr($program->coordinator->name, 0, 2)) }}</span>
                        </div>
                        <span class="text-[10px] text-gray-400 truncate max-w-24">{{ explode(' ', $program->coordinator->name)[0] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="animate-fade-in-up delay-4">
        {{ $programs->links() }}
    </div>
    @else
    <div class="card animate-fade-in-up delay-3">
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v7"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No se encontraron programas</p>
            <p class="text-xs text-gray-400 mt-1">Crea un nuevo programa de posgrado para empezar.</p>
            <a href="{{ route('admin.programs.create') }}" class="btn-primary btn-sm mt-4 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo programa
            </a>
        </div>
    </div>
    @endif

</div>
@endsection
