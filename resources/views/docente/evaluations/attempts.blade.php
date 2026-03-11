@extends('layouts.app')

@section('title', 'Intentos — ' . $evaluation->title)

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in-up">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-700 to-purple-800 text-white p-8 mb-6 shadow-xl">
        <div class="relative">
            <a href="{{ route('docente.evaluations.show', [$course, $evaluation]) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $evaluation->title }}
            </a>
            <h1 class="text-2xl font-bold mb-1">Intentos de Alumnos</h1>
            <p class="text-white/70 text-sm">{{ $course->name }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Matriculados',  'value' => $stats['total'],         'color' => 'blue'],
            ['label' => 'Entregadas',    'value' => $stats['submitted'],      'color' => 'violet'],
            ['label' => 'Calificadas',   'value' => $stats['graded'],         'color' => 'emerald'],
            ['label' => 'Por revisar',   'value' => $stats['pending_grade'],  'color' => 'amber'],
        ] as $s)
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>
    @if($stats['avg_score'])
    <div class="card p-4 mb-6 flex items-center gap-3">
        <span class="text-sm text-gray-600">Promedio de la clase:</span>
        <span class="text-2xl font-bold {{ $stats['avg_score'] >= 14 ? 'text-emerald-600' : ($stats['avg_score'] >= 11 ? 'text-amber-600' : 'text-red-600') }}">
            {{ number_format($stats['avg_score'], 1) }} / {{ $evaluation->max_score }}
        </span>
    </div>
    @endif

    {{-- Intentos entregados --}}
    <div class="space-y-3">
        @forelse($attempts as $attempt)
        <div x-data="{ reviewing: false }" class="card overflow-hidden">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-violet-100 text-violet-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($attempt->student->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $attempt->student->name }}</p>
                        <p class="text-xs text-gray-500">{{ $attempt->student->alumnoProfile?->student_code ?? $attempt->student->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                    @if($attempt->submitted_at)
                    <span class="text-xs text-gray-400 hidden sm:block">{{ $attempt->submitted_at->format('d/m/Y H:i') }}</span>
                    @endif
                    @if($attempt->score !== null)
                    <span class="text-base font-bold {{ $attempt->score_color_class }}">{{ number_format($attempt->score, 1) }}/{{ $evaluation->max_score }}</span>
                    @endif
                    <span class="badge {{ $attempt->status_badge['class'] }} text-xs">{{ $attempt->status_badge['label'] }}</span>
                    @if($attempt->file_path)
                    <a href="{{ $attempt->file_url }}" target="_blank"
                       class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-medium rounded-lg transition-colors"
                       title="{{ $attempt->original_filename }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Archivo
                    </a>
                    @endif
                    @if($attempt->status === 'submitted' && $attempt->hasUngradedShortAnswers())
                    <button @click="reviewing = !reviewing" class="btn-sm bg-amber-500 hover:bg-amber-600 text-white">
                        Calificar
                    </button>
                    @endif
                </div>
            </div>

            {{-- Panel de calificación de respuestas cortas --}}
            <div x-show="reviewing" x-transition class="border-t border-gray-100">
                <form method="POST" action="{{ route('docente.evaluations.attempts.grade', [$course, $evaluation, $attempt]) }}" class="p-5 space-y-4">
                    @csrf @method('PATCH')
                    <h4 class="font-semibold text-gray-800 text-sm">Calificar respuestas cortas</h4>
                    @foreach($attempt->answers as $answer)
                        @if($answer->question->type === 'short')
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <p class="text-sm font-medium text-gray-700">{{ $answer->question->text }}</p>
                            <p class="text-xs text-gray-500">Puntaje máximo: {{ $answer->question->points }} pts</p>
                            <div class="bg-white border border-gray-200 rounded-lg p-3 text-sm text-gray-800 whitespace-pre-line">
                                {{ $answer->text_answer ?: '(Sin respuesta)' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-600">Puntaje:</label>
                                <input type="number"
                                    name="scores[{{ $answer->id }}]"
                                    value="{{ $answer->score }}"
                                    min="0" max="{{ $answer->question->points }}" step="0.5"
                                    class="form-input w-20 text-sm">
                                <span class="text-xs text-gray-400">/ {{ $answer->question->points }}</span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary">Guardar calificación</button>
                        <button type="button" @click="reviewing = false" class="btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="card p-10 text-center text-gray-400">
            <div class="text-4xl mb-2">📭</div>
            <p class="font-medium">No hay intentos entregados aún.</p>
        </div>
        @endforelse
    </div>

    {{-- Alumnos sin entregar --}}
    @if($pendingStudents->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Sin entregar ({{ $pendingStudents->count() }})</h3>
        <div class="card divide-y divide-gray-100">
            @foreach($pendingStudents as $student)
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 font-semibold text-sm flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ $student->name }}</p>
                    <p class="text-xs text-gray-400">{{ $student->alumnoProfile?->student_code ?? $student->email }}</p>
                </div>
                <span class="badge bg-gray-100 text-gray-500 ml-auto text-xs">Sin entregar</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
