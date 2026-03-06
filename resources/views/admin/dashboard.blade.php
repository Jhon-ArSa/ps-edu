@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Dashboard</span>
@endsection

@section('content')
<div class="space-y-6 max-w-7xl">

    {{-- Welcome banner --}}
    <div class="bg-gradient-to-r from-primary-700 to-primary-900 rounded-2xl px-6 py-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primary-200 text-sm font-medium">Bienvenido de vuelta,</p>
                <h1 class="text-2xl font-bold mt-0.5">{{ auth()->user()->name }}</h1>
                <p class="text-primary-300 text-sm mt-1">
                    {{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl border border-white/20">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alumnos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['role' => 'alumno']) }}"
               class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium mt-3">
                Ver alumnos
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Docentes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_teachers'] }}</p>
                </div>
                <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['role' => 'docente']) }}"
               class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium mt-3">
                Ver docentes
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cursos Activos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_courses'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">de {{ $stats['total_courses'] }} totales</p>
                </div>
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.courses.index') }}"
               class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium mt-3">
                Ver cursos
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Matrículas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_enrollments'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">activas</p>
                </div>
                <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.enrollments.index') }}"
               class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium mt-3">
                Ver matrículas
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

    </div>

    {{-- Módulos de Acceso Rápido (estilo UNCP) --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Módulos del Sistema</h2>
            <p class="text-xs text-gray-500 mt-0.5">Acceso rápido a todas las funcionalidades</p>
        </div>
        <div class="p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            {{-- Usuarios --}}
            <a href="{{ route('admin.users.index') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Usuarios</span>
            </a>

            {{-- Cursos --}}
            <a href="{{ route('admin.courses.index') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-emerald-100 group-hover:bg-emerald-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Cursos</span>
            </a>

            {{-- Matrículas --}}
            <a href="{{ route('admin.enrollments.index') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-amber-100 group-hover:bg-amber-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Matrículas</span>
            </a>

            {{-- Comunicados --}}
            <a href="{{ route('admin.announcements.index') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-orange-100 group-hover:bg-orange-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Comunicados</span>
            </a>

            {{-- Configuración --}}
            <a href="{{ route('admin.settings') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-gray-100 group-hover:bg-gray-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Configuración</span>
            </a>

            {{-- Nuevo Usuario --}}
            <a href="{{ route('admin.users.create') }}"
               class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all text-center">
                <div class="w-12 h-12 bg-primary-100 group-hover:bg-primary-200 rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-700 leading-tight">Nuevo Usuario</span>
            </a>

        </div>
    </div>

    {{-- Bottom: Recent Users + Recent Announcements --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Users --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Usuarios Recientes</h3>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="text-xs text-primary-600 hover:text-primary-800 font-medium hover:underline">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($stats['recent_users'] as $user)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-primary-100 overflow-hidden shrink-0">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-primary-600 text-sm font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                        {{ $user->role === 'admin'   ? 'bg-red-100 text-red-700' :
                           ($user->role === 'docente' ? 'bg-violet-100 text-violet-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                    </svg>
                    <p class="text-sm text-gray-400">Sin usuarios registrados</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Announcements --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Comunicados Recientes</h3>
                </div>
                <a href="{{ route('admin.announcements.index') }}"
                   class="text-xs text-primary-600 hover:text-primary-800 font-medium hover:underline">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($stats['recent_announcements'] as $ann)
                <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $ann->title }}</p>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 whitespace-nowrap shrink-0">
                            {{ $ann->target_role === 'all' ? 'Todos' : ucfirst($ann->target_role) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ optional($ann->published_at)->diffForHumans() ?? 'No publicado' }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-gray-400">Sin comunicados publicados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
