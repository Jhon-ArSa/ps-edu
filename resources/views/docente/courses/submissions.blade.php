@extends('layouts.app')

@section('title', 'Entregas: ' . $task->title)

@section('breadcrumb')
    <a href="{{ route('docente.courses.index') }}" class="hover:text-primary-600">Mis Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('docente.courses.show', $course) }}" class="hover:text-primary-600">{{ $course->name }}</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Entregas</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- ═══ HEADER: TAREA INFO ═══════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-br from-violet-600 via-violet-700 to-purple-800 rounded-2xl overflow-hidden shadow-lg animate-fade-in-up">
        <div class="relative px-7 py-6 text-white">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

            <a href="{{ route('docente.courses.show', $course) }}"
               class="inline-flex items-center gap-1.5 text-violet-200 hover:text-white text-xs font-medium mb-3 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver al curso
            </a>

            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-xs font-bold text-violet-200 uppercase tracking-widest mb-1">
                        Semana {{ $task->week->number }} · Entregas de tarea
                    </p>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ $task->title }}</h1>
                    @if($task->description)
                    <p class="text-violet-200 text-sm mt-1.5 max-w-xl">{{ $task->description }}</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-5 pt-5 border-t border-white/15">
                @if($task->due_date)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Fecha límite</p>
                        <p class="text-sm font-semibold">{{ $task->due_date->format('d/m/Y H:i') }}
                            @if($task->isExpired()) <span class="text-red-300 text-xs">(Vencida)</span> @endif
                        </p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Puntaje máximo</p>
                        <p class="text-sm font-semibold">{{ $task->max_score }} puntos</p>
                    </div>
                </div>
                @if($task->file_path)
                <a href="{{ Storage::url($task->file_path) }}" target="_blank"
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10 transition-colors">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Archivo adjunto</p>
                        <p class="text-sm font-semibold">Descargar guía</p>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ STATS CARDS ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 animate-fade-in-up delay-1">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400">Matriculados</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['submitted'] + $stats['graded'] }}</p>
                    <p class="text-xs text-gray-400">Entregadas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['graded'] }}</p>
                    <p class="text-xs text-gray-400">Calificadas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_score'] !== null ? number_format($stats['avg_score'], 1) : '—' }}</p>
                    <p class="text-xs text-gray-400">Promedio</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress bar --}}
    @if($stats['total'] > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-4 animate-fade-in-up delay-1">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-600">Progreso de entregas</span>
            <span class="text-xs font-bold text-gray-900">{{ $stats['submitted'] + $stats['graded'] }}/{{ $stats['total'] }}</span>
        </div>
        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden flex">
            @php
                $gradedPct   = $stats['total'] > 0 ? ($stats['graded'] / $stats['total']) * 100 : 0;
                $submittedPct = $stats['total'] > 0 ? ($stats['submitted'] / $stats['total']) * 100 : 0;
            @endphp
            <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $gradedPct }}%"></div>
            <div class="h-full bg-violet-400 transition-all duration-500" style="width: {{ $submittedPct }}%"></div>
        </div>
        <div class="flex items-center gap-4 mt-2">
            <span class="flex items-center gap-1.5 text-[10px] text-gray-500"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Calificadas</span>
            <span class="flex items-center gap-1.5 text-[10px] text-gray-500"><span class="w-2 h-2 rounded-full bg-violet-400"></span> Por calificar</span>
            <span class="flex items-center gap-1.5 text-[10px] text-gray-500"><span class="w-2 h-2 rounded-full bg-gray-200"></span> Sin entregar</span>
        </div>
    </div>
    @endif

    {{-- ═══ SUBMISSIONS LIST ═════════════════════════════════════════════════ --}}
    @if($submissions->isNotEmpty())
    <div class="space-y-3 animate-fade-in-up delay-2">
        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Entregas recibidas ({{ $submissions->count() }})
        </h2>

        @foreach($submissions as $submission)
        @php $badge = $submission->status_badge; @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm"
             x-data="{ grading: {{ $submission->isGraded() ? 'false' : 'false' }} }">

            <div class="flex items-center gap-4 px-5 py-4">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    {{ $submission->isGraded() ? 'bg-emerald-100' : 'bg-violet-100' }}">
                    @if($submission->student->avatar)
                        <img src="{{ $submission->student->avatar_url }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-sm font-bold {{ $submission->isGraded() ? 'text-emerald-600' : 'text-violet-600' }}">
                            {{ strtoupper(substr($submission->student->name, 0, 2)) }}
                        </span>
                    @endif
                </div>

                {{-- Student info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ $submission->student->name }}</p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-xs text-gray-400">{{ $submission->student->email }}</span>
                        @if($submission->student->alumnoProfile?->code)
                        <span class="text-xs text-gray-300">·</span>
                        <span class="text-xs font-mono text-gray-400">{{ $submission->student->alumnoProfile->code }}</span>
                        @endif
                    </div>
                </div>

                {{-- Status + date --}}
                <div class="text-right shrink-0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $submission->submitted_at?->format('d/m/Y H:i') }}
                    </p>
                </div>

                {{-- Score if graded --}}
                @if($submission->isGraded())
                <div class="text-center px-3 py-1.5 bg-gray-50 rounded-xl shrink-0">
                    <p class="text-lg font-bold {{ $submission->score_color }}">{{ $submission->score }}</p>
                    <p class="text-[10px] text-gray-400">/{{ $task->max_score }}</p>
                </div>
                @endif
            </div>

            {{-- Submission content --}}
            <div class="px-5 pb-4 space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    @if($submission->file_path)
                    <a href="{{ $submission->file_url }}" target="_blank"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ $submission->original_filename ?? 'Descargar archivo' }}
                    </a>
                    @endif
                    @if($submission->comments)
                    <div class="flex-1 min-w-0">
                        <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100 text-xs text-gray-600">
                            <p class="font-medium text-gray-500 mb-0.5">Comentario del alumno:</p>
                            {{ $submission->comments }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Feedback (if graded) --}}
                @if($submission->isGraded() && $submission->feedback)
                <div class="px-3 py-2 bg-emerald-50 rounded-lg border border-emerald-100">
                    <p class="text-xs font-semibold text-emerald-700 mb-0.5">Retroalimentación:</p>
                    <p class="text-xs text-emerald-800">{{ $submission->feedback }}</p>
                </div>
                @endif

                {{-- Grade form --}}
                @if(!$submission->isGraded())
                <div x-show="!grading" class="pt-1">
                    <button @click="grading = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Calificar entrega
                    </button>
                </div>
                @endif

                <form x-show="grading" x-cloak method="POST"
                      action="{{ route('docente.courses.submissions.grade', [$course, $task, $submission]) }}"
                      class="pt-2 space-y-3 border-t border-gray-100">
                    @csrf @method('PATCH')

                    <div class="flex items-end gap-3">
                        <div class="w-32">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Puntaje</label>
                            <div class="relative">
                                <input type="number" name="score" step="0.5" min="0" max="{{ $task->max_score }}"
                                       value="{{ old('score', $submission->score) }}"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-violet-400"
                                       required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">/{{ $task->max_score }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Retroalimentación <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <textarea name="feedback" rows="2" placeholder="Comentario para el alumno…"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-violet-400 resize-none">{{ old('feedback', $submission->feedback) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar calificación
                        </button>
                        <button type="button" @click="grading = false"
                                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>

                {{-- Re-grade option --}}
                @if($submission->isGraded())
                <div class="pt-1">
                    <button @click="grading = true"
                            class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-violet-600 font-medium transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Recalificar
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══ PENDING STUDENTS ═════════════════════════════════════════════════ --}}
    @if($pendingStudents->isNotEmpty())
    <div class="animate-fade-in-up delay-3">
        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Sin entregar ({{ $pendingStudents->count() }})
        </h2>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-50">
                @foreach($pendingStudents as $student)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/60 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-gray-400">{{ strtoupper(substr($student->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700">{{ $student->name }}</p>
                        <p class="text-xs text-gray-400">{{ $student->email }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-medium">Sin entrega</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Empty state --}}
    @if($submissions->isEmpty() && $pendingStudents->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">No hay alumnos matriculados</h3>
        <p class="text-xs text-gray-400">Matricula alumnos en el curso para que puedan entregar tareas.</p>
    </div>
    @endif

</div>
@endsection
