@extends('layouts.app')

@section('title', 'Nuevo Semestre')

@section('breadcrumb')
    <a href="{{ route('admin.semesters.index') }}" class="hover:text-primary-600">Semestres</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Nuevo</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Info banner --}}
    <div class="bg-blue-50 border border-blue-200/60 rounded-xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-blue-800">Programa de Posgrado — 3 años</p>
            <p class="text-xs text-blue-600 mt-0.5">Cada maestría dura 6 semestres (3 años). Los semestres se organizan como <strong>Año-I</strong> (inicio de año) y <strong>Año-II</strong> (mediados de año). Solo puede haber un semestre activo a la vez.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Crear nuevo semestre</h2>
                <p class="text-xs text-gray-400">Define el período académico y sus fechas</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.semesters.store') }}" class="space-y-5" x-data="{
            year: '{{ old('year', $suggestedYear) }}',
            period: '{{ old('period', $suggestedPeriod) }}',
            get name() { return this.year + '-' + this.period; }
        }">
            @csrf

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
                        <label class="relative flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                               :class="period === 'I' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600'">
                            <input type="radio" name="period" value="I" x-model="period" class="sr-only">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>
                            <span class="text-sm font-bold">Semestre I</span>
                            <span class="text-[10px] font-medium opacity-60 absolute -bottom-0.5 right-2">Mar–Jul</span>
                        </label>
                        <label class="relative flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                               :class="period === 'II' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600'">
                            <input type="radio" name="period" value="II" x-model="period" class="sr-only">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <span class="text-sm font-bold">Semestre II</span>
                            <span class="text-[10px] font-medium opacity-60 absolute -bottom-0.5 right-2">Ago–Dic</span>
                        </label>
                    </div>
                    @error('period') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('start_date') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de fin <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('end_date') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ old('status', 'planned') === 'planned' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="planned" {{ old('status', 'planned') === 'planned' ? 'checked' : '' }}
                                   class="text-blue-500 focus:ring-blue-400">
                            <div>
                                <span class="text-xs font-bold {{ old('status', 'planned') === 'planned' ? 'text-blue-700' : 'text-gray-600' }}">Planificado</span>
                                <p class="text-[10px] text-gray-400">En preparación</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ old('status') === 'active' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="active" {{ old('status') === 'active' ? 'checked' : '' }}
                                   class="text-emerald-500 focus:ring-emerald-400">
                            <div>
                                <span class="text-xs font-bold {{ old('status') === 'active' ? 'text-emerald-700' : 'text-gray-600' }}">En curso</span>
                                <p class="text-[10px] text-gray-400">Semestre actual</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 cursor-pointer transition-all
                                      {{ old('status') === 'closed' ? 'border-gray-400 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="closed" {{ old('status') === 'closed' ? 'checked' : '' }}
                                   class="text-gray-500 focus:ring-gray-400">
                            <div>
                                <span class="text-xs font-bold {{ old('status') === 'closed' ? 'text-gray-700' : 'text-gray-600' }}">Finalizado</span>
                                <p class="text-[10px] text-gray-400">Ya terminó</p>
                            </div>
                        </label>
                    </div>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <textarea name="description" rows="3" maxlength="500"
                              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none"
                              placeholder="Ej: Primer semestre del programa de Maestría en Ciencias de la Educación — Promoción 2026">{{ old('description') }}</textarea>
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
                    Crear semestre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
