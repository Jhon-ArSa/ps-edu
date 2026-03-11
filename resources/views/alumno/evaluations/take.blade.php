@extends('layouts.app')

@section('title', 'Evaluación: ' . $evaluation->title)

@section('content')
<div class="max-w-3xl mx-auto"
     x-data="evaluationTimer({
         timeLimit: {{ $evaluation->time_limit ?? 0 }},
         remainingSeconds: {{ $attempt->getRemainingSeconds() }},
         formId: 'eval-form'
     })">

    {{-- Header fijo con temporizador --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm rounded-b-xl mb-6 px-5 py-3 flex items-center justify-between">
        <div class="min-w-0">
            <p class="text-xs text-gray-500 truncate">{{ $course->name }}</p>
            <p class="text-sm font-semibold text-gray-800 truncate">{{ $evaluation->title }}</p>
        </div>
        @if($evaluation->time_limit)
        <div class="flex items-center gap-2 flex-shrink-0 ml-4">
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-mono text-sm font-bold transition-colors"
                 :class="{
                     'bg-gray-100 text-gray-700': progress > 20,
                     'bg-amber-100 text-amber-700': progress <= 20 && progress > 10,
                     'bg-red-100 text-red-700 animate-pulse': progress <= 10
                 }">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="formatTime(timeLeft)"></span>
            </div>
            {{-- Barra de progreso del tiempo --}}
            <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden hidden sm:block">
                <div class="h-full rounded-full transition-all duration-1000"
                     :style="`width: ${progress}%`"
                     :class="{
                         'bg-emerald-500': progress > 50,
                         'bg-amber-400': progress <= 50 && progress > 20,
                         'bg-red-500': progress <= 20
                     }"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Formulario de evaluación --}}
    @if($evaluation->file_path)
    <div class="flex items-center gap-3 p-4 bg-violet-50 border border-violet-200 rounded-xl mb-4">
        <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-700">Archivo de la evaluación</p>
            <p class="text-xs text-gray-400">Material de referencia</p>
        </div>
        <a href="{{ $evaluation->file_url }}" target="_blank"
           class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
            Descargar
        </a>
    </div>
    @endif

    <form id="eval-form" method="POST" action="{{ route('alumno.evaluations.submit', [$course, $evaluation]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @foreach($evaluation->questions as $question)
        <div class="card p-6">
            <div class="flex items-start gap-3 mb-4">
                <span class="w-8 h-8 rounded-full bg-violet-100 text-violet-700 font-bold text-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                    {{ $loop->iteration }}
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $question->type_badge_class }}">{{ $question->type_label }}</span>
                        <span class="text-xs text-gray-400">{{ $question->points }} {{ $question->points == 1 ? 'punto' : 'puntos' }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800 leading-relaxed">{{ $question->text }}</p>
                </div>
            </div>

            @php $existingAnswer = $existingAnswers->get($question->id); @endphp

            {{-- Opciones según tipo --}}
            @if($question->type === 'short')
                <textarea name="answers[{{ $question->id }}]"
                    class="form-input"
                    rows="4"
                    placeholder="Escribe tu respuesta aquí..."
                    >{{ $existingAnswer?->text_answer }}</textarea>

            @elseif($question->type === 'multiple_one')
                <div class="space-y-2">
                    @foreach($question->options as $option)
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-violet-50 hover:border-violet-300 transition-colors has-[:checked]:bg-violet-50 has-[:checked]:border-violet-400">
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $option->id }}"
                               {{ in_array($option->id, $existingAnswer?->selected_options ?? []) ? 'checked' : '' }}
                               class="w-4 h-4 text-violet-600">
                        <span class="text-sm text-gray-700">{{ $option->text }}</span>
                    </label>
                    @endforeach
                </div>

            @elseif($question->type === 'multiple_many')
                <p class="text-xs text-indigo-600 mb-2 italic">Puedes seleccionar varias opciones correctas.</p>
                <div class="space-y-2">
                    @foreach($question->options as $option)
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition-colors has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-400">
                        <input type="checkbox"
                               name="answers[{{ $question->id }}][]"
                               value="{{ $option->id }}"
                               {{ in_array($option->id, $existingAnswer?->selected_options ?? []) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-sm text-gray-700">{{ $option->text }}</span>
                    </label>
                    @endforeach
                </div>

            @elseif($question->type === 'true_false')
                <div class="flex gap-3">
                    @foreach($question->options as $option)
                    <label class="flex-1 flex items-center justify-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-colors has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                        <input type="radio"
                               name="answers[{{ $question->id }}]"
                               value="{{ $option->id }}"
                               {{ in_array($option->id, $existingAnswer?->selected_options ?? []) ? 'checked' : '' }}
                               class="w-4 h-4 text-amber-600">
                        <span class="text-sm font-medium text-gray-700">{{ $option->text }}</span>
                    </label>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach

        {{-- Botón de envío --}}
        <div class="card p-5 space-y-4">
            {{-- Archivo de respuesta opcional --}}
            <div>
                <label class="form-label flex items-center gap-1.5 mb-1">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Adjuntar archivo de respuesta (opcional)
                </label>
                <input type="file" name="file_answer"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.jpg,.jpeg,.png"
                       class="w-full text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <p class="text-xs text-gray-400 mt-1">PDF, Word, imagen u otros. Máx. 10 MB.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">Revisa tus respuestas antes de enviar. Una vez enviado no podrás modificarlas.</p>
                <button type="button"
                    onclick="Swal.fire({
                        title: '¿Enviar evaluación?',
                        text: 'Una vez enviada no podrás modificar tus respuestas.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#7c3aed',
                        cancelButtonText: 'Continuar revisando',
                        confirmButtonText: 'Sí, enviar'
                    }).then(r => { if(r.isConfirmed) document.getElementById('eval-form').submit(); })"
                    class="btn-primary bg-violet-600 hover:bg-violet-700 border-violet-600 whitespace-nowrap">
                    Enviar evaluación
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
function evaluationTimer({ timeLimit, remainingSeconds, formId }) {
    return {
        timeLeft: remainingSeconds,
        totalTime: remainingSeconds,
        timer: null,

        get progress() {
            if (!timeLimit || this.totalTime === 0) return 100;
            return Math.max(0, (this.timeLeft / this.totalTime) * 100);
        },

        init() {
            if (!timeLimit || this.timeLeft <= 0) return;
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timer);
                    // Auto-enviar cuando expira el tiempo
                    Swal.fire({
                        title: 'Tiempo agotado',
                        text:  'El tiempo de la evaluación ha expirado. Enviando automáticamente...',
                        icon:  'warning',
                        timer: 3000,
                        showConfirmButton: false,
                    }).then(() => {
                        document.getElementById(formId).submit();
                    });
                    setTimeout(() => document.getElementById(formId).submit(), 3500);
                }
            }, 1000);
        },

        formatTime(secs) {
            if (secs <= 0) return '00:00';
            const m = Math.floor(secs / 60).toString().padStart(2, '0');
            const s = (secs % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },
    };
}
</script>
@endsection
