@extends('layouts.app')

@section('title', $evaluation->title . ' — Evaluación')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-700 to-purple-800 text-white p-8 mb-6 shadow-xl">
        <div class="relative">
            <a href="{{ route('alumno.courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $course->name }}
            </a>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $evaluation->status_badge['class'] }}">
                    {{ $evaluation->status_badge['label'] }}
                </span>
            </div>
            <h1 class="text-2xl font-bold mb-2">{{ $evaluation->title }}</h1>
            <div class="flex flex-wrap gap-4 text-sm text-white/80">
                <span>⏱ {{ $evaluation->time_limit_label }}</span>
                <span>📋 {{ $evaluation->max_attempts }} intento(s) máx.</span>
                <span>🏆 Máx. {{ $evaluation->max_score }} pts</span>
            </div>
            @if($evaluation->opens_at || $evaluation->closes_at)
            <p class="mt-1 text-sm text-white/70">🗓 {{ $evaluation->open_window }}</p>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl">{{ session('info') }}</div>
    @endif

    {{-- Instrucciones --}}
    @if($evaluation->instructions)
    <div class="card p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Instrucciones
        </h3>
        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $evaluation->instructions }}</div>
    </div>
    @endif

    {{-- Archivo adjunto del docente --}}
    @if($evaluation->file_path)
    <div class="card p-4 mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-700">Archivo de la evaluación</p>
            <p class="text-xs text-gray-400">Subido por el docente</p>
        </div>
        <a href="{{ $evaluation->file_url }}" target="_blank"
           class="px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
            Descargar
        </a>
    </div>
    @endif

    {{-- Estado actual y botón de acción --}}
    @php
        $userId       = auth()->id();
        $usedAttempts = $attempts->whereIn('status', ['submitted', 'graded'])->count();
        $canStart     = $evaluation->isOpen() && $usedAttempts < $evaluation->max_attempts;
    @endphp

    @if($active)
    {{-- Intento en progreso --}}
    <div class="card p-6 mb-6 border-l-4 border-amber-400 bg-amber-50">
        <p class="text-sm font-semibold text-amber-800 mb-1">Tienes un intento en progreso</p>
        <p class="text-xs text-amber-700">Iniciado: {{ $active->started_at?->format('d/m/Y H:i') }}</p>
        <a href="{{ route('alumno.evaluations.take', [$course, $evaluation]) }}"
            class="mt-3 inline-flex btn-primary bg-amber-500 hover:bg-amber-600 border-amber-500">
            Continuar evaluación →
        </a>
    </div>
    @elseif($canStart)
    <div class="card p-6 mb-6 text-center bg-violet-50 border border-violet-200">
        <div class="text-4xl mb-3">✍️</div>
        <p class="font-semibold text-violet-800 mb-1">Listo para iniciar</p>
        <p class="text-sm text-violet-600 mb-4">Intentos usados: {{ $usedAttempts }} / {{ $evaluation->max_attempts }}</p>
        <form method="POST" action="{{ route('alumno.evaluations.start', [$course, $evaluation]) }}"
              onsubmit="return confirm('¿Iniciar la evaluación? {{ $evaluation->time_limit ? 'Tendrás ' . $evaluation->time_limit . ' minutos.' : '' }}')">
            @csrf
            <button type="submit" class="btn-primary bg-violet-600 hover:bg-violet-700 border-violet-600">
                Iniciar evaluación
            </button>
        </form>
    </div>
    @elseif(!$evaluation->isOpen())
    <div class="card p-6 mb-6 text-center bg-gray-50 border border-gray-200">
        <div class="text-4xl mb-2">🔒</div>
        <p class="font-semibold text-gray-700">Esta evaluación no está disponible en este momento.</p>
        <p class="text-sm text-gray-500 mt-1">{{ $evaluation->open_window }}</p>
    </div>
    @else
    <div class="card p-6 mb-6 text-center bg-gray-50 border border-gray-200">
        <div class="text-4xl mb-2">✅</div>
        <p class="font-semibold text-gray-700">Has agotado todos tus intentos.</p>
    </div>
    @endif

    {{-- Historial de intentos --}}
    @if($attempts->isNotEmpty())
    <div class="card divide-y divide-gray-100">
        <div class="px-4 py-3 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">Mis intentos</h3>
        </div>
        @foreach($attempts as $attempt)
        <div class="flex items-center justify-between px-4 py-3">
            <div>
                <p class="text-sm font-medium text-gray-800">Intento #{{ $attempt->attempt_number }}</p>
                <p class="text-xs text-gray-500">{{ $attempt->submitted_at?->format('d/m/Y H:i') ?? $attempt->started_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($attempt->score !== null)
                <span class="text-base font-bold {{ $attempt->score_color_class }}">
                    {{ number_format($attempt->score, 1) }}/{{ $evaluation->max_score }}
                </span>
                @endif
                <span class="badge {{ $attempt->status_badge['class'] }} text-xs">{{ $attempt->status_badge['label'] }}</span>
                @if($attempt->isSubmitted() && $evaluation->show_results)
                <a href="{{ route('alumno.evaluations.result', [$course, $evaluation, $attempt]) }}"
                   class="text-xs text-violet-600 hover:underline">Ver →</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
