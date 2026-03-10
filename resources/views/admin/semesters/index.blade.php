@extends('layouts.app')

@section('title', 'Semestres Académicos')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Semestres Académicos</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Semestres Académicos</h1>
            <p class="text-sm text-gray-500">Gestión de períodos académicos del programa de posgrado (3 años / 6 semestres)</p>
        </div>
        <a href="{{ route('admin.semesters.create') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm shadow-primary-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo semestre
        </a>
    </div>

    {{-- Active Semester Banner --}}
    @if($activeSemester)
    <div class="relative bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl px-6 py-5 text-white shadow-lg shadow-emerald-500/15 overflow-hidden animate-fade-in-up">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">Semestre activo</p>
                    <h2 class="text-2xl font-extrabold tracking-tight mt-0.5">{{ $activeSemester->name }}</h2>
                    <p class="text-emerald-100/80 text-sm mt-0.5">{{ $activeSemester->date_range }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                {{-- Progress bar --}}
                <div class="hidden md:block min-w-[200px]">
                    <div class="flex items-center justify-between text-xs text-emerald-100 mb-1.5">
                        <span>Progreso</span>
                        <span class="font-bold text-white">{{ $activeSemester->progress_percent }}%</span>
                    </div>
                    <div class="h-2.5 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all duration-500"
                             style="width: {{ $activeSemester->progress_percent }}%"></div>
                    </div>
                    <p class="text-[10px] text-emerald-100/60 mt-1">{{ $activeSemester->duration_weeks }} semanas de duración</p>
                </div>
                <a href="{{ route('admin.semesters.show', $activeSemester) }}"
                   class="shrink-0 inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all">
                    Ver detalle
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline Visual (3-year program) --}}
    @if($timeline->isNotEmpty())
    <div class="card animate-fade-in-up delay-1">
        <div class="card-header flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Línea de Tiempo del Programa</h2>
                    <p class="text-xs text-gray-400">Programa de posgrado — 3 años / 6 semestres</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-[10px] font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> En curso</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Planificado</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> Finalizado</span>
            </div>
        </div>
        <div class="p-6">
            <div class="relative">
                {{-- Timeline bar --}}
                <div class="absolute top-8 left-0 right-0 h-1 bg-gray-100 rounded-full hidden sm:block"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    @php $counter = 0; @endphp
                    @foreach($timeline as $year => $yearSemesters)
                        @foreach($yearSemesters as $sem)
                            @php $counter++; @endphp
                            <a href="{{ route('admin.semesters.show', $sem) }}"
                               class="group relative flex flex-col items-center text-center p-4 rounded-xl border-2 transition-all duration-300 hover:shadow-lg hover:-translate-y-1
                                      {{ $sem->status === 'active'
                                          ? 'border-emerald-300 bg-emerald-50/50 shadow-md shadow-emerald-500/10'
                                          : ($sem->status === 'planned'
                                              ? 'border-blue-200 bg-blue-50/30 hover:border-blue-300'
                                              : 'border-gray-200 bg-gray-50/50 hover:border-gray-300') }}">

                                {{-- Cycle number --}}
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-extrabold mb-2 transition-transform group-hover:scale-110
                                    {{ $sem->status === 'active'
                                        ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                                        : ($sem->status === 'planned'
                                            ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20'
                                            : 'bg-gray-300 text-white') }}">
                                    {{ $counter }}
                                </div>

                                <p class="text-sm font-bold text-gray-800">{{ $sem->name }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $sem->start_date->isoFormat('MMM YYYY') }}</p>

                                @if($sem->status === 'active')
                                    <div class="mt-2 w-full">
                                        <div class="h-1.5 bg-emerald-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $sem->progress_percent }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-2 flex items-center gap-1 text-[10px] font-medium text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $sem->courses_count }} cursos
                                </div>

                                @if($sem->status === 'active')
                                    <span class="absolute -top-2 -right-2 flex h-5 w-5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-5 w-5 bg-emerald-500 items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.semesters.index') }}"
          class="flex flex-col sm:flex-row gap-3 bg-white rounded-xl border border-gray-200 p-4">
        <select name="status"
                class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">Todos los estados</option>
            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>En curso</option>
            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planificado</option>
            <option value="closed"  {{ request('status') === 'closed'  ? 'selected' : '' }}>Finalizado</option>
        </select>
        <select name="year"
                class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">Todos los años</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Filtrar</button>
        @if(request()->hasAny(['status', 'year']))
            <a href="{{ route('admin.semesters.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Limpiar</a>
        @endif
    </form>

    {{-- Semesters Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($semesters as $semester)
        <div class="card group hover:shadow-lg hover:border-{{ $semester->status_color }}-200 transition-all duration-300 overflow-hidden">
            {{-- Color top bar --}}
            <div class="h-1 {{ $semester->is_active ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : ($semester->is_planned ? 'bg-gradient-to-r from-blue-400 to-indigo-500' : 'bg-gray-200') }}"></div>

            <div class="p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-extrabold text-gray-900">{{ $semester->name }}</h3>
                            @if($semester->is_active)
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $semester->is_active ? 'bg-emerald-100 text-emerald-700' : ($semester->is_planned ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $semester->is_active ? 'bg-emerald-500' : ($semester->is_planned ? 'bg-blue-500' : 'bg-gray-400') }}"></span>
                            {{ $semester->status_label }}
                        </span>
                    </div>
                    {{-- Actions dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-100 p-1.5 z-20">
                            <a href="{{ route('admin.semesters.show', $semester) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Ver detalle
                            </a>
                            <a href="{{ route('admin.semesters.edit', $semester) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                            @if(!$semester->is_active && !$semester->is_closed)
                            <form method="POST" action="{{ route('admin.semesters.activate', $semester) }}"
                                  data-confirm="¿Activar este semestre? Los semestres anteriores serán cerrados."
                                  data-confirm-color="#10b981">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-emerald-600 hover:bg-emerald-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Activar
                                </button>
                            </form>
                            @endif
                            @if($semester->is_active)
                            <form method="POST" action="{{ route('admin.semesters.close', $semester) }}"
                                  data-confirm="¿Cerrar este semestre? Ya no será el semestre activo."
                                  data-confirm-color="#f59e0b">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-amber-600 hover:bg-amber-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Cerrar
                                </button>
                            </form>
                            @endif
                            @if($semester->courses_count === 0)
                            <form method="POST" action="{{ route('admin.semesters.destroy', $semester) }}"
                                  data-confirm="¿Eliminar este semestre permanentemente?">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Date range --}}
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $semester->date_range }}
                </div>

                @if($semester->description)
                    <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $semester->description }}</p>
                @endif

                {{-- Progress bar for active --}}
                @if($semester->is_active)
                <div class="mb-4">
                    <div class="flex items-center justify-between text-[10px] font-medium text-gray-400 mb-1">
                        <span>Progreso del semestre</span>
                        <span class="text-emerald-600 font-bold">{{ $semester->progress_percent }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full transition-all duration-500" style="width: {{ $semester->progress_percent }}%"></div>
                    </div>
                </div>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $semester->courses_count }}</p>
                            <p class="text-[10px] text-gray-400">Cursos</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $semester->active_courses_count }}</p>
                            <p class="text-[10px] text-gray-400">Activos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 xl:col-span-3">
            <div class="bg-white rounded-xl border border-gray-200 py-16 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay semestres registrados</p>
                <p class="text-xs text-gray-400 mb-4">Crea el primer semestre académico para organizar los cursos del programa.</p>
                <a href="{{ route('admin.semesters.create') }}"
                   class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear primer semestre
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($semesters->hasPages())
    <div class="flex justify-center">{{ $semesters->links() }}</div>
    @endif

</div>
@endsection
