@extends('layouts.app')

@section('title', $program->name)

@section('breadcrumb')
    <a href="{{ route('admin.programs.index') }}" class="hover:text-primary-600">Programas</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $program->code }}</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header card --}}
    <div class="relative rounded-2xl overflow-hidden animate-fade-in-up
                {{ $program->status === 'active' ? 'bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800' : 'bg-gradient-to-br from-gray-500 via-gray-600 to-gray-700' }}">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative px-7 py-6 text-white">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <h1 class="text-2xl font-extrabold tracking-tight">{{ $program->name }}</h1>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $program->status === 'active' ? 'bg-emerald-300 animate-pulse' : 'bg-gray-300' }}"></span>
                            {{ $program->status_label }}
                        </span>
                    </div>
                    <p class="text-white/60 text-sm font-mono">{{ $program->code }}</p>
                    @if($program->description)
                        <p class="text-white/70 text-sm mt-2 max-w-xl">{{ $program->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.programs.edit', $program) }}"
                       class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                    <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" class="inline"
                          onsubmit="return confirm('¿Eliminar este programa? Esta acción no se puede deshacer.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-red-500/20 hover:bg-red-500/40 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-xl border border-red-400/30 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info pills --}}
            <div class="flex flex-wrap gap-3 mt-5 pt-5 border-t border-white/15">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Grado</p>
                        <p class="text-sm font-semibold">{{ $program->degree_type_label }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Duración</p>
                        <p class="text-sm font-semibold">{{ $program->duration_semesters }} sem.{{ $program->has_propedeutic ? ' + propedéutico' : '' }} · {{ $program->duration_years }}</p>
                    </div>
                </div>
                @if($program->total_credits)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Créditos</p>
                        <p class="text-sm font-semibold">{{ $program->total_credits }}</p>
                    </div>
                </div>
                @endif
                @if($program->mentions->count() > 0)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Menciones</p>
                        <p class="text-sm font-semibold">{{ $program->mentions->count() }}</p>
                    </div>
                </div>
                @endif
                @if($program->resolution)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Resolución</p>
                        <p class="text-sm font-semibold">{{ $program->resolution }}</p>
                    </div>
                </div>
                @endif
                @if($program->coordinator)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Coordinador</p>
                        <p class="text-sm font-semibold">{{ $program->coordinator->name }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 animate-fade-in-up delay-1">
        <div class="stat-card stat-card-blue group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Cursos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_courses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-emerald group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Activos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['active_courses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-violet group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Alumnos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-amber group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Docentes</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_teachers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-rose group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Menciones</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['mentions'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-rose-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coordinator card --}}
    @if($program->coordinator)
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Coordinador del programa</h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                    @if($program->coordinator->avatar)
                        <img src="{{ $program->coordinator->avatar_url }}" class="w-full h-full rounded-2xl object-cover">
                    @else
                        <span class="text-white text-lg font-bold">{{ strtoupper(substr($program->coordinator->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900">{{ $program->coordinator->name }}</p>
                    <p class="text-sm text-gray-500">{{ $program->coordinator->email }}</p>
                    @if($program->coordinator->phone)
                    <p class="text-sm text-gray-400 mt-0.5">{{ $program->coordinator->phone }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         PLAN DE ESTUDIOS — CURRÍCULO COMPARTIDO + MENCIONES
    ═══════════════════════════════════════════════════════════ --}}

    {{-- Shared propedéutico --}}
    @if($program->has_propedeutic && $sharedCurriculum->has(0))
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Semestre Propedéutico (Semestre 0)</h2>
                <p class="text-xs text-gray-400">Cursos de preparación académica — Común a todas las menciones</p>
            </div>
        </div>
        <div class="p-6">
            <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl border border-violet-200/50 p-4">
                <div class="grid gap-2">
                    @foreach($sharedCurriculum[0] as $item)
                    <div class="flex items-center gap-3 bg-white rounded-lg px-4 py-3 border border-violet-100">
                        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                            <span class="text-violet-600 text-[10px] font-extrabold">{{ $loop->iteration }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-800">{{ $item->course_name }}</span>
                        @if($item->credits)
                        <span class="ml-auto text-[10px] font-bold text-violet-500 bg-violet-50 px-2 py-0.5 rounded-full shrink-0">{{ $item->credits }} cr.</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Mentions section --}}
    @if($program->mentions->count() > 0)
    <div class="card animate-fade-in-up delay-3">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Menciones y Plan de Estudios</h2>
                    <p class="text-xs text-gray-400">{{ $program->mentions->count() }} {{ $program->mentions->count() === 1 ? 'mención disponible' : 'menciones disponibles' }}</p>
                </div>
            </div>
            <a href="{{ route('admin.programs.mentions.create', $program) }}"
               class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva mención
            </a>
        </div>

        <div class="p-6" x-data="{ openMention: 0 }">
            @php $mentionColors = ['blue', 'emerald', 'amber', 'rose', 'violet', 'cyan']; @endphp

            {{-- Mention tabs --}}
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($program->mentions as $mIdx => $mention)
                @php $mc = $mentionColors[$mIdx % count($mentionColors)]; @endphp
                <button @click="openMention = {{ $mIdx }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border-2 transition-all"
                        :class="openMention === {{ $mIdx }} ? 'border-{{ $mc }}-400 bg-{{ $mc }}-50 text-{{ $mc }}-700 shadow-sm' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                    <span class="w-2 h-2 rounded-full bg-{{ $mc }}-500"></span>
                    {{ $mention->name }}
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full"
                          :class="openMention === {{ $mIdx }} ? 'bg-{{ $mc }}-200 text-{{ $mc }}-700' : 'bg-gray-100 text-gray-400'">
                        {{ $mention->curriculumItems->count() }}
                    </span>
                </button>
                @endforeach
            </div>

            {{-- Mention panels --}}
            @foreach($program->mentions as $mIdx => $mention)
            @php
                $mc = $mentionColors[$mIdx % count($mentionColors)];
                $grouped = $mention->curriculumItems->groupBy('semester_number');
            @endphp
            <div x-show="openMention === {{ $mIdx }}" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Mención: {{ $mention->name }}</h3>
                        @if($mention->description)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $mention->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.programs.mentions.edit', [$program, $mention]) }}"
                           class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-{{ $mc }}-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar
                        </a>
                        <form method="POST" action="{{ route('admin.programs.mentions.destroy', [$program, $mention]) }}" class="inline"
                              onsubmit="return confirm('¿Eliminar esta mención y todo su currículo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-4">
                    @for($sem = ($program->has_propedeutic ? 0 : 1); $sem <= $program->duration_semesters; $sem++)
                    @php
                        $semItems = $grouped->get($sem, collect());
                        $isSharedSem = ($sem === 0 && $semItems->isEmpty() && $sharedCurriculum->has(0));
                        if ($isSharedSem) { $semItems = $sharedCurriculum[0]; }
                        $year = $sem > 0 ? ceil($sem / 2) : 0;
                    @endphp
                    @if($semItems->isNotEmpty())
                    <div class="rounded-xl border {{ $sem === 0 ? 'border-violet-200 bg-violet-50/30' : 'border-gray-200' }}">
                        <div class="flex items-center gap-3 px-4 py-3 {{ $sem === 0 ? 'bg-violet-50' : 'bg-gray-50' }} rounded-t-xl border-b {{ $sem === 0 ? 'border-violet-100' : 'border-gray-100' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-extrabold shrink-0
                                        {{ $sem === 0 ? 'bg-violet-200 text-violet-700' : 'bg-'.$mc.'-100 text-'.$mc.'-600' }}">
                                {{ $sem === 0 ? '0' : $sem }}
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ $sem === 0 ? 'text-violet-800' : 'text-gray-800' }}">
                                    {{ $sem === 0 ? 'Semestre Propedéutico' : 'Semestre ' . $sem }}
                                </p>
                                <p class="text-[10px] {{ $sem === 0 ? 'text-violet-500' : 'text-gray-400' }}">
                                    {{ $sem === 0 ? 'Preparación académica' : 'Año ' . $year . ' — Periodo ' . ($sem % 2 === 1 ? 'I' : 'II') }}
                                    @if($isSharedSem) · <span class="font-semibold">Común</span> @endif
                                </p>
                            </div>
                            <span class="ml-auto text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                {{ $semItems->count() }} {{ $semItems->count() === 1 ? 'curso' : 'cursos' }}
                            </span>
                        </div>
                        <div class="p-3 grid gap-1.5">
                            @foreach($semItems as $item)
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $item->is_elective ? 'bg-amber-50/50 border border-amber-100' : 'bg-white border border-gray-100' }} hover:shadow-sm transition-shadow">
                                <div class="w-6 h-6 rounded-md flex items-center justify-center shrink-0
                                            {{ $item->is_elective ? 'bg-amber-100 text-amber-600' : ($sem === 0 ? 'bg-violet-100 text-violet-600' : 'bg-'.$mc.'-100 text-'.$mc.'-600') }}">
                                    <span class="text-[9px] font-bold">{{ $loop->iteration }}</span>
                                </div>
                                <span class="text-sm {{ $item->is_elective ? 'text-gray-600 italic' : 'font-medium text-gray-800' }}">{{ $item->course_name }}</span>
                                @if($item->is_elective)
                                <span class="text-[9px] font-bold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded-full shrink-0">Electivo</span>
                                @endif
                                @if($item->credits)
                                <span class="ml-auto text-[10px] font-bold text-gray-400 shrink-0">{{ $item->credits }} cr.</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endfor
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- No mentions — show shared curriculum (all semesters) if available --}}
    @php $nonPropShared = $sharedCurriculum->filter(fn($items, $key) => $key != 0); @endphp
    @if($nonPropShared->isNotEmpty())
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Plan de Estudios</h2>
                <p class="text-xs text-gray-400">{{ $sharedCurriculum->flatten()->count() }} cursos organizados por semestre</p>
            </div>
        </div>
        <div class="p-6 space-y-4">
            @foreach($nonPropShared as $sem => $semItems)
            @php $year = ceil($sem / 2); @endphp
            <div class="rounded-xl border border-gray-200">
                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-t-xl border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-extrabold shrink-0 bg-blue-100 text-blue-600">
                        {{ $sem }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Semestre {{ $sem }}</p>
                        <p class="text-[10px] text-gray-400">Año {{ $year }} — Periodo {{ $sem % 2 === 1 ? 'I' : 'II' }}</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $semItems->count() }} {{ $semItems->count() === 1 ? 'curso' : 'cursos' }}
                    </span>
                </div>
                <div class="p-3 grid gap-1.5">
                    @foreach($semItems as $item)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $item->is_elective ? 'bg-amber-50/50 border border-amber-100' : 'bg-white border border-gray-100' }} hover:shadow-sm transition-shadow">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 {{ $item->is_elective ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                            <span class="text-[9px] font-bold">{{ $loop->iteration }}</span>
                        </div>
                        <span class="text-sm {{ $item->is_elective ? 'text-gray-600 italic' : 'font-medium text-gray-800' }}">{{ $item->course_name }}</span>
                        @if($item->is_elective)
                        <span class="text-[9px] font-bold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded-full shrink-0">Electivo</span>
                        @endif
                        @if($item->credits)
                        <span class="ml-auto text-[10px] font-bold text-gray-400 shrink-0">{{ $item->credits }} cr.</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- No mentions and no curriculum --}}
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Menciones del programa</h2>
            </div>
            <a href="{{ route('admin.programs.mentions.create', $program) }}"
               class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva mención
            </a>
        </div>
        <div class="py-12 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">No hay menciones registradas aún</p>
            <p class="text-xs text-gray-400 mt-1">Agrega menciones (especializaciones) con su plan de estudios.</p>
        </div>
    </div>
    @endif
    @endif

    {{-- Courses table --}}
    <div class="card animate-fade-in-up delay-3">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Cursos vinculados al programa</h2>
                    <p class="text-xs text-gray-400">{{ $courses->count() }} cursos · {{ $stats['active_courses'] }} activos</p>
                </div>
            </div>
            <a href="{{ route('admin.courses.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo curso
            </a>
        </div>

        @if($courses->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Curso</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Docente</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Semestre</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Alumnos</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.courses.show', $course) }}" class="group">
                                <p class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors">{{ $course->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $course->code }}</p>
                            </a>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            @if($course->teacher)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-amber-50 flex items-center justify-center shrink-0">
                                    <span class="text-amber-600 text-[9px] font-bold">{{ strtoupper(substr($course->teacher->name, 0, 2)) }}</span>
                                </div>
                                <span class="text-gray-600 text-sm truncate max-w-32">{{ $course->teacher->name }}</span>
                            </div>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 hidden lg:table-cell">
                            @if($course->semesterPeriod)
                            <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">{{ $course->semesterPeriod->name }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-sm font-semibold text-gray-700">{{ $course->students_count }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">No hay cursos asignados a este programa</p>
            <p class="text-xs text-gray-400 mt-1">Crea un curso y selecciona este programa para vincularlo.</p>
        </div>
        @endif
    </div>

    {{-- Duration timeline --}}
    <div class="card animate-fade-in-up delay-4">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Línea de tiempo</h2>
                <p class="text-xs text-gray-400">{{ $program->has_propedeutic ? 'Propedéutico + ' : '' }}{{ $program->duration_semesters }} semestres · {{ $program->duration_years }}</p>
            </div>
        </div>
        <div class="px-6 py-5">
            <div class="flex flex-wrap gap-3">
                @if($program->has_propedeutic)
                @php $propCount = $sharedCurriculum->has(0) ? $sharedCurriculum[0]->count() : 0; @endphp
                <div class="relative bg-gradient-to-b from-violet-50 to-white rounded-xl border border-violet-200 p-4 text-center min-w-[90px] hover:shadow-sm transition-shadow">
                    @if($propCount > 0)
                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-violet-500 rounded-full flex items-center justify-center shadow-sm">
                        <span class="text-[9px] text-white font-bold">{{ $propCount }}</span>
                    </div>
                    @endif
                    <p class="text-lg font-extrabold text-violet-700">0</p>
                    <p class="text-[10px] text-violet-400 font-medium mt-0.5">Propedéutico</p>
                    <div class="w-full h-1 rounded-full mt-2 bg-violet-200"></div>
                </div>
                @endif

                @for($i = 1; $i <= $program->duration_semesters; $i++)
                @php
                    $year = ceil($i / 2);
                    $sem  = $i % 2 === 1 ? 'I' : 'II';
                    $coursesInSem = $courses->filter(fn($c) => $c->cycle == $i)->count();
                @endphp
                <div class="relative bg-gradient-to-b from-gray-50 to-white rounded-xl border {{ $coursesInSem > 0 ? 'border-primary-200' : 'border-gray-200' }} p-4 text-center min-w-[90px] hover:shadow-sm transition-shadow">
                    @if($coursesInSem > 0)
                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center shadow-sm">
                        <span class="text-[9px] text-white font-bold">{{ $coursesInSem }}</span>
                    </div>
                    @endif
                    <p class="text-lg font-extrabold text-gray-800">{{ $i }}</p>
                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">Año {{ $year }} — {{ $sem }}</p>
                    <div class="w-full h-1 rounded-full mt-2 {{ $coursesInSem > 0 ? 'bg-primary-200' : 'bg-gray-100' }}"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

</div>
@endsection
