@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-2xl px-7 py-6 text-white shadow-xl shadow-primary-500/10 overflow-hidden animate-fade-in-up">
        {{-- Decorative --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-56 h-56 bg-primary-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-primary-200 text-sm font-medium">Bienvenido de vuelta,</p>
                <h1 class="text-2xl font-extrabold mt-1 tracking-tight">{{ auth()->user()->name }}</h1>
                <p class="text-primary-200/70 text-sm mt-1.5">
                    {{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 shadow-lg">
                <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Active Semester Banner --}}
    @if($activeSemester)
    <div class="relative bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 rounded-xl p-5 text-white overflow-hidden animate-fade-in-up delay-1">
        <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold">{{ $activeSemester->name }}</h3>
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> En curso
                        </span>
                    </div>
                    <p class="text-white/65 text-xs mt-0.5">{{ $activeSemester->date_range }} · {{ $activeSemester->progress_percent }}% completado</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-32 bg-white/20 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all" style="width: {{ $activeSemester->progress_percent }}%"></div>
                </div>
                <a href="{{ route('admin.semesters.show', $activeSemester) }}" class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold px-3 py-2 rounded-lg border border-white/20 transition-all whitespace-nowrap">
                    Ver semestre
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="stat-card stat-card-blue group animate-fade-in-up delay-1">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Alumnos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25 group-hover:shadow-blue-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.users.index', ['role' => 'alumno']) }}"
                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-semibold mt-3 transition-colors group/link">
                    Ver alumnos
                    <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="stat-card stat-card-violet group animate-fade-in-up delay-2">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Docentes</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_teachers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25 group-hover:shadow-violet-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.users.index', ['role' => 'docente']) }}"
                   class="inline-flex items-center gap-1 text-xs text-violet-600 hover:text-violet-700 font-semibold mt-3 transition-colors group/link">
                    Ver docentes
                    <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="stat-card stat-card-emerald group animate-fade-in-up delay-3">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Cursos Activos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['active_courses'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">de {{ $stats['total_courses'] }} totales</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 group-hover:shadow-emerald-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.courses.index') }}"
                   class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-semibold mt-3 transition-colors group/link">
                    Ver cursos
                    <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="stat-card stat-card-amber group animate-fade-in-up delay-4">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Matrículas</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_enrollments'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">activas</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25 group-hover:shadow-amber-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.enrollments.index') }}"
                   class="inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-700 font-semibold mt-3 transition-colors group/link">
                    Ver matrículas
                    <svg class="w-3 h-3 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>

    {{-- Módulos de Acceso Rápido --}}
    <div class="card animate-fade-in-up delay-5">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Módulos del Sistema</h2>
                <p class="text-xs text-gray-400 mt-0.5">Acceso rápido a todas las funcionalidades</p>
            </div>
        </div>
        <div class="p-4 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">

            {{-- Usuarios --}}
            <a href="{{ route('admin.users.index') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:shadow-lg group-hover:shadow-blue-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Usuarios</span>
            </a>

            {{-- Cursos --}}
            <a href="{{ route('admin.courses.index') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-md shadow-emerald-500/20 group-hover:shadow-lg group-hover:shadow-emerald-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Cursos</span>
            </a>

            {{-- Matrículas --}}
            <a href="{{ route('admin.enrollments.index') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-md shadow-amber-500/20 group-hover:shadow-lg group-hover:shadow-amber-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Matrículas</span>
            </a>

            {{-- Comunicados --}}
            <a href="{{ route('admin.announcements.index') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-md shadow-orange-500/20 group-hover:shadow-lg group-hover:shadow-orange-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Comunicados</span>
            </a>

            {{-- Configuración --}}
            <a href="{{ route('admin.settings') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center shadow-md shadow-gray-500/20 group-hover:shadow-lg group-hover:shadow-gray-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Configuración</span>
            </a>

            {{-- Semestres --}}
            <a href="{{ route('admin.semesters.index') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-md shadow-teal-500/20 group-hover:shadow-lg group-hover:shadow-teal-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Semestres</span>
            </a>

            {{-- Nuevo Usuario --}}
            <a href="{{ route('admin.users.create') }}"
               class="group module-card">
                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-md shadow-primary-500/20 group-hover:shadow-lg group-hover:shadow-primary-500/30 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <span class="relative z-10 text-xs font-semibold text-gray-600 group-hover:text-primary-700 leading-tight transition-colors">Nuevo Usuario</span>
            </a>

        </div>
    </div>

    {{-- Bottom: Recent Users + Recent Announcements --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up delay-6">

        {{-- Recent Users --}}
        <div class="card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Usuarios Recientes</h3>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($stats['recent_users'] as $user)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 overflow-hidden shrink-0">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-primary-600 text-sm font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="badge-{{ $user->role === 'admin' ? 'red' : ($user->role === 'docente' ? 'blue' : 'green') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Sin usuarios registrados</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Announcements --}}
        <div class="card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Comunicados Recientes</h3>
                </div>
                <a href="{{ route('admin.announcements.index') }}"
                   class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($stats['recent_announcements'] as $ann)
                <div class="px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $ann->title }}</p>
                        <span class="badge-gray whitespace-nowrap shrink-0">
                            {{ $ann->target_role === 'all' ? 'Todos' : ucfirst($ann->target_role) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ optional($ann->published_at)->diffForHumans() ?? 'No publicado' }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Sin comunicados publicados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
