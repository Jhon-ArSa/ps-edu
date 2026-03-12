@extends('layouts.app')

@section('title', 'Reportes')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Reportes</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reportes del Sistema</h1>
            <p class="text-sm text-gray-500 mt-0.5">Estadísticas académicas y actividad por semestre</p>
        </div>
        <div class="flex items-center gap-2">
            @if($semester)
                {{-- Exportar CSV --}}
                <a href="{{ route('admin.reports.csv', ['semester_id' => $semester->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Exportar CSV
                </a>
                {{-- Imprimir / PDF --}}
                <a href="{{ route('admin.reports.print', ['semester_id' => $semester->id]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                    </svg>
                    Imprimir / PDF
                </a>
            @endif
        </div>
    </div>

    {{-- Selector de semestre --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Semestre:</label>
            <select name="semester_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400 min-w-[160px]">
                <option value="">— Seleccionar —</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ $semester?->id == $sem->id ? 'selected' : '' }}>
                        {{ $sem->name }}
                        @if($sem->status === 'active') · En curso @endif
                    </option>
                @endforeach
            </select>
            @if($semester)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $semester->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $semester->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $semester->status === 'active' ? 'En curso' : ucfirst($semester->status) }}
                </span>
                @if($semester->start_date)
                    <span class="text-xs text-gray-400">
                        {{ $semester->start_date->format('d M Y') }} — {{ $semester->end_date?->format('d M Y') ?? 'Sin fin' }}
                    </span>
                @endif
            @endif
        </form>
    </div>

    {{-- Stats globales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Alumnos --}}
        <div class="bg-white rounded-xl border border-gray-200/60 p-4 lg:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">
                        Alumnos Activos
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold text-gray-900 mt-1 tabular-nums leading-none">
                        {{ $globalStats['total_students'] }}
                    </p>
                </div>

                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772"/>
                    </svg>
                </div>
            </div>

            <div class="h-1 w-10 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full"></div>
        </div>


        {{-- Docentes --}}
        <div class="bg-white rounded-xl border border-gray-200/60 p-4 lg:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">
                        Docentes Activos
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold text-gray-900 mt-1 tabular-nums leading-none">
                        {{ $globalStats['total_teachers'] }}
                    </p>
                </div>

                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814"/>
                    </svg>
                </div>
            </div>

            <div class="h-1 w-10 bg-gradient-to-r from-violet-400 to-violet-500 rounded-full"></div>
        </div>


        {{-- Cursos --}}
        <div class="bg-white rounded-xl border border-gray-200/60 p-4 lg:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">
                        Cursos Activos
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold text-gray-900 mt-1 tabular-nums leading-none">
                        {{ $globalStats['active_courses'] }}
                    </p>
                </div>

                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292"/>
                    </svg>
                </div>
            </div>

            <div class="h-1 w-10 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full"></div>
        </div>


        {{-- Matrículas --}}
        <div class="bg-white rounded-xl border border-gray-200/60 p-4 lg:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">
                        Matrículas Activas
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold text-gray-900 mt-1 tabular-nums leading-none">
                        {{ $globalStats['active_enrollments'] }}
                    </p>
                </div>

                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108"/>
                    </svg>
                </div>
            </div>

            <div class="h-1 w-10 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full"></div>
        </div>

    </div>

    @if(! $semester)
        <div class="card text-center py-16">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 10.5v7.5"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Selecciona un semestre para ver el reporte detallado</p>
        </div>
    @else

        {{-- Stats del semestre --}}
        @if($semesterStats)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-bold text-gray-900 mb-4">Resumen del semestre {{ $semester->name }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-center">
                @foreach([
                    ['label' => 'Cursos', 'value' => $semesterStats['courses'], 'color' => 'text-primary-600'],
                    ['label' => 'Docentes', 'value' => $semesterStats['teachers'], 'color' => 'text-violet-600'],
                    ['label' => 'Matrículas', 'value' => $semesterStats['enrollments'], 'color' => 'text-blue-600'],
                    ['label' => 'Materiales', 'value' => $semesterStats['materials'], 'color' => 'text-emerald-600'],
                    ['label' => 'Tareas', 'value' => $semesterStats['tasks'], 'color' => 'text-amber-600'],
                ] as $stat)
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-2xl font-extrabold {{ $stat['color'] }} tabular-nums">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tabla de cursos --}}
        @if($courseReports->isEmpty())
            <div class="card text-center py-10">
                <p class="text-gray-400">No hay cursos registrados en este semestre.</p>
            </div>
        @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Actividad por Curso</h2>
                <span class="text-xs text-gray-400">{{ $courseReports->count() }} curso{{ $courseReports->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Curso / Docente</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Alumnos</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Semanas</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Materiales</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Tareas</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Promedio</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Aprobados</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($courseReports as $report)
                        @php
                            $avg   = $report['average'];
                            $avgClass = $avg === null ? 'text-gray-300'
                                : ($avg < 11  ? 'text-red-600 font-semibold'
                                : ($avg < 14  ? 'text-amber-600 font-semibold'
                                : 'text-emerald-600 font-semibold'));
                            $approvalPct = $report['approval_rate'];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-gray-900">{{ $report['course']->name }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400 font-mono">{{ $report['course']->code }}</span>
                                    @if($report['course']->teacher)
                                        <span class="text-gray-300">·</span>
                                        <span class="text-xs text-gray-500">{{ $report['course']->teacher->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3.5 text-center">
                                <span class="font-semibold text-gray-900">{{ $report['active_students'] }}</span>
                            </td>
                            <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['weeks'] }}</td>
                            <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['materials'] }}</td>
                            <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['tasks'] }}</td>
                            <td class="px-3 py-3.5 text-center">
                                <span class="text-sm {{ $avgClass }}">
                                    {{ $avg !== null ? number_format($avg, 1) : '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-3.5 text-center">
                                @if($approvalPct !== null)
                                    @php
                                        $barColor = $approvalPct >= 70 ? 'bg-emerald-500'
                                            : ($approvalPct >= 50 ? 'bg-amber-400' : 'bg-red-400');
                                    @endphp
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs font-semibold {{ $approvalPct >= 70 ? 'text-emerald-700' : ($approvalPct >= 50 ? 'text-amber-700' : 'text-red-700') }}">
                                            {{ $approvalPct }}%
                                        </span>
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="{{ $barColor }} h-full rounded-full transition-all" style="width: {{ $approvalPct }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.reports.course.show', $report['course']) }}"
                                   class="text-xs text-primary-600 hover:text-primary-700 font-medium hover:underline">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif

</div>
@endsection
