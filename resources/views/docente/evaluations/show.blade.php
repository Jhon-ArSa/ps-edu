@extends('layouts.app')

@section('title', 'Gestionar Evaluación — ' . $evaluation->title)

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in-up">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-700 to-purple-800 text-white p-8 mb-6 shadow-xl">
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('docente.courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $course->name }}
                </a>
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $evaluation->status_badge['class'] }}">
                        {{ $evaluation->status_badge['label'] }}
                    </span>
                    <span class="text-white/60 text-sm">{{ $evaluation->week->title }}</span>
                </div>
                <h1 class="text-2xl font-bold">{{ $evaluation->title }}</h1>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-white/80">
                    <span>⏱ {{ $evaluation->time_limit_label }}</span>
                    <span>📋 Hasta {{ $evaluation->max_attempts }} intento(s)</span>
                    <span>🗓 {{ $evaluation->open_window }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($evaluation->status !== 'draft')
                <a href="{{ route('docente.evaluations.attempts.index', [$course, $evaluation]) }}"
                   class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                    Ver intentos ({{ $stats['submitted'] }})
                </a>
                @endif
                @if($evaluation->status === 'draft')
                <button type="button"
                    onclick="toggleEvaluationStatus('draft')"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 rounded-lg text-sm font-medium transition-colors">
                    🚀 Publicar
                </button>
                @elseif($evaluation->status === 'published')
                <button type="button"
                    onclick="toggleEvaluationStatus('published')"
                    class="px-4 py-2 bg-red-500 hover:bg-red-400 rounded-lg text-sm font-medium transition-colors">
                    🔒 Cerrar
                </button>
                @elseif($evaluation->status === 'closed')
                <button type="button"
                    onclick="toggleEvaluationStatus('closed')"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-400 rounded-lg text-sm font-medium transition-colors">
                    🔓 Reabrir
                </button>
                @endif
                <form id="toggle-status-form" method="POST" action="{{ route('docente.evaluations.toggle', [$course, $evaluation]) }}" class="hidden">
                    @csrf @method('PATCH')
                </form>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
        <ul class="list-disc list-inside space-y-1 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Editar Descripción/Instrucciones --}}
    @if($evaluation->status === 'draft')
    <div class="card p-5 mb-6" x-data="{ editing: false }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descripción / Instrucciones
            </h3>
            <button @click="editing = !editing" type="button" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                <span x-show="!editing">✏️ Editar</span>
                <span x-show="editing">❌ Cancelar</span>
            </button>
        </div>
        
        <div x-show="!editing">
            @if($evaluation->instructions)
            <div class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded-lg">{{ $evaluation->instructions }}</div>
            @else
            <p class="text-sm text-gray-400 italic">Sin descripción. Puedes agregar instrucciones o un enlace a Google Forms.</p>
            @endif
        </div>

        <div x-show="editing" x-cloak>
            <form method="POST" action="{{ route('docente.evaluations.update', [$course, $evaluation]) }}">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $evaluation->title }}">
                <input type="hidden" name="max_attempts" value="{{ $evaluation->max_attempts }}">
                <textarea name="instructions" rows="4" class="form-input w-full resize-none" placeholder="Escribe las instrucciones o pega un enlace a Google Forms...">{{ $evaluation->instructions }}</textarea>
                <div class="flex justify-end gap-2 mt-2">
                    <button type="button" @click="editing = false" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    {{-- Solo mostrar si no es borrador --}}
    @if($evaluation->instructions)
    <div class="card p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descripción / Instrucciones
        </h3>
        <div class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded-lg">{{ $evaluation->instructions }}</div>
    </div>
    @endif
    @endif

    {{-- Archivo adjunto de la evaluación --}}
    <div class="card p-5 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Archivo adjunto <span class="text-xs text-gray-400 font-normal">(opcional)</span>
                </h3>
                @if($evaluation->file_path)
                <a href="{{ $evaluation->file_url }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-sm text-violet-600 hover:text-violet-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Descargar archivo adjunto
                </a>
                @else
                <p class="text-sm text-gray-400">Sin archivo adjunto.</p>
                @endif
            </div>
            @if($evaluation->status === 'draft')
            <form method="POST" action="{{ route('docente.evaluations.file', [$course, $evaluation]) }}"
                  enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.jpg,.jpeg,.png"
                       class="text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <button type="submit" class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                    {{ $evaluation->file_path ? 'Reemplazar' : 'Subir' }}
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @if($evaluation->type === 'quiz')
            @foreach([
                ['label' => 'Preguntas',         'value' => $stats['questions'],    'color' => 'blue'],
                ['label' => 'Puntaje total',      'value' => $stats['total_points'], 'color' => 'violet'],
                ['label' => 'Entregadas',         'value' => $stats['submitted'],    'color' => 'emerald'],
                ['label' => 'Por revisar',        'value' => $stats['pending_review'],'color' => 'amber'],
            ] as $s)
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</div>
            </div>
            @endforeach
        @else
            @foreach([
                ['label' => 'Tipo',               'value' => 'Documento',            'color' => 'violet'],
                ['label' => 'Puntaje máximo',     'value' => $evaluation->max_score, 'color' => 'blue'],
                ['label' => 'Visualizaciones',    'value' => $stats['submitted'],    'color' => 'emerald'],
                ['label' => 'Estado',             'value' => $evaluation->status_badge['label'], 'color' => 'amber'],
            ] as $s)
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Sección de preguntas (solo para evaluaciones tipo quiz) --}}
    @if($evaluation->type === 'quiz')
    <div x-data="questionBuilder()" class="space-y-4">

        {{-- Lista de preguntas existentes --}}
        @forelse($evaluation->questions as $q)
        <div x-data="{ open: false }" class="card overflow-hidden">
            <div class="flex items-center justify-between p-4 cursor-pointer select-none" @click="open = !open">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-7 h-7 rounded-full bg-violet-100 text-violet-700 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $loop->iteration }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $q->type_badge_class }}">{{ $q->type_label }}</span>
                    <span class="text-sm font-medium text-gray-800 truncate">{{ $q->text }}</span>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                    <span class="text-xs font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">{{ $q->points }} pts</span>
                    @if($evaluation->status === 'draft')
                    <button type="button"
                        x-on:click.stop="removeQuestion({{ $q->id }}, $el.closest('[x-data]'))"
                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @endif
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div x-show="open" x-transition class="border-t border-gray-100 p-4 bg-gray-50 space-y-3">
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $q->text }}</p>
                @if($q->explanation)
                <div class="text-xs text-amber-700 bg-amber-50 p-2 rounded-lg"><strong>Explicación:</strong> {{ $q->explanation }}</div>
                @endif
                @unless($q->type === 'short')
                <ul class="space-y-1">
                    @foreach($q->options as $opt)
                    <li class="flex items-center gap-2 text-sm">
                        <span class="w-4 h-4 rounded-full flex-shrink-0 {{ $opt->is_correct ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                        <span class="{{ $opt->is_correct ? 'text-emerald-700 font-medium' : 'text-gray-600' }}">{{ $opt->text }}</span>
                        @if($opt->is_correct)<span class="text-xs text-emerald-600 font-medium">✓ Correcta</span>@endif
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-xs text-violet-600 italic">Pregunta de respuesta abierta — revisión manual por docente.</p>
                @endunless
            </div>
        </div>
        @empty
        <div class="card p-10 text-center text-gray-400">
            <div class="text-4xl mb-2">📝</div>
            <p class="font-medium">Sin preguntas aún</p>
            <p class="text-sm mt-1">Agrega preguntas usando el formulario de abajo.</p>
        </div>
        @endforelse

        {{-- Formulario para agregar pregunta (solo si draft) --}}
        @if($evaluation->status === 'draft')
        <div class="card p-6" id="add-question-form">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Agregar Pregunta</h3>

            <div class="space-y-4">
                {{-- Tipo --}}
                <div>
                    <label class="form-label">Tipo de pregunta</label>
                    <select x-model="questionType" class="form-select">
                        <option value="multiple_one">Opción múltiple (una respuesta)</option>
                        <option value="multiple_many">Opción múltiple (varias respuestas)</option>
                        <option value="true_false">Verdadero / Falso</option>
                        <option value="short">Respuesta corta (manual)</option>
                    </select>
                </div>

                {{-- Texto --}}
                <div>
                    <label class="form-label">Enunciado de la pregunta</label>
                    <textarea x-model="questionText" class="form-input" rows="3" placeholder="Escribe el enunciado..."></textarea>
                </div>

                {{-- Puntos --}}
                <div>
                    <label class="form-label">Puntaje</label>
                    <input type="number" x-model="questionPoints" class="form-input w-32" min="0.5" max="100" step="0.5" value="1">
                </div>

                {{-- Opciones (para multiple y true_false) --}}
                <div x-show="questionType !== 'short'" x-transition>
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">Opciones</label>
                        <button type="button" @click="addOption()" x-show="questionType !== 'true_false'"
                            class="text-xs text-violet-600 hover:text-violet-800 font-medium">+ Agregar opción</button>
                    </div>
                    <template x-if="questionType === 'true_false'">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-lg">
                                <input type="checkbox" x-model="tfCorrect" :checked="tfCorrect === true" @change="tfCorrect = true" class="rounded text-emerald-500">
                                <span class="text-sm font-medium text-gray-700">Verdadero</span>
                                <span x-show="tfCorrect === true" class="text-xs text-emerald-600 font-medium ml-auto">✓ Correcta</span>
                            </div>
                            <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-lg">
                                <input type="checkbox" :checked="tfCorrect === false" @change="tfCorrect = false" class="rounded text-emerald-500">
                                <span class="text-sm font-medium text-gray-700">Falso</span>
                                <span x-show="tfCorrect === false" class="text-xs text-emerald-600 font-medium ml-auto">✓ Correcta</span>
                            </div>
                        </div>
                    </template>
                    <template x-if="questionType !== 'true_false'">
                        <ul class="space-y-2">
                            <template x-for="(opt, i) in options" :key="i">
                                <li class="flex items-center gap-2">
                                    <input type="text" x-model="opt.text" class="form-input flex-1" placeholder="Texto de la opción">
                                    <label class="flex items-center gap-1 text-xs text-gray-600 whitespace-nowrap cursor-pointer">
                                        <input type="checkbox" x-model="opt.is_correct" class="rounded text-emerald-500"> Correcta
                                    </label>
                                    <button type="button" @click="options.splice(i, 1)" class="p-1 text-red-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>

                {{-- Explicación --}}
                <div>
                    <label class="form-label">Explicación (opcional — se muestra al alumno si se habilitan resultados)</label>
                    <input type="text" x-model="explanation" class="form-input" placeholder="Ej: La respuesta correcta es... porque...">
                </div>

                {{-- Botón enviar --}}
                <button type="button" @click="submitQuestion()" :disabled="saving"
                    class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!saving">Guardar Pregunta</span>
                    <span x-show="saving">Guardando...</span>
                </button>
                <p x-show="error" x-text="error" class="text-sm text-red-600 mt-1"></p>
            </div>
        </div>
        @endif

    </div>{{-- /questionBuilder --}}
    @else
    {{-- Mensaje para evaluaciones tipo documento --}}
    <div class="card p-10 text-center">
        <div class="w-16 h-16 rounded-full bg-violet-100 mx-auto mb-4 flex items-center justify-center">
            <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Evaluación tipo Documento</h3>
        <p class="text-sm text-gray-500 mb-4">Esta evaluación requiere que los estudiantes descarguen el archivo de instrucciones y completen la tarea externamente.</p>
        <p class="text-xs text-gray-400">Asegúrate de subir un archivo con las instrucciones antes de publicar.</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleEvaluationStatus(currentStatus) {
    let config = {};
    
    if (currentStatus === 'draft') {
        config = {
            title: '¿Publicar evaluación?',
            html: '<p class="text-gray-600">Se notificará a todos los alumnos matriculados en el curso.</p>',
            icon: 'question',
            confirmButtonColor: '#10b981',
            confirmButtonText: '🚀 Sí, publicar'
        };
    } else if (currentStatus === 'published') {
        config = {
            title: '¿Cerrar evaluación?',
            html: '<p class="text-gray-600">Los alumnos ya no podrán realizar intentos. El contenido quedará oculto.</p>',
            icon: 'question',
            confirmButtonColor: '#ef4444',
            confirmButtonText: '🔒 Sí, cerrar'
        };
    } else if (currentStatus === 'closed') {
        config = {
            title: '¿Reabrir evaluación?',
            html: '<p class="text-gray-600">Los alumnos podrán volver a acceder y realizar intentos.</p>',
            icon: 'question',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: '🔓 Sí, reabrir'
        };
    }
    
    Swal.fire({
        ...config,
        showCancelButton: true,
        cancelButtonColor: '#6b7280',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-lg font-bold',
            confirmButton: 'rounded-lg px-6 py-2.5 font-semibold',
            cancelButton: 'rounded-lg px-6 py-2.5 font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-status-form').submit();
        }
    });
}

function questionBuilder() {
    return {
        questionType:  'multiple_one',
        questionText:  '',
        questionPoints: 1,
        explanation:   '',
        options: [
            { text: '', is_correct: false },
            { text: '', is_correct: false },
        ],
        tfCorrect: true,
        saving: false,
        error: '',

        addOption() {
            this.options.push({ text: '', is_correct: false });
        },

        async submitQuestion() {
            this.error = '';
            if (!this.questionText.trim()) { this.error = 'El enunciado es obligatorio.'; return; }

            let opts = [];
            if (this.questionType === 'true_false') {
                opts = [
                    { text: 'Verdadero', is_correct: this.tfCorrect === true },
                    { text: 'Falso',     is_correct: this.tfCorrect === false },
                ];
            } else if (this.questionType !== 'short') {
                opts = this.options.filter(o => o.text.trim());
                if (opts.length < 2) { this.error = 'Agrega al menos 2 opciones.'; return; }
                if (this.questionType === 'multiple_one' && opts.filter(o => o.is_correct).length !== 1) {
                    this.error = 'Marca exactamente 1 opción como correcta.'; return;
                }
                if (this.questionType === 'multiple_many' && opts.filter(o => o.is_correct).length < 1) {
                    this.error = 'Marca al menos 1 opción como correcta.'; return;
                }
            }

            this.saving = true;
            try {
                const resp = await fetch('{{ route('docente.evaluations.questions.store', [$course, $evaluation]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type:        this.questionType,
                        text:        this.questionText,
                        points:      parseFloat(this.questionPoints),
                        explanation: this.explanation || null,
                        options:     opts,
                    }),
                });
                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    this.error = data.message || 'Error al guardar la pregunta.';
                } else {
                    // Refrescar la página para mostrar la nueva pregunta
                    window.location.reload();
                }
            } catch (e) {
                this.error = 'Error de conexión.';
            } finally {
                this.saving = false;
            }
        },

        async removeQuestion(questionId, card) {
            if (!confirm('¿Eliminar esta pregunta?')) return;
            try {
                const resp = await fetch('{{ route('docente.evaluations.questions.destroy', [$course, $evaluation, '__QID__']) }}'.replace('__QID__', questionId), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                });
                if (resp.ok) card.remove();
            } catch(e) { alert('Error al eliminar la pregunta.'); }
        },
    };
}
</script>
@endpush
