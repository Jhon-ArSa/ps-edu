@extends('layouts.app')

@section('title', 'Editar Semestre ' . $semester->name)

@section('breadcrumb')
    <a href="{{ route('admin.semesters.index') }}" class="hover:text-primary-600">Semestres</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar: {{ $semester->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Editar semestre {{ $semester->name }}</h2>
                <p class="text-xs text-gray-400">Modifica las fechas y el estado del período académico</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.semesters.update', $semester) }}" class="space-y-5" x-data="{
            year: '{{ old('year', $semester->year) }}',
            period: '{{ old('period', $semester->period) }}',
            get name() { return this.year + '-' + this.period; }
        }">
            @csrf
            @method('PUT')

            {{-- Name preview --}}
            <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-center gap-3 border border-gray-100">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                <p class="text-xl font-extrabold text-gray-800 tracking-tight" x-text="name"></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año <span class="text-red-500">*</span></label>
                    <input type="number" name="year" x-model="year" required min="2020" max="2100"
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('year') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Período <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                               :class="period === 'I' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600'">
                            <input type="radio" name="period" value="I" x-model="period" class="sr-only">
                            <span class="text-sm font-bold">Semestre I</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                               :class="period === 'II' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600'">
                            <input type="radio" name="period" value="II" x-model="period" class="sr-only">
                            <span class="text-sm font-bold">Semestre II</span>
                        </label>
                    </div>
                    @error('period') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $semester->start_date->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('start_date') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de fin <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $semester->end_date->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('end_date') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="grid grid-cols-3 gap-2">
                        @php $currentStatus = old('status', $semester->status); @endphp
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ $currentStatus === 'planned' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="planned" {{ $currentStatus === 'planned' ? 'checked' : '' }}
                                   class="text-blue-500 focus:ring-blue-400">
                            <div>
                                <span class="text-xs font-bold {{ $currentStatus === 'planned' ? 'text-blue-700' : 'text-gray-600' }}">Planificado</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ $currentStatus === 'active' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="active" {{ $currentStatus === 'active' ? 'checked' : '' }}
                                   class="text-emerald-500 focus:ring-emerald-400">
                            <div>
                                <span class="text-xs font-bold {{ $currentStatus === 'active' ? 'text-emerald-700' : 'text-gray-600' }}">En curso</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ $currentStatus === 'closed' ? 'border-gray-400 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="closed" {{ $currentStatus === 'closed' ? 'checked' : '' }}
                                   class="text-gray-500 focus:ring-gray-400">
                            <div>
                                <span class="text-xs font-bold {{ $currentStatus === 'closed' ? 'text-gray-700' : 'text-gray-600' }}">Finalizado</span>
                            </div>
                        </label>
                    </div>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <textarea name="description" rows="3" maxlength="500"
                              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('description', $semester->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.semesters.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-primary-500/20">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
