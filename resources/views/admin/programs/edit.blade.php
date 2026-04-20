@extends('layouts.app')

@section('title', 'Editar: ' . $program->name)

@section('breadcrumb')
    <a href="{{ route('admin.programs.index') }}" class="hover:text-primary-600">Programas</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.programs.show', $program) }}" class="hover:text-primary-600">{{ $program->code }}</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="programEditForm()">

    {{-- Header with status --}}
    <div class="flex items-center justify-between animate-fade-in-up">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Editar programa</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $program->name }}</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
            {{ $program->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
            <span class="w-2 h-2 rounded-full {{ $program->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
            {{ $program->status_label }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Card 1: Información general --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Información del programa</h2>
                    <p class="text-xs text-gray-400">Datos generales de identificación</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="form-label">Nombre del programa <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $program->name) }}" required
                           class="form-input w-full">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Código <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $program->code) }}" required maxlength="20"
                               class="form-input w-full font-mono uppercase">
                        @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Grado académico <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="type in degreeTypes" :key="type.value">
                                <label class="relative flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition-all text-center"
                                       :class="degreeType === type.value ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600'">
                                    <input type="radio" name="degree_type" :value="type.value" x-model="degreeType" class="sr-only">
                                    <span class="text-xs font-bold leading-tight" x-text="type.label"></span>
                                </label>
                            </template>
                        </div>
                        @error('degree_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Estado</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1"
                               :class="status === 'active' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="status" value="active" x-model="status" class="sr-only">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0" :class="status === 'active' ? 'ring-4 ring-emerald-100' : 'opacity-30'"></span>
                            <div>
                                <p class="text-sm font-bold" :class="status === 'active' ? 'text-emerald-700' : 'text-gray-500'">Activo</p>
                                <p class="text-[10px]" :class="status === 'active' ? 'text-emerald-500' : 'text-gray-400'">Visible y operable</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1"
                               :class="status === 'inactive' ? 'border-gray-400 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="status" value="inactive" x-model="status" class="sr-only">
                            <span class="w-3 h-3 rounded-full bg-gray-400 shrink-0" :class="status === 'inactive' ? 'ring-4 ring-gray-200' : 'opacity-30'"></span>
                            <div>
                                <p class="text-sm font-bold" :class="status === 'inactive' ? 'text-gray-700' : 'text-gray-500'">Inactivo</p>
                                <p class="text-[10px]" :class="status === 'inactive' ? 'text-gray-500' : 'text-gray-400'">Suspendido</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="3" placeholder="Breve descripción del programa..."
                              class="form-input w-full resize-none">{{ old('description', $program->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Card 2: Estructura académica --}}
        <div class="card animate-fade-in-up delay-2">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Estructura académica</h2>
                    <p class="text-xs text-gray-400">Duración, créditos y resolución</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Duración (semestres) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="duration_semesters" x-model="durationSemesters" required min="1" max="20"
                                   class="form-input w-full pr-16">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none" x-text="durationLabel"></span>
                        </div>
                        @error('duration_semesters') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Total de créditos</label>
                        <input type="number" name="total_credits" value="{{ old('total_credits', $program->total_credits) }}" min="1" max="500"
                               class="form-input w-full">
                        @error('total_credits') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Resolución</label>
                        <input type="text" name="resolution" value="{{ old('resolution', $program->resolution) }}"
                               class="form-input w-full">
                        @error('resolution') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Semestre propedéutico --}}
                <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                       :class="hasPropedeutic ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="checkbox" name="has_propedeutic" value="1" x-model="hasPropedeutic"
                           class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <div>
                        <p class="text-sm font-bold" :class="hasPropedeutic ? 'text-violet-700' : 'text-gray-600'">Incluye semestre propedéutico (Semestre 0)</p>
                        <p class="text-[10px]" :class="hasPropedeutic ? 'text-violet-500' : 'text-gray-400'">Semestre de preparación previo al inicio formal del programa</p>
                    </div>
                </label>

                {{-- Duration visual --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <p class="text-xs font-semibold text-gray-500">Vista previa de duración</p>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-if="hasPropedeutic">
                            <div class="flex items-center gap-1.5 bg-violet-50 rounded-lg px-2.5 py-1.5 border border-violet-200 text-xs">
                                <span class="w-2 h-2 rounded-full bg-violet-400"></span>
                                <span class="font-medium text-violet-700">Sem. 0</span>
                                <span class="text-[10px] text-violet-400">Propedéutico</span>
                            </div>
                        </template>
                        <template x-for="i in parseInt(durationSemesters) || 0" :key="i">
                            <div class="flex items-center gap-1.5 bg-white rounded-lg px-2.5 py-1.5 border border-gray-200 text-xs">
                                <span class="w-2 h-2 rounded-full bg-primary-400"></span>
                                <span class="font-medium text-gray-600">Sem. <span x-text="i"></span></span>
                                <span class="text-[10px] text-gray-400" x-text="'Año ' + Math.ceil(i/2)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Coordinador --}}
        <div class="card animate-fade-in-up delay-3">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Coordinador del programa</h2>
                    <p class="text-xs text-gray-400">Docente responsable de la gestión académica</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <div x-data="coordinatorEditSelect()" @click.away="open = false">
                    <label class="form-label">Seleccionar coordinador</label>

                    {{-- Selected coordinator display --}}
                    <div x-show="selectedName" x-cloak class="flex items-center gap-3 p-3 mb-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center shrink-0">
                            <span class="text-amber-700 text-xs font-bold" x-text="selectedInitials"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="selectedName"></p>
                            <p class="text-xs text-gray-400" x-text="selectedEmail"></p>
                        </div>
                        <button type="button" @click="clearSelection()" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Search input --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m21 21-4.35-4.35"/></svg>
                        <input type="text" x-model="search" @focus="open = true"
                               placeholder="Buscar docente por nombre o correo..."
                               class="form-input w-full pl-10" autocomplete="off">
                    </div>

                    <input type="hidden" name="coordinator_id" :value="selectedId">

                    {{-- Lista inline (no flotante) --}}
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-1 bg-white rounded-xl border border-gray-200 shadow-lg max-h-64 overflow-y-auto">
                        <div class="p-1.5">
                            <div @click="clearSelection(); open = false"
                                 class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                                 :class="!selectedId ? 'bg-primary-50 border border-primary-100' : ''">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                                </div>
                                <span class="text-sm text-gray-500 italic">Sin coordinador asignado</span>
                                <svg x-show="!selectedId" class="w-4 h-4 text-primary-500 ml-auto shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            @foreach($coordinators as $coordinator)
                            <div @click="selectCoordinator('{{ $coordinator->id }}', '{{ addslashes($coordinator->name) }}', '{{ $coordinator->email }}'); open = false; search = ''"
                                 class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 cursor-pointer transition-colors"
                                 :class="selectedId == '{{ $coordinator->id }}' ? 'bg-amber-50 border border-amber-100' : ''"
                                 x-show="!search || '{{ strtolower(addslashes($coordinator->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower($coordinator->email) }}'.includes(search.toLowerCase())">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center shrink-0">
                                    <span class="text-amber-600 text-[10px] font-bold">{{ strtoupper(substr($coordinator->name, 0, 2)) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $coordinator->name }}
                                        @if($program->coordinator_id == $coordinator->id)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded ml-1">Actual</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $coordinator->email }}</p>
                                </div>
                                <svg x-show="selectedId == '{{ $coordinator->id }}'" class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @error('coordinator_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
                        <div class="p-1.5">
                            <div @click="clearSelection(); open = false"
                                 class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                                 :class="!selectedId ? 'bg-primary-50 border border-primary-200' : ''">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                                </div>
                                <span class="text-sm text-gray-500 italic">Sin coordinador asignado</span>
                            </div>
                            @foreach($coordinators as $coordinator)
                            <div @click="selectCoordinator('{{ $coordinator->id }}', '{{ addslashes($coordinator->name) }}', '{{ $coordinator->email }}'); open = false; search = ''"
                                 class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 cursor-pointer transition-colors"
                                 :class="selectedId == '{{ $coordinator->id }}' ? 'bg-amber-50 border border-amber-200' : ''"
                                 x-show="!search || '{{ strtolower(addslashes($coordinator->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower($coordinator->email) }}'.includes(search.toLowerCase())">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center shrink-0">
                                    <span class="text-amber-600 text-[10px] font-bold">{{ strtoupper(substr($coordinator->name, 0, 2)) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $coordinator->name }}
                                        @if($program->coordinator_id == $coordinator->id)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded ml-1">Actual</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $coordinator->email }}</p>
                                </div>
                                <svg x-show="selectedId == '{{ $coordinator->id }}'" class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @error('coordinator_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 animate-fade-in-up delay-4">
            <a href="{{ route('admin.programs.show', $program) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function programEditForm() {
    return {
        degreeType: '{{ old('degree_type', $program->degree_type) }}',
        status: '{{ old('status', $program->status) }}',
        durationSemesters: '{{ old('duration_semesters', $program->duration_semesters) }}',
        hasPropedeutic: {{ old('has_propedeutic', $program->has_propedeutic) ? 'true' : 'false' }},
        degreeTypes: [
            { value: 'maestria', label: 'Maestría' },
            { value: 'doctorado', label: 'Doctorado' },
            { value: 'segunda_especialidad', label: '2da Especialidad' },
            { value: 'diplomado', label: 'Diplomado' },
        ],
        get durationLabel() {
            const s = parseInt(this.durationSemesters) || 0;
            const y = s / 2;
            if (y === Math.floor(y)) return Math.floor(y) + (Math.floor(y) === 1 ? ' año' : ' años');
            return y.toFixed(1) + ' años';
        }
    }
}

function coordinatorEditSelect() {
    return {
        open: false,
        search: '',
        selectedId: '{{ old('coordinator_id', $program->coordinator_id ?? '') }}',
        selectedName: '',
        selectedEmail: '',
        get selectedInitials() {
            if (!this.selectedName) return '';
            return this.selectedName.substring(0, 2).toUpperCase();
        },
        init() {
            @php $currentCoordId = old('coordinator_id', $program->coordinator_id); @endphp
            @if($currentCoordId)
                @foreach($coordinators as $c)
                    @if($currentCoordId == $c->id)
                        this.selectedName = '{{ addslashes($c->name) }}';
                        this.selectedEmail = '{{ $c->email }}';
                    @endif
                @endforeach
            @endif
        },
        selectCoordinator(id, name, email) {
            this.selectedId = id;
            this.selectedName = name;
            this.selectedEmail = email;
        },
        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedEmail = '';
        }
    }
}
</script>
@endsection
