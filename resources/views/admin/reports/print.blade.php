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

        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; padding: 16px 24px; }
        .stat-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; text-align: center; }
        .stat-box .value { font-size: 20px; font-weight: 800; color: #1e40af; }
        .stat-box .label { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .section { padding: 0 24px 20px; }
        .section-title { font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 1px solid #d1d5db; }
        thead th.center { text-align: center; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tbody td.center { text-align: center; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .avg-green { color: #15803d; font-weight: 700; }
        .avg-amber { color: #d97706; font-weight: 700; }
        .avg-red   { color: #dc2626; font-weight: 700; }
        .avg-none  { color: #d1d5db; }

        .pill { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 10px; font-weight: 600; }
        .pill-green { background: #dcfce7; color: #15803d; }
        .pill-red   { background: #fee2e2; color: #dc2626; }

        .footer { padding: 12px 24px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; display: flex; justify-content: space-between; }

        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1e40af; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(30,64,175,0.35); }
        .print-btn:hover { background: #1d4ed8; }

        @media print {
            .print-btn { display: none !important; }
            body { font-size: 11px; }
            .page-header { padding: 12px 16px 10px; }
            .stats-grid { padding: 10px 16px; }
            .section { padding: 0 16px 14px; }
            @page { margin: 10mm 12mm; }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div>
            <div class="institution">PS-EDU · Sistema de Gestión Académica</div>
            <div class="report-title">Reporte General del Sistema</div>
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

    {{-- Stats globales --}}
    @if($semester && $courseReports->isNotEmpty())
    @php
        $printStats = [
            'Cursos'     => $courseReports->count(),
            'Docentes'   => $courseReports->pluck('course.teacher_id')->filter()->unique()->count(),
            'Matrículas' => $courseReports->sum('active_students'),
            'Materiales' => $courseReports->sum('materials'),
            'Tareas'     => $courseReports->sum('tasks'),
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

    {{-- Tabla de cursos --}}
    <div class="section">
        <div class="section-title">Actividad por Curso</div>

        @if($courseReports->isEmpty())
            <p style="color:#9ca3af; padding: 16px 0;">No hay cursos registrados en este semestre.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Código</th>
                    <th>Docente</th>
                    <th class="center">Alumnos</th>
                    <th class="center">Semanas</th>
                    <th class="center">Materiales</th>
                    <th class="center">Tareas</th>
                    <th class="center">Promedio</th>
                    <th class="center">Aprobados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courseReports as $report)
                @php
                    $avg = $report['average'];
                    $avgClass = $avg === null ? 'avg-none' : ($avg < 11 ? 'avg-red' : ($avg < 14 ? 'avg-amber' : 'avg-green'));
                    $pct = $report['approval_rate'];
                @endphp
                <tr>
                    <td>{{ $report['course']->name }}</td>
                    <td style="font-family:monospace; font-size:10px; color:#6b7280;">{{ $report['course']->code }}</td>
                    <td>{{ $report['course']->teacher?->name ?? '—' }}</td>
                    <td class="center">{{ $report['active_students'] }}</td>
                    <td class="center">{{ $report['weeks'] }}</td>
                    <td class="center">{{ $report['materials'] }}</td>
                    <td class="center">{{ $report['tasks'] }}</td>
                    <td class="center {{ $avgClass }}">{{ $avg !== null ? number_format($avg, 1) : '—' }}</td>
                    <td class="center">
                        @if($pct !== null)
                            <span class="pill {{ $pct >= 51 ? 'pill-green' : 'pill-red' }}">{{ $pct }}%</span>
                        @else
                            <span class="avg-none">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        <span>PS-EDU — Reporte generado automáticamente</span>
        <span>{{ $semester?->name ?? 'Sin semestre' }} | {{ $courseReports->count() }} curso(s)</span>
    </div>

    <button class="print-btn" onclick="window.print()">
        &#x2399; Imprimir / Guardar PDF
    </button>

</body>
</html>
