@extends('layouts.app')

@section('title', 'Libreta de notas – ' . $course->name)

@section('content')
<div class="space-y-6">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('docente.courses.show', $course) }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 mb-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                {{ $course->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Libreta de calificaciones</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $students->count() }} alumno{{ $students->count() !== 1 ? 's' : '' }} matriculados
                · {{ $items->count() }} ítem{{ $items->count() !== 1 ? 's' : '' }}
            </p>
        </div>

        {{-- Botón agregar ítem --}}
        <button type="button"
                x-data
                @click="$dispatch('open-add-item')"
                class="btn-primary inline-flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Agregar ítem
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($students->isEmpty())
        {{-- Estado vacío --}}
        <div class="card text-center py-16">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
            </div>
            <p class="text-gray-500">No hay alumnos matriculados en este curso.</p>
        </div>

    @else
        {{-- Tabla de calificaciones --}}
        <div x-data="gradebook()" class="space-y-4">

            {{-- Tabla scrollable --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                {{-- Columna alumno --}}
                                <th class="sticky left-0 z-10 bg-gray-50 text-left px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wide min-w-[200px] border-r border-gray-200">
                                    Alumno
                                </th>

                                {{-- Columnas por ítem --}}
                                @foreach($items as $item)
                                <th class="px-3 py-2 text-center min-w-[120px] border-r border-gray-100 last:border-r-0">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="font-medium text-gray-800 text-xs leading-tight text-center">
                                            {{ Str::limit($item->name, 24) }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $item->type_badge_class }}">
                                            {{ $item->type_label }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">
                                            / {{ $item->max_score }}
                                            @if($item->weight > 0)
                                                · {{ $item->weight }}%
                                            @endif
                                        </span>

                                        @if($item->isManual())
                                        {{-- Acciones del ítem --}}
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open"
                                                    class="text-gray-400 hover:text-gray-600 p-0.5 rounded">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false"
                                                 x-transition
                                                 class="absolute top-5 left-1/2 -translate-x-1/2 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-20 w-32 text-left">
                                                <button type="button"
                                                        @click="open=false; $dispatch('edit-item', {{ json_encode(['id'=>$item->id,'name'=>$item->name,'weight'=>$item->weight,'max_score'=>$item->max_score]) }})"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">
                                                    Editar
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('docente.grades.items.destroy', [$course, $item]) }}"
                                                      onsubmit="return confirm('¿Eliminar este ítem y todas las notas asociadas?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </th>
                                @endforeach

                                {{-- Columna promedio --}}
                                <th class="px-4 py-3 text-center min-w-[90px] bg-gray-50 border-l border-gray-200 font-semibold text-gray-700 text-xs uppercase tracking-wide">
                                    Promedio
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Alumno --}}
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 border-r border-gray-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm leading-tight">
                                                {{ $student->name }}
                                            </div>
                                            @if($student->alumnoProfile?->student_code)
                                                <div class="text-[11px] text-gray-400">{{ $student->alumnoProfile->student_code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Celda por ítem --}}
                                @foreach($items as $item)
                                @php $grade = $gradesMap[$item->id][$student->id] ?? null; @endphp
                                <td class="px-2 py-2 text-center border-r border-gray-100 last:border-r-0">
                                    @if($item->isManual())
                                        {{-- Celda editable --}}
                                        <div x-data="gradeCell({
                                                gradeItemId: {{ $item->id }},
                                                userId: {{ $student->id }},
                                                score: {{ $grade?->score ?? 'null' }},
                                                maxScore: {{ $item->max_score }},
                                                updateUrl: '{{ route('docente.grades.update', [$course, $item, $student]) }}'
                                             })"
                                             class="relative">

                                            {{-- Display --}}
                                            <button @click="startEdit"
                                                    x-show="!editing"
                                                    :class="scoreClasses"
                                                    class="w-full px-2 py-1.5 rounded-lg text-sm font-medium transition-all hover:ring-2 hover:ring-primary-300 hover:ring-offset-1 min-w-[52px]">
                                                <span x-text="displayScore"></span>
                                            </button>

                                            {{-- Editor inline --}}
                                            <div x-show="editing" x-transition class="flex items-center gap-1">
                                                <input x-ref="scoreInput"
                                                       type="number"
                                                       x-model="editValue"
                                                       @keydown.enter="save"
                                                       @keydown.escape="cancelEdit"
                                                       :max="maxScore"
                                                       min="0"
                                                       step="0.5"
                                                       class="w-16 text-center text-sm border border-primary-400 rounded-lg px-1.5 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                <button @click="save"
                                                        :disabled="saving"
                                                        class="text-emerald-600 hover:text-emerald-700 disabled:opacity-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Spinner --}}
                                            <div x-show="saving" class="absolute inset-0 flex items-center justify-center bg-white/70 rounded-lg">
                                                <svg class="animate-spin w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Solo lectura (task/evaluation) --}}
                                        <span class="{{ $grade?->score_color_class ?? 'text-gray-400' }} text-sm">
                                            {{ $grade?->score !== null ? number_format($grade->score, 1) : '—' }}
                                        </span>
                                    @endif
                                </td>
                                @endforeach

                                {{-- Promedio del alumno --}}
                                @php
                                    // Calcular el promedio en Blade para la carga inicial
                                    $studentGrades = collect($gradesMap)->map(fn($u) => $u[$student->id] ?? null)->filter();
                                    $totalWeight   = $items->sum('weight');
                                    $useWeighted   = $totalWeight > 0;
                                    $wSum  = 0; $wW = 0; $sSum = 0; $sC = 0;
                                    foreach ($items as $itm) {
                                        $g = $gradesMap[$itm->id][$student->id] ?? null;
                                        if (!$g || $g->score === null) continue;
                                        $norm = ($g->score / $itm->max_score) * 20.0;
                                        if ($useWeighted && $itm->weight > 0) { $wSum += $norm * $itm->weight; $wW += $itm->weight; }
                                        $sSum += $norm; $sC++;
                                    }
                                    $avg = null;
                                    if ($useWeighted && $wW > 0) $avg = round($wSum / $wW, 1);
                                    elseif ($sC > 0)             $avg = round($sSum / $sC, 1);

                                    $avgClass = $avg === null ? 'text-gray-400'
                                        : ($avg < 11 ? 'text-red-600 font-bold'
                                        : ($avg < 14 ? 'text-amber-600 font-bold'
                                        : 'text-emerald-600 font-bold'));
                                @endphp
                                <td class="px-4 py-3 text-center bg-gray-50 border-l border-gray-200"
                                    id="avg-{{ $student->id }}">
                                    <span class="text-sm {{ $avgClass }}">
                                        {{ $avg !== null ? number_format($avg, 1) : '—' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Leyenda de colores --}}
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 px-1">
                <span class="font-medium text-gray-600">Escala vigesimal:</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                    Desaprobado (0–10)
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Regular (11–13)
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Aprobado (14–20)
                </span>
                <span class="ml-auto text-gray-400">
                    Haz clic en una celda editable para ingresar nota
                </span>
            </div>

        </div>{{-- /x-data gradebook --}}
    @endif

</div>

{{-- ── Modal: Agregar ítem ── --}}
<div x-data="addItemModal()"
     x-show="open"
     x-transition.opacity
     @open-add-item.window="open = true"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     style="display:none;">
    <div @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900">Agregar ítem de calificación</h2>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('docente.grades.items.store', $course) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del ítem <span class="text-red-500">*</span></label>
                <input name="name" type="text" required maxlength="80" placeholder="Ej. Participación semana 3"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="type" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="participation">Participación</option>
                    <option value="oral">Oral</option>
                    <option value="final">Final</option>
                    <option value="other">Otro</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nota máxima</label>
                    <input name="max_score" type="number" min="1" max="100" step="0.5" value="20"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (%)</label>
                    <input name="weight" type="number" min="0" max="100" step="0.5" value="0"
                           placeholder="0 = sin peso"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary px-4 py-2 text-sm">
                    Agregar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Editar ítem ── --}}
<div x-data="editItemModal()"
     x-show="open"
     x-transition.opacity
     @edit-item.window="load($event.detail)"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     style="display:none;">
    <div @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900">Editar ítem</h2>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form :action="`{{ url('docente/cursos/' . $course->id . '/notas/items') }}/${itemId}`" method="POST" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input name="name" type="text" required maxlength="80" x-model="name"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nota máxima</label>
                    <input name="max_score" type="number" min="1" max="100" step="0.5" x-model="maxScore"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (%)</label>
                    <input name="weight" type="number" min="0" max="100" step="0.5" x-model="weight"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary px-4 py-2 text-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ── Gradebook Alpine components ──────────────────────────────────────────────

document.addEventListener('alpine:init', () => {

    // ── Componente raíz de la tabla ──────────────────────────────────────
    Alpine.data('gradebook', () => ({
        init() {}
    }));

    // ── Modal: agregar ítem ──────────────────────────────────────────────
    Alpine.data('addItemModal', () => ({
        open: false,
    }));

    // ── Modal: editar ítem ───────────────────────────────────────────────
    Alpine.data('editItemModal', () => ({
        open:     false,
        itemId:   null,
        name:     '',
        weight:   0,
        maxScore: 20,
        load(detail) {
            this.itemId   = detail.id;
            this.name     = detail.name;
            this.weight   = detail.weight;
            this.maxScore = detail.max_score;
            this.open     = true;
        },
    }));

    // ── Celda de nota editable ───────────────────────────────────────────
    Alpine.data('gradeCell', ({ gradeItemId, userId, score, maxScore, updateUrl }) => ({
        editing:    false,
        saving:     false,
        score:      score,
        editValue:  score ?? '',
        maxScore:   maxScore,
        updateUrl:  updateUrl,
        userId:     userId,

        get displayScore() {
            return this.score !== null ? Number(this.score).toFixed(1) : '—';
        },

        get scoreClasses() {
            if (this.score === null) return 'bg-gray-100 text-gray-400';
            const normalized = (this.score / this.maxScore) * 20;
            if (normalized < 11) return 'bg-red-50 text-red-700';
            if (normalized < 14) return 'bg-amber-50 text-amber-700';
            return 'bg-emerald-50 text-emerald-700';
        },

        startEdit() {
            this.editValue = this.score !== null ? this.score : '';
            this.editing   = true;
            this.$nextTick(() => this.$refs.scoreInput?.focus());
        },

        cancelEdit() {
            this.editing = false;
        },

        async save() {
            const val = parseFloat(this.editValue);
            if (isNaN(val) || val < 0 || val > this.maxScore) return;

            this.saving  = true;
            this.editing = false;

            try {
                const response = await axios.patch(this.updateUrl, {
                    score: val,
                });
                if (response.data.success) {
                    this.score = response.data.score;
                    // Actualizar promedio del alumno en la fila
                    const avgCell = document.getElementById('avg-' + this.userId);
                    if (avgCell && response.data.average !== null) {
                        const avg = response.data.average;
                        let cls = 'text-gray-400';
                        if (avg !== null) {
                            cls = avg < 11 ? 'text-red-600 font-bold'
                                : avg < 14 ? 'text-amber-600 font-bold'
                                : 'text-emerald-600 font-bold';
                        }
                        avgCell.innerHTML = `<span class="text-sm ${cls}">${avg !== null ? avg.toFixed(1) : '—'}</span>`;
                    }
                }
            } catch (e) {
                // Revertir
                this.editing = true;
                console.error('Error al guardar nota:', e);
            } finally {
                this.saving = false;
            }
        },
    }));
});
</script>
@endpush
@endsection
