@extends('layouts.app')

@section('title', 'Resultado — ' . $evaluation->title)

@section('content')
<div class="max-w-3xl mx-auto animate-fade-in-up">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-700 to-purple-800 text-white p-8 mb-6 shadow-xl text-center">
        <div class="text-5xl mb-3">
            @if($attempt->score >= $evaluation->max_score * 0.7) 🎉
            @elseif($attempt->score >= $evaluation->max_score * 0.5) 💪
            @else 📚
            @endif
        </div>
        <h1 class="text-2xl font-bold mb-1">{{ $evaluation->title }}</h1>
        <p class="text-white/70 text-sm mb-4">{{ $course->name }}</p>

        <div class="inline-flex items-baseline gap-2 bg-white rounded-2xl px-8 py-4 shadow-lg">
            <span class="text-5xl font-black {{ $attempt->score_color_class }}">
                {{ number_format($attempt->score, 1) }}
            </span>
            <span class="text-xl text-gray-400">/ {{ $evaluation->max_score }}</span>
        </div>

        <p class="text-white/60 text-sm mt-3">
            Entregado: {{ $attempt->submitted_at?->format('d/m/Y H:i') }}
            &bull; Intento #{{ $attempt->attempt_number }}
        </p>
    </div>

    {{-- Acción --}}
    <div class="flex justify-between mb-6">
        <a href="{{ route('alumno.evaluations.show', [$course, $evaluation]) }}" class="btn-secondary">
            ← Volver
        </a>
        <a href="{{ route('alumno.courses.show', $course) }}" class="btn-ghost">
            Ver curso
        </a>
    </div>

    {{-- Archivos adjuntos --}}
    @if($evaluation->file_path || $attempt->file_path)
    <div class="card p-4 mb-6 space-y-3">
        @if($evaluation->file_path)
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-600">Archivo del docente</p>
            </div>
            <a href="{{ $evaluation->file_url }}" target="_blank"
               class="px-2.5 py-1 bg-violet-100 hover:bg-violet-200 text-violet-700 text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                Descargar
            </a>
        </div>
        @endif
        @if($attempt->file_path)
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-600">Mi archivo enviado</p>
                <p class="text-xs text-gray-400 truncate">{{ $attempt->original_filename }}</p>
            </div>
            <a href="{{ $attempt->file_url }}" target="_blank"
               class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                Descargar
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- Detalle de preguntas (solo si show_results) --}}
    @if($evaluation->show_results)
    <div class="space-y-4">
        <h2 class="text-base font-semibold text-gray-700">Revisión detallada</h2>

        @foreach($evaluation->questions as $question)
        @php $answer = $answerMap->get($question->id); @endphp
        <div class="card p-5 {{ $answer?->is_correct ? 'border-l-4 border-emerald-400' : ($question->type === 'short' ? 'border-l-4 border-violet-300' : 'border-l-4 border-red-300') }}">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full {{ $answer?->is_correct ? 'bg-emerald-100 text-emerald-700' : ($question->type === 'short' ? 'bg-violet-100 text-violet-700' : 'bg-red-100 text-red-700') }} text-xs font-bold flex items-center justify-center">
                        {{ $loop->iteration }}
                    </span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $question->type_badge_class }}">{{ $question->type_label }}</span>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-sm font-bold {{ $answer?->score !== null ? ($answer->is_correct ? 'text-emerald-600' : 'text-red-600') : 'text-gray-400' }}">
                        {{ $answer?->score !== null ? number_format($answer->score, 1) : '—' }} / {{ $question->points }}
                    </span>
                </div>
            </div>

            <p class="text-sm font-medium text-gray-800 mb-3">{{ $question->text }}</p>

            @if($question->type === 'short')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                    <p class="text-xs text-gray-500 mb-1 font-medium">Tu respuesta:</p>
                    <p class="whitespace-pre-line">{{ $answer?->text_answer ?: '(Sin respuesta)' }}</p>
                </div>
            @else
                <div class="space-y-1.5">
                    @foreach($question->options as $opt)
                    @php
                        $selected = in_array($opt->id, $answer?->selected_options ?? []);
                    @endphp
                    <div class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg
                        {{ $opt->is_correct ? 'bg-emerald-50 border border-emerald-200' : ($selected && !$opt->is_correct ? 'bg-red-50 border border-red-200' : 'bg-gray-50') }}">
                        @if($opt->is_correct)
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($selected)
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <span class="w-4 h-4 flex-shrink-0"></span>
                        @endif
                        <span class="{{ $opt->is_correct ? 'text-emerald-700 font-medium' : ($selected ? 'text-red-600' : 'text-gray-600') }}">
                            {{ $opt->text }}
                        </span>
                        @if($selected && !$opt->is_correct)
                        <span class="text-xs text-red-500 ml-auto">(Tu respuesta)</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif

            @if($question->explanation)
            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                <span class="font-semibold">Explicación: </span>{{ $question->explanation }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="card p-8 text-center text-gray-500">
        <div class="text-3xl mb-2">🔍</div>
        <p class="font-medium">La revisión detallada no está disponible para esta evaluación.</p>
    </div>
    @endif

</div>
@endsection
