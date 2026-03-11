{{-- Expects: $week, $course, $submissions (Collection keyed by task_id) --}}
@php $activeTasks = $week->tasks->where('status', 'active'); @endphp

@if($activeTasks->isNotEmpty())
<div class="border-t border-gray-100">
    <div class="px-5 py-2.5 bg-violet-50/50">
        <p class="text-xs font-bold text-violet-600 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Tareas ({{ $activeTasks->count() }})
        </p>
    </div>

    <div class="divide-y divide-gray-50">
        @foreach($activeTasks->sortBy('created_at') as $task)
        @php
            $submission = $submissions->get($task->id);
            $isExpired  = $task->isExpired();
            $dueBadge   = $task->due_date_badge;
        @endphp

        <div class="px-5 py-4" x-data="{ editing: false, showInstructions: false }">
            {{-- Task header --}}
            <div class="flex items-start gap-3">
                {{-- Status icon --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 shadow-sm
                    @if($submission && $submission->isGraded()) bg-gradient-to-br from-emerald-100 to-emerald-50 ring-1 ring-emerald-200
                    @elseif($submission) bg-gradient-to-br from-blue-100 to-blue-50 ring-1 ring-blue-200
                    @elseif($isExpired) bg-gradient-to-br from-red-100 to-red-50 ring-1 ring-red-200
                    @else bg-gradient-to-br from-violet-100 to-violet-50 ring-1 ring-violet-200
                    @endif">
                    @if($submission && $submission->isGraded())
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif($submission)
                    <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($isExpired)
                    <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    {{-- Title + badges --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium {{ $dueBadge['class'] }}">
                            {{ $dueBadge['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            {{ $task->max_score }} pts
                        </span>
                        @if($submission)
                            @php $sBadge = $submission->status_badge; @endphp
                            <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium {{ $sBadge['class'] }}">
                                {{ $sBadge['label'] }}
                            </span>
                        @elseif($isExpired)
                            <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium bg-red-100 text-red-700">No entregada</span>
                        @endif
                    </div>

                    @if($task->description)
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">{{ $task->description }}</p>
                    @endif

                    {{-- Instructions toggle --}}
                    @if($task->instructions || $task->file_path)
                    <div class="mt-2">
                        <button @click="showInstructions = !showInstructions"
                                class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1 transition-colors">
                            <svg class="w-3 h-3 transition-transform" :class="showInstructions ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span x-text="showInstructions ? 'Ocultar instrucciones' : 'Ver instrucciones'"></span>
                        </button>
                        <div x-show="showInstructions" x-cloak
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-2 space-y-2">
                            @if($task->instructions)
                            <div class="p-3.5 bg-violet-50 rounded-xl border border-violet-100 text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $task->instructions }}</div>
                            @endif
                            @if($task->file_path)
                            <a href="{{ Storage::url($task->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs text-violet-600 hover:text-violet-700 hover:underline font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Descargar guía/anexo
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ═══ SUBMISSION AREA ═══════════════════════════════ --}}
                    <div class="mt-3.5 pt-3.5 border-t border-gray-100">

                        {{-- GRADED --}}
                        @if($submission && $submission->isGraded())
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-emerald-50/70 rounded-xl border border-emerald-100">
                                <div class="px-3 py-1.5 bg-white rounded-xl text-center shadow-sm border border-emerald-100">
                                    <p class="text-xl font-bold {{ $submission->score_color }}">{{ $submission->score }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">/{{ $task->max_score }}</p>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Calificada
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $submission->graded_at?->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($submission->feedback)
                            <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-100">
                                <p class="text-xs font-bold text-emerald-700 mb-1 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Retroalimentación del docente
                                </p>
                                <p class="text-xs text-emerald-800 leading-relaxed">{{ $submission->feedback }}</p>
                            </div>
                            @endif
                            @if($submission->file_path)
                            <a href="{{ $submission->file_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:underline font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ $submission->original_filename ?? 'Mi archivo entregado' }}
                            </a>
                            @endif
                            @if($submission->comments)
                            <p class="text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg">
                                <span class="font-semibold text-gray-600">Mi comentario:</span> {{ $submission->comments }}
                            </p>
                            @endif
                        </div>

                        {{-- SUBMITTED, NOT GRADED --}}
                        @elseif($submission)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-blue-50/70 rounded-xl border border-blue-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-blue-700">Entregada</p>
                                        <p class="text-[11px] text-blue-500">{{ $submission->submitted_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                @if(!$isExpired)
                                <button @click="editing = !editing"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-blue-200 text-xs text-violet-600 hover:text-violet-700 font-semibold rounded-lg hover:bg-violet-50 transition-colors shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar entrega
                                </button>
                                @endif
                            </div>

                            @if($submission->file_path)
                            <a href="{{ $submission->file_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:underline font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ $submission->original_filename ?? 'Mi archivo entregado' }}
                            </a>
                            @endif
                            @if($submission->comments)
                            <p class="text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg">
                                <span class="font-semibold text-gray-600">Mi comentario:</span> {{ $submission->comments }}
                            </p>
                            @endif

                            {{-- Edit form --}}
                            @if(!$isExpired)
                            <div x-show="editing" x-cloak
                                 x-transition:enter="transition-all duration-200 ease-out"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <form method="POST"
                                      action="{{ route('alumno.submissions.update', [$course, $task, $submission]) }}"
                                      enctype="multipart/form-data"
                                      class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reemplazar archivo <span class="text-gray-400 font-normal">(opcional)</span></label>
                                        <input type="file" name="file"
                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png"
                                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Comentario</label>
                                        <textarea name="comments" rows="2" placeholder="Comentario sobre tu entrega…"
                                                  class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent resize-none bg-white">{{ $submission->comments }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            Actualizar entrega
                                        </button>
                                        <button type="button" @click="editing = false"
                                                class="px-4 py-2 bg-white text-gray-600 text-xs rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>

                        {{-- EXPIRED, NOT SUBMITTED --}}
                        @elseif($isExpired)
                        <div class="flex items-center gap-2.5 p-3.5 bg-red-50 rounded-xl border border-red-100">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-red-700">Plazo vencido</p>
                                <p class="text-[11px] text-red-500">La fecha límite de esta tarea ha pasado. No se puede entregar.</p>
                            </div>
                        </div>

                        {{-- NOT SUBMITTED, OPEN → show form --}}
                        @else
                        <div class="p-4 bg-violet-50/40 rounded-xl border border-violet-100/60">
                            <form method="POST"
                                  action="{{ route('alumno.submissions.store', [$course, $task]) }}"
                                  enctype="multipart/form-data"
                                  class="space-y-3">
                                @csrf
                                <p class="text-xs font-bold text-violet-700 uppercase tracking-wider flex items-center gap-1.5 mb-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Entregar tarea
                                </p>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1.5 font-medium">Archivo (PDF, DOC, ZIP, imagen — máx. 10 MB)</label>
                                    <input type="file" name="file"
                                           accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png"
                                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200 transition">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1.5 font-medium">Comentario <span class="text-gray-400">(opcional)</span></label>
                                    <textarea name="comments" rows="2" placeholder="Escribe un comentario sobre tu entrega…"
                                              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent resize-none bg-white"></textarea>
                                </div>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Enviar entrega
                                </button>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Evaluaciones de esta semana --}}
{{-- Expects: $evalAttempts (Collection keyed by evaluation_id) --}}
@php $weekEvals = $week->evaluations ?? collect(); @endphp

@if($weekEvals->isNotEmpty())
<div class="border-t border-gray-100">
    <div class="px-5 py-2.5 bg-amber-50/50">
        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Evaluaciones ({{ $weekEvals->count() }})
        </p>
    </div>

    <div class="divide-y divide-gray-50">
        @foreach($weekEvals as $evaluation)
        @php
            $attempt = isset($evalAttempts) ? $evalAttempts->get($evaluation->id) : null;

            if ($attempt && $attempt->status === 'in_progress') {
                $evalBadge   = ['label' => 'En progreso', 'class' => 'bg-amber-100 text-amber-700'];
                $evalIconBg  = 'bg-gradient-to-br from-amber-100 to-amber-50 ring-1 ring-amber-200';
                $evalIconCol = 'text-amber-600';
                $actionUrl   = route('alumno.evaluations.take', [$course, $evaluation]);
                $actionLabel = 'Continuar evaluación';
                $actionClass = 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm';
            } elseif ($attempt && $attempt->status === 'graded') {
                $evalBadge   = ['label' => 'Calificada', 'class' => 'bg-emerald-100 text-emerald-700'];
                $evalIconBg  = 'bg-gradient-to-br from-emerald-100 to-emerald-50 ring-1 ring-emerald-200';
                $evalIconCol = 'text-emerald-600';
                $actionUrl   = route('alumno.evaluations.result', [$course, $evaluation, $attempt]);
                $actionLabel = 'Ver resultado';
                $actionClass = 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm';
            } elseif ($attempt && $attempt->status === 'submitted') {
                $evalBadge   = ['label' => 'Entregada', 'class' => 'bg-blue-100 text-blue-700'];
                $evalIconBg  = 'bg-gradient-to-br from-blue-100 to-blue-50 ring-1 ring-blue-200';
                $evalIconCol = 'text-blue-600';
                $actionUrl   = route('alumno.evaluations.show', [$course, $evaluation]);
                $actionLabel = 'Ver detalles';
                $actionClass = 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm';
            } elseif ($evaluation->status === 'closed' || ($evaluation->closes_at && $evaluation->closes_at->isPast())) {
                $evalBadge   = ['label' => 'Cerrada', 'class' => 'bg-red-100 text-red-700'];
                $evalIconBg  = 'bg-gradient-to-br from-red-100 to-red-50 ring-1 ring-red-200';
                $evalIconCol = 'text-red-400';
                $actionUrl   = null;
                $actionLabel = null;
                $actionClass = '';
            } elseif ($evaluation->opens_at && $evaluation->opens_at->isFuture()) {
                $evalBadge   = ['label' => 'Próximamente', 'class' => 'bg-gray-100 text-gray-600'];
                $evalIconBg  = 'bg-gradient-to-br from-gray-100 to-gray-50 ring-1 ring-gray-200';
                $evalIconCol = 'text-gray-400';
                $actionUrl   = null;
                $actionLabel = null;
                $actionClass = '';
            } elseif ($evaluation->isOpen()) {
                $evalBadge   = ['label' => 'Disponible', 'class' => 'bg-green-100 text-green-700'];
                $evalIconBg  = 'bg-gradient-to-br from-green-100 to-green-50 ring-1 ring-green-200';
                $evalIconCol = 'text-green-600';
                $actionUrl   = route('alumno.evaluations.show', [$course, $evaluation]);
                $actionLabel = 'Iniciar evaluación';
                $actionClass = 'bg-green-600 hover:bg-green-700 text-white shadow-sm';
            } else {
                $evalBadge   = ['label' => 'No disponible', 'class' => 'bg-gray-100 text-gray-500'];
                $evalIconBg  = 'bg-gradient-to-br from-gray-100 to-gray-50 ring-1 ring-gray-200';
                $evalIconCol = 'text-gray-400';
                $actionUrl   = null;
                $actionLabel = null;
                $actionClass = '';
            }
        @endphp

        <div class="px-5 py-4">
            <div class="flex items-start gap-3">
                {{-- Status icon --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 shadow-sm {{ $evalIconBg }}">
                    @if($attempt && $attempt->status === 'graded')
                    <svg class="w-4.5 h-4.5 {{ $evalIconCol }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif($attempt && $attempt->status === 'submitted')
                    <svg class="w-4.5 h-4.5 {{ $evalIconCol }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($attempt && $attempt->status === 'in_progress')
                    <svg class="w-4.5 h-4.5 {{ $evalIconCol }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                    <svg class="w-4.5 h-4.5 {{ $evalIconCol }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    {{-- Title + badges --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-gray-900">{{ $evaluation->title }}</p>
                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium {{ $evalBadge['class'] }}">
                            {{ $evalBadge['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            {{ $evaluation->max_score }} pts
                        </span>
                        @if($evaluation->time_limit)
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $evaluation->time_limit }} min
                        </span>
                        @endif
                    </div>

                    {{-- Graded score display --}}
                    @if($attempt && $attempt->status === 'graded' && $attempt->score !== null)
                    <div class="flex items-center gap-2.5 mt-2">
                        <div class="px-3 py-1.5 bg-white rounded-xl text-center shadow-sm border border-emerald-100">
                            <p class="text-xl font-bold {{ $attempt->score_color_class }}">{{ number_format($attempt->score, 1) }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">/{{ $evaluation->max_score }}</p>
                        </div>
                        <p class="text-xs text-emerald-700 font-medium">Nota obtenida</p>
                    </div>
                    @endif

                    {{-- closes_at hint --}}
                    @if($evaluation->closes_at && $evaluation->closes_at->isFuture())
                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Cierra {{ $evaluation->closes_at->format('d/m/Y H:i') }}
                    </p>
                    @endif

                    {{-- Action button --}}
                    @if($actionUrl)
                    <div class="mt-3">
                        <a href="{{ $actionUrl }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg transition-colors {{ $actionClass }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $actionLabel }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
