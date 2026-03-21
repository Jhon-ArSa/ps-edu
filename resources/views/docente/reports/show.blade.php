@extends('layouts.app')

@section('title', 'Reporte - ' . $course->name)

@section('breadcrumb')
    @if(isset($routePrefix) && $routePrefix === 'admin.reports.course')
    <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">Reportes</a>
    @else
    <a href="{{ route('docente.courses.show', $course) }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">{{ $course->name }}</a>
    @endif
    <svg class="w-3.5 h-3.5 text-gray-300 mx-1.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="font-semibold text-gray-700">Reporte</span>
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reporte del Curso</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded-md">{{ $course->code }}</span>
                <span class="mx-1.5 text-gray-300">-</span>
                {{ $course->name }}
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route($routePrefix . '.csv', $course) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Exportar CSV
            </a>
            <a href="{{ route($routePrefix . '.print', $course) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Imprimir
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Alumnos</p>
            <p class="text-2xl font-bold text-gray-900">{{ $courseStats['total_students'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Tareas</p>
            <p class="text-2xl font-bold text-gray-900">{{ $courseStats['total_tasks'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Entregadas</p>
            <p class="text-2xl font-bold text-blue-600">{{ $courseStats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Revisadas</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $courseStats['reviewed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Sin Entrega</p>
            <p class="text-2xl font-bold text-amber-600">{{ $courseStats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Cumplimiento</p>
            <p class="text-2xl font-bold text-gray-900">{{ $courseStats['submission_rate'] }}%</p>
        </div>
    </div>

    @if($attemptStats['available'])
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-sm font-semibold text-gray-800">Evaluaciones</p>
        <p class="text-sm text-gray-500 mt-1">
            Intentos enviados: <span class="font-semibold text-gray-800">{{ $attemptStats['total_attempts'] }}</span>
            <span class="mx-2 text-gray-300">|</span>
            Intentos completados: <span class="font-semibold text-gray-800">{{ $attemptStats['passed'] }}</span>
        </p>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">Estado de Entregas por Alumno</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[220px]">Alumno</th>
                        @foreach($tasks as $task)
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[110px]">
                            S{{ $task->week_number }}
                        </th>
                        @endforeach
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[110px]">Entregadas</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[110px]">Revisadas</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[110px]">Avance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($studentRows as $row)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $row['student']->name }}</div>
                            <div class="text-xs text-gray-400">{{ $row['student']->alumnoProfile?->student_code ?? '-' }}</div>
                        </td>

                        @foreach($tasks as $task)
                            @php $submission = $row['submissions'][$task->id] ?? null; @endphp
                            <td class="px-3 py-3 text-center">
                                @if($submission)
                                    @if($submission->isGraded())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Revisada</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Entregada</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Sin entrega</span>
                                @endif
                            </td>
                        @endforeach

                        <td class="px-3 py-3 text-center font-semibold text-blue-700">{{ $row['submitted_count'] }}/{{ $tasks->count() }}</td>
                        <td class="px-3 py-3 text-center font-semibold text-emerald-700">{{ $row['reviewed_count'] }}/{{ $tasks->count() }}</td>
                        <td class="px-3 py-3 text-center font-semibold text-gray-700">{{ $row['submission_pct'] }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $tasks->count() + 4 }}" class="px-5 py-10 text-center text-gray-400">
                            No hay alumnos matriculados para este reporte.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
