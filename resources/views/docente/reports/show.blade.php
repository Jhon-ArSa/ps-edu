@extends('layouts.app')

@section('title', 'Reporte — ' . $course->name)

@section('breadcrumb')
    @if(isset($routePrefix) && $routePrefix === 'admin.reports.course')
    <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">Reportes</a>
    @else
    <a href="{{ route('docente.courses.show', $course) }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
        {{ $course->name }}
    </a>
    @endif
    <svg class="w-3.5 h-3.5 text-gray-300 mx-1.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="font-semibold text-gray-700">Reporte</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reporte del Curso</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded-md">{{ $course->code }}</span>
                <span class="mx-1.5 text-gray-300">·</span>
                {{ $course->name }}
                @if($course->semesterPeriod)
                    <span class="mx-1.5 text-gray-300">·</span>{{ $course->semesterPeriod->name }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route($routePrefix . '.csv', $course) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Exportar CSV
            </a>
            <a href="{{ route($routePrefix . '.print', $course) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Imprimir / PDF
            </a>
        </div>
    </div>

    {{-- Stats cards del curso --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <div class="stat-card stat-card-blue group animate-fade-in-up delay-1">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Alumnos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $courseStats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25 group-hover:shadow-blue-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-violet group animate-fade-in-up delay-2">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Calificados</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $courseStats['scored'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">de {{ $courseStats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25 group-hover:shadow-violet-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-emerald group animate-fade-in-up delay-3">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Aprobados</p>
                        <p class="text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">{{ $courseStats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 group-hover:shadow-emerald-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-red group animate-fade-in-up delay-4">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Desaprobados</p>
                        <p class="text-3xl font-extrabold text-red-600 mt-2 tracking-tight">{{ $courseStats['failed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-red-500/25 group-hover:shadow-red-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-amber group animate-fade-in-up delay-5">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Promedio</p>
                        @php
                            $avg = $courseStats['average'];
                            $avgColor = $avg === null ? 'text-gray-300' : ($avg < 11 ? 'text-red-600' : ($avg < 14 ? 'text-amber-600' : 'text-emerald-600'));
                        @endphp
                        <p class="text-3xl font-extrabold {{ $avgColor }} mt-2 tracking-tight">{{ $avg !== null ? number_format($avg, 1) : '—' }}</p>
                        @if($courseStats['approval_rate'] !== null)
                            <p class="text-xs text-gray-400 mt-1">{{ $courseStats['approval_rate'] }}% aprobados</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25 group-hover:shadow-amber-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tarjetas de integración Juan / Jhon (visibles solo cuando estén disponibles) --}}
    @if($submissionStats['available'] || $attemptStats['available'])
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        @if($submissionStats['available'])
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Entregas (Tareas)</p>
                <p class="text-xs text-gray-500">
                    <span class="font-bold text-cyan-700">{{ $submissionStats['submitted'] }}</span> entregadas ·
                    <span class="font-bold text-emerald-700">{{ $submissionStats['graded'] }}</span> calificadas
                </p>
            </div>
        </div>
        @endif

        @if($attemptStats['available'])
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Evaluaciones</p>
                <p class="text-xs text-gray-500">
                    <span class="font-bold text-purple-700">{{ $attemptStats['total_attempts'] }}</span> intentos ·
                    <span class="font-bold text-emerald-700">{{ $attemptStats['passed'] }}</span> aprobados
                </p>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- Tabla alumno × ítem --}}
    @if($items->isEmpty())
    <div class="card text-center py-10">
        <p class="text-gray-400 text-sm">Aún no hay ítems de calificación en este curso.</p>
        <a href="{{ route('docente.grades.index', $course) }}" class="text-sm text-primary-600 hover:underline mt-2 inline-block">
            Ir a la libreta de notas →
        </a>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Notas por Alumno</h2>
            <span class="text-xs text-gray-400">{{ $studentRows->count() }} alumno{{ $studentRows->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="sticky left-0 bg-gray-50 text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[160px]">Alumno</th>
                        @foreach($items as $item)
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[90px]">
                            <div class="max-w-[90px] truncate mx-auto" title="{{ $item->name }}">{{ $item->name }}</div>
                            @if($item->weight > 0)
                                <div class="text-gray-400 normal-case font-normal">{{ $item->weight }}%</div>
                            @endif
                        </th>
                        @endforeach
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide min-w-[80px] bg-gray-100">Promedio</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($studentRows->sortByDesc('average') as $row)
                    @php
                        $avg = $row['average'];
                        $avgTextClass = $avg === null ? 'text-gray-300'
                            : ($avg < 11  ? 'text-red-600 font-bold'
                            : ($avg < 14  ? 'text-amber-600 font-bold'
                            : 'text-emerald-600 font-bold'));
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="sticky left-0 bg-white px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 shrink-0">
                                    {{ mb_strtoupper(mb_substr($row['student']->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 leading-tight">{{ $row['student']->name }}</div>
                                    @if($row['student']->alumnoProfile?->student_code)
                                        <div class="text-xs text-gray-400 font-mono">{{ $row['student']->alumnoProfile->student_code }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @foreach($items as $item)
                        @php
                            $score = $row['scores'][$item->id] ?? null;
                            $norm  = $score !== null ? round((min((float) $score, (float) $item->max_score) / $item->max_score) * 20, 1) : null;
                            $cellBg = $norm === null ? ''
                                : ($norm < 11  ? 'bg-red-50 text-red-700'
                                : ($norm < 14  ? 'bg-amber-50 text-amber-700'
                                : 'bg-emerald-50 text-emerald-700'));
                        @endphp
                        <td class="px-3 py-3 text-center">
                            @if($score !== null)
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $cellBg }}">
                                    {{ number_format($norm, 1) }}
                                    <span class="text-[10px] opacity-60 ml-0.5">({{ $score }}/{{ $item->max_score }})</span>
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-4 py-3 text-center bg-gray-50/50">
                            <span class="text-sm {{ $avgTextClass }}">
                                {{ $avg !== null ? number_format($avg, 1) : '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($avg !== null)
                                @if($row['approved'])
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Aprobado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        Desaprobado
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-300">Sin nota</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $items->count() + 3 }}" class="px-5 py-10 text-center text-gray-400">
                            No hay alumnos matriculados en este curso.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
