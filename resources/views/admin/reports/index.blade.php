@extends('layouts.app')

@section('title', 'Reportes')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Reportes</span>
@endsection

@section('content')
<div class="space-y-6" x-data="reportFilters()">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reportes del Sistema</h1>
            <p class="text-sm text-gray-500 mt-0.5">Estadísticas académicas por semestre, programa y curso</p>
        </div>
        @if($semester)
        <div class="flex items-center gap-2">
            <button @click="exportCsv()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Exportar CSV
            </button>
            <button @click="printReport()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Imprimir / PDF
            </button>
        </div>
        @endif
    </div>

    {{-- Filtros Avanzados --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <form method="GET" action="{{ route('admin.reports.index') }}" id="filterForm">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                {{-- Semestre --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Semestre</label>
                    <select name="semester_id" @change="$el.form.submit()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
                        <option value="">— Seleccionar —</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semester?->id == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }}
                                @if($sem->status === 'active') · En curso @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Programas Multi-Select --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Programas
                        <span class="font-normal text-gray-400">(selecciona uno o varios)</span>
                    </label>
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400 text-left flex items-center justify-between">
                            <span x-text="selectedProgramIds.length > 0 ? selectedProgramIds.length + ' programa(s) seleccionado(s)' : 'Todos los programas'" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            @foreach($programs as $prog)
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="program_ids[]" value="{{ $prog->id }}"
                                       :checked="selectedProgramIds.includes({{ $prog->id }})"
                                       @change="syncProgram({{ $prog->id }}, $event.target.checked)"
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <span class="text-sm text-gray-700">{{ $prog->name }}</span>
                                <span class="text-xs text-gray-400 font-mono">{{ $prog->code }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        Aplicar Filtros
                    </button>
                    @if(!empty($selectedProgramIds) || !empty($selectedCourseIds))
                    <a href="{{ route('admin.reports.index', ['semester_id' => $semester?->id]) }}"
                       class="px-3 py-2.5 text-gray-500 hover:text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition-colors">
                        Limpiar
                    </a>
                    @endif
                </div>
            </div>

            {{-- Cursos Multi-Select (solo si hay semestre) --}}
            @if($semester && $availableCourses->count() > 0)
            <div class="border-t border-gray-100 pt-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Cursos específicos
                    <span class="font-normal text-gray-400">(opcional - deja vacío para todos)</span>
                </label>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400 text-left flex items-center justify-between">
                        <span x-text="selectedCourseIds.length > 0 ? selectedCourseIds.length + ' curso(s) seleccionado(s)' : 'Todos los cursos'" class="truncate"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                         class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="course in filteredCourses" :key="course.id">
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="course_ids[]" :value="course.id"
                                       :checked="selectedCourseIds.includes(course.id)"
                                       @change="syncCourse(course.id, $event.target.checked)"
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm text-gray-700 block truncate" x-text="course.name"></span>
                                    <span class="text-xs text-gray-400" x-text="(course.program_code || 'Sin prog.') + ' · ' + course.code"></span>
                                </div>
                            </label>
                        </template>
                        <div x-show="filteredCourses.length === 0" class="px-3 py-4 text-sm text-gray-400 text-center">
                            No hay cursos para los programas seleccionados
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Resumen de filtros activos --}}
            @if($semester && (!empty($selectedProgramIds) || !empty($selectedCourseIds)))
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs text-gray-500 py-1">Filtros activos:</span>
                @foreach($selectedPrograms as $prog)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full">
                    {{ $prog->code }}
                    <button type="button" @click="removeProgram({{ $prog->id }})" class="hover:text-primary-900">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </span>
                @endforeach
                @foreach($selectedCourses as $course)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-violet-100 text-violet-700 text-xs font-medium rounded-full">
                    {{ \Str::limit($course->name, 15) }}
                    <button type="button" @click="removeCourse({{ $course->id }})" class="hover:text-violet-900">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </span>
                @endforeach
            </div>
            @endif

        </form>
    </div>

    {{-- Stats globales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Alumnos Activos', 'value' => $globalStats['total_students'], 'color' => 'blue', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772'],
            ['label' => 'Docentes Activos', 'value' => $globalStats['total_teachers'], 'color' => 'violet', 'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814'],
            ['label' => 'Cursos Activos', 'value' => $globalStats['active_courses'], 'color' => 'emerald', 'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292'],
            ['label' => 'Matrículas Activas', 'value' => $globalStats['active_enrollments'], 'color' => 'amber', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-200/60 p-4 lg:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-3xl lg:text-4xl font-bold text-gray-900 mt-1 tabular-nums leading-none">{{ $stat['value'] }}</p>
                </div>
                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-{{ $stat['color'] }}-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <div class="h-1 w-10 bg-gradient-to-r from-{{ $stat['color'] }}-400 to-{{ $stat['color'] }}-500 rounded-full"></div>
        </div>
        @endforeach
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
            <h2 class="text-base font-bold text-gray-900 mb-4">
                Resumen del semestre {{ $semester->name }}
                @if(!empty($selectedProgramIds) && $selectedPrograms->count() > 0)
                    <span class="text-primary-600 font-normal">·
                        {{ $selectedPrograms->count() }} programa(s)
                    </span>
                @endif
                @if(!empty($selectedCourseIds))
                    <span class="text-violet-600 font-normal">·
                        {{ count($selectedCourseIds) }} curso(s)
                    </span>
                @endif
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-center">
                @foreach([
                    ['label' => 'Programas', 'value' => $semesterStats['programs'], 'color' => 'text-indigo-600'],
                    ['label' => 'Cursos', 'value' => $semesterStats['courses'], 'color' => 'text-primary-600'],
                    ['label' => 'Docentes', 'value' => $semesterStats['teachers'], 'color' => 'text-violet-600'],
                    ['label' => 'Matrículas', 'value' => $semesterStats['enrollments'], 'color' => 'text-blue-600'],
                    ['label' => 'Materiales', 'value' => $semesterStats['materials'], 'color' => 'text-emerald-600'],
                ] as $stat)
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-2xl font-extrabold {{ $stat['color'] }} tabular-nums">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Gráficos (sin notas) --}}
        @if($chartData && $programReports->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico: Alumnos por Programa --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Alumnos por Programa</h3>
                <div class="h-64">
                    <canvas id="chartStudentsByProgram"></canvas>
                </div>
            </div>

            {{-- Gráfico: Materiales por Programa --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Materiales por Programa</h3>
                <div class="h-64">
                    <canvas id="chartMaterialsByProgram"></canvas>
                </div>
            </div>

            {{-- Gráfico: Cursos por Programa --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Cursos por Programa</h3>
                <div class="h-64">
                    <canvas id="chartCoursesByProgram"></canvas>
                </div>
            </div>

            {{-- Gráfico: Top 5 Cursos por Alumnos --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Top 5 Cursos con más Alumnos</h3>
                <div class="h-64">
                    <canvas id="chartTopCourses"></canvas>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabla de Cursos por Programa --}}
        @if($programReports->isNotEmpty())
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Detalle por Programa y Curso</h2>

            @foreach($programReports as $programReport)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                {{-- Header del programa --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $programReport['program_name'] }}</h3>
                                @if($programReport['program_code'])
                                <span class="text-xs text-gray-500 font-mono">{{ $programReport['program_code'] }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-center">
                            <div>
                                <p class="text-xl font-bold text-primary-600">{{ $programReport['courses_count'] }}</p>
                                <p class="text-xs text-gray-500">Cursos</p>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-blue-600">{{ $programReport['total_students'] }}</p>
                                <p class="text-xs text-gray-500">Alumnos</p>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-emerald-600">{{ $programReport['total_materials'] }}</p>
                                <p class="text-xs text-gray-500">Materiales</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de cursos del programa --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Curso / Docente</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Código</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Alumnos</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Semanas</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Materiales</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Tareas</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($programReport['courses'] as $report)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-gray-900">{{ $report['course']->name }}</div>
                                    @if($report['course']->teacher)
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $report['course']->teacher->name }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="text-xs text-gray-400 font-mono">{{ $report['course']->code }}</span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="font-semibold text-gray-900">{{ $report['active_students'] }}</span>
                                </td>
                                <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['weeks'] }}</td>
                                <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['materials'] }}</td>
                                <td class="px-3 py-3.5 text-center text-gray-600">{{ $report['tasks'] }}</td>
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
            @endforeach
        </div>
        @else
            <div class="card text-center py-10">
                <p class="text-gray-400">No hay cursos registrados con los filtros seleccionados.</p>
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
function reportFilters() {
    return {
        selectedProgramIds: @json(array_map('intval', $selectedProgramIds ?? [])),
        selectedCourseIds: @json(array_map('intval', $selectedCourseIds ?? [])),
        allCourses: @json($coursesForFilter ?? []),

        get filteredCourses() {
            if (this.selectedProgramIds.length === 0) {
                return this.allCourses;
            }
            return this.allCourses.filter(c => this.selectedProgramIds.includes(c.program_id));
        },

        syncProgram(id, checked) {
            const idx = this.selectedProgramIds.indexOf(id);
            if (checked && idx === -1) {
                this.selectedProgramIds.push(id);
            } else if (!checked && idx > -1) {
                this.selectedProgramIds.splice(idx, 1);
            }
            // Limpiar cursos que ya no pertenecen a programas seleccionados
            if (this.selectedProgramIds.length > 0) {
                this.selectedCourseIds = this.selectedCourseIds.filter(cid => {
                    const course = this.allCourses.find(c => c.id === cid);
                    return course && this.selectedProgramIds.includes(course.program_id);
                });
            }
        },

        syncCourse(id, checked) {
            const idx = this.selectedCourseIds.indexOf(id);
            if (checked && idx === -1) {
                this.selectedCourseIds.push(id);
            } else if (!checked && idx > -1) {
                this.selectedCourseIds.splice(idx, 1);
            }
        },

        removeProgram(id) {
            this.selectedProgramIds = this.selectedProgramIds.filter(p => p !== id);
            // Limpiar cursos que ya no pertenecen a programas seleccionados
            if (this.selectedProgramIds.length > 0) {
                this.selectedCourseIds = this.selectedCourseIds.filter(cid => {
                    const course = this.allCourses.find(c => c.id === cid);
                    return course && this.selectedProgramIds.includes(course.program_id);
                });
            }
            document.getElementById('filterForm').submit();
        },

        removeCourse(id) {
            this.selectedCourseIds = this.selectedCourseIds.filter(c => c !== id);
            document.getElementById('filterForm').submit();
        },

        buildExportUrl(baseUrl) {
            const params = new URLSearchParams();
            @if($semester)
            params.set('semester_id', '{{ $semester->id }}');
            @endif
            this.selectedProgramIds.forEach(id => params.append('program_ids[]', id));
            this.selectedCourseIds.forEach(id => params.append('course_ids[]', id));
            return baseUrl + '?' + params.toString();
        },

        exportCsv() {
            window.location.href = this.buildExportUrl('{{ route('admin.reports.csv') }}');
        },

        printReport() {
            window.open(this.buildExportUrl('{{ route('admin.reports.print') }}'), '_blank');
        }
    }
}
</script>

@if($semester && $chartData && $programReports->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const backgroundColors = [
        'rgba(37, 99, 235, 0.7)',
        'rgba(16, 185, 129, 0.7)',
        'rgba(245, 158, 11, 0.7)',
        'rgba(139, 92, 246, 0.7)',
        'rgba(236, 72, 153, 0.7)',
        'rgba(6, 182, 212, 0.7)',
        'rgba(99, 102, 241, 0.7)',
        'rgba(34, 197, 94, 0.7)',
    ];

    // Gráfico: Alumnos por Programa
    const studentsByProgram = @json($chartData['studentsByProgram']);
    if (studentsByProgram.length > 0) {
        new Chart(document.getElementById('chartStudentsByProgram'), {
            type: 'doughnut',
            data: {
                labels: studentsByProgram.map(d => d.name),
                datasets: [{
                    data: studentsByProgram.map(d => d.value),
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, padding: 15 } }
                }
            }
        });
    }

    // Gráfico: Materiales por Programa
    const materialsByProgram = @json($chartData['materialsByProgram']);
    if (materialsByProgram.length > 0) {
        new Chart(document.getElementById('chartMaterialsByProgram'), {
            type: 'bar',
            data: {
                labels: materialsByProgram.map(d => d.name),
                datasets: [{
                    label: 'Materiales',
                    data: materialsByProgram.map(d => d.value),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Gráfico: Cursos por Programa
    const coursesByProgram = @json($chartData['coursesByProgram']);
    if (coursesByProgram.length > 0) {
        new Chart(document.getElementById('chartCoursesByProgram'), {
            type: 'bar',
            data: {
                labels: coursesByProgram.map(d => d.name),
                datasets: [{
                    label: 'Cursos',
                    data: coursesByProgram.map(d => d.value),
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Gráfico: Top 5 Cursos
    const topCourses = @json($chartData['topCoursesByStudents']);
    if (topCourses.length > 0) {
        new Chart(document.getElementById('chartTopCourses'), {
            type: 'bar',
            data: {
                labels: topCourses.map(d => d.name),
                datasets: [{
                    label: 'Alumnos',
                    data: topCourses.map(d => d.value),
                    backgroundColor: 'rgba(139, 92, 246, 0.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endif
@endpush
