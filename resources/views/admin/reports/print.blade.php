<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte — {{ $semester?->name ?? 'General' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #fff; }

        .page-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 24px 16px; border-bottom: 2px solid #1e40af; }
        .institution { font-size: 16px; font-weight: 700; color: #1e40af; }
        .report-title { font-size: 14px; font-weight: 700; margin-top: 2px; }
        .report-meta { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .print-date { text-align: right; font-size: 11px; color: #6b7280; }

        .filters-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; margin: 16px 24px; font-size: 11px; color: #4b5563; }
        .filters-box strong { color: #111827; }

        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; padding: 16px 24px; }
        .stat-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; text-align: center; }
        .stat-box .value { font-size: 20px; font-weight: 800; color: #1e40af; }
        .stat-box .label { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .program-section { padding: 0 24px 20px; page-break-inside: avoid; }
        .program-header { background: #eff6ff; border: 1px solid #dbeafe; border-radius: 6px 6px 0 0; padding: 10px 14px; margin-bottom: 0; }
        .program-header h2 { font-size: 13px; font-weight: 700; color: #1e40af; margin: 0; }
        .program-header .meta { font-size: 10px; color: #6b7280; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border: 1px solid #e5e7eb; }
        thead th.center { text-align: center; }
        tbody td { padding: 6px 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        tbody td.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .footer { padding: 12px 24px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; display: flex; justify-content: space-between; margin-top: 20px; }

        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1e40af; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(30,64,175,0.35); }
        .print-btn:hover { background: #1d4ed8; }

        @media print {
            .print-btn { display: none !important; }
            body { font-size: 11px; }
            .page-header { padding: 12px 16px 10px; }
            .stats-grid { padding: 10px 16px; }
            .program-section { padding: 0 16px 14px; }
            .filters-box { margin: 12px 16px; }
            @page { margin: 10mm 12mm; }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div>
            <div class="institution">PS-EDU · Sistema de Gestión Académica</div>
            <div class="report-title">Reporte por Programa y Curso</div>
            @if($semester)
                <div class="report-meta">
                    Semestre: <strong>{{ $semester->name }}</strong>
                    @if($semester->start_date)
                        &nbsp;|&nbsp; {{ $semester->start_date->format('d/m/Y') }} — {{ $semester->end_date?->format('d/m/Y') ?? 'Activo' }}
                    @endif
                </div>
            @endif
        </div>
        <div class="print-date">
            Generado el {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Filtros aplicados --}}
    @if($selectedPrograms->count() > 0)
    <div class="filters-box">
        <strong>Filtros aplicados:</strong>
        Programas: {{ $selectedPrograms->pluck('name')->join(', ') }}
    </div>
    @endif

    {{-- Stats globales --}}
    @if($semester && $courseReports->isNotEmpty())
    @php
        $printStats = [
            'Programas'  => $programReports->count(),
            'Cursos'     => $courseReports->count(),
            'Docentes'   => $courseReports->pluck('course.teacher_id')->filter()->unique()->count(),
            'Matrículas' => $courseReports->sum('active_students'),
            'Materiales' => $courseReports->sum('materials'),
        ];
    @endphp
    <div class="stats-grid">
        @foreach($printStats as $label => $value)
        <div class="stat-box">
            <div class="value">{{ $value }}</div>
            <div class="label">{{ $label }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Cursos agrupados por programa --}}
    @forelse($programReports as $programReport)
    <div class="program-section">
        <div class="program-header">
            <h2>{{ $programReport['program_name'] }}</h2>
            <div class="meta">
                @if($programReport['program_code'])
                Código: {{ $programReport['program_code'] }} &nbsp;|&nbsp;
                @endif
                {{ $programReport['courses']->count() }} curso(s)
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Curso</th>
                    <th style="width: 10%;">Código</th>
                    <th style="width: 20%;">Docente</th>
                    <th class="center" style="width: 10%;">Alumnos</th>
                    <th class="center" style="width: 8%;">Semanas</th>
                    <th class="center" style="width: 9%;">Materiales</th>
                    <th class="center" style="width: 8%;">Tareas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programReport['courses'] as $report)
                <tr>
                    <td style="font-weight: 600;">{{ $report['course']->name }}</td>
                    <td style="font-family: monospace; font-size: 10px; color: #6b7280;">{{ $report['course']->code }}</td>
                    <td>{{ $report['course']->teacher?->name ?? '—' }}</td>
                    <td class="center" style="font-weight: 600;">{{ $report['active_students'] }}</td>
                    <td class="center">{{ $report['weeks'] }}</td>
                    <td class="center">{{ $report['materials'] }}</td>
                    <td class="center">{{ $report['tasks'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="program-section">
        <p style="color: #9ca3af; padding: 30px; text-align: center;">No hay datos para mostrar con los filtros seleccionados.</p>
    </div>
    @endforelse

    <div class="footer">
        <span>PS-EDU — Reporte generado automáticamente</span>
        <span>{{ $semester?->name ?? 'Sin semestre' }} | {{ $programReports->count() }} programa(s) | {{ $courseReports->count() }} curso(s)</span>
    </div>

    <button class="print-btn" onclick="window.print()">
        &#x2399; Imprimir / Guardar PDF
    </button>

</body>
</html>
