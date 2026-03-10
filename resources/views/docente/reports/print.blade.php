<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte — {{ $course->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #fff; }

        .page-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 24px 16px; border-bottom: 2px solid #1e40af; }
        .institution { font-size: 15px; font-weight: 700; color: #1e40af; }
        .report-title { font-size: 13px; font-weight: 700; margin-top: 3px; }
        .report-meta { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .print-date { text-align: right; font-size: 10px; color: #9ca3af; }

        .stats-bar { display: flex; gap: 20px; padding: 12px 24px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-item .v { font-size: 18px; font-weight: 800; }
        .stat-item .l { font-size: 10px; color: #6b7280; }
        .green { color: #15803d; }
        .red   { color: #dc2626; }
        .blue  { color: #1d4ed8; }
        .gray  { color: #9ca3af; }

        .section { padding: 16px 24px 0; }

        table { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 16px; }
        thead th { background: #f3f4f6; text-align: center; padding: 6px 5px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; border-bottom: 1px solid #d1d5db; border-right: 1px solid #e5e7eb; }
        thead th.left { text-align: left; padding-left: 8px; }
        thead th.avg-col { background: #e0e7ff; color: #3730a3; }
        tbody td { padding: 5px 5px; border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6; text-align: center; vertical-align: middle; }
        tbody td.name-col { text-align: left; padding-left: 8px; font-weight: 500; }
        tbody td.code-col { font-family: monospace; font-size: 9.5px; color: #9ca3af; }
        tbody td.avg-col  { background: #eef2ff; font-weight: 700; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody tr:nth-child(even) td.avg-col { background: #e8edff; }

        .score-green { color: #15803d; font-weight: 700; }
        .score-amber { color: #d97706; font-weight: 700; }
        .score-red   { color: #dc2626; font-weight: 700; }
        .score-none  { color: #d1d5db; }

        .pill { display: inline-block; padding: 1px 6px; border-radius: 999px; font-size: 9.5px; font-weight: 700; }
        .pill-green { background: #dcfce7; color: #15803d; }
        .pill-red   { background: #fee2e2; color: #dc2626; }

        .footer { padding: 10px 24px; border-top: 1px solid #e5e7eb; font-size: 9.5px; color: #9ca3af; display: flex; justify-content: space-between; margin-top: 8px; }

        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1e40af; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 14px rgba(30,64,175,0.35); }
        .print-btn:hover { background: #1d4ed8; }

        @media print {
            .print-btn { display: none !important; }
            body { font-size: 10px; }
            .page-header { padding: 10px 14px 10px; }
            .stats-bar { padding: 8px 14px; }
            .section { padding: 10px 14px 0; }
            @page { margin: 8mm 10mm; }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div>
            <div class="institution">PS-EDU · Sistema de Gestión Académica</div>
            <div class="report-title">{{ $course->name }}</div>
            <div class="report-meta">
                Código: <strong>{{ $course->code }}</strong>
                @if($course->teacher)
                    &nbsp;·&nbsp; Docente: <strong>{{ $course->teacher->name }}</strong>
                @endif
                @if($course->semester)
                    &nbsp;·&nbsp; Semestre: <strong>{{ $course->semester->name }}</strong>
                @endif
            </div>
        </div>
        <div class="print-date">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Stats --}}
    @php
        $scored   = collect($studentRows)->filter(fn($r) => $r['average'] !== null);
        $approved = $scored->filter(fn($r) => $r['average'] >= 11);
        $avgGlobal = $scored->count() > 0 ? round($scored->avg(fn($r) => $r['average']), 1) : null;
    @endphp
    <div class="stats-bar">
        <div class="stat-item"><div class="v blue">{{ count($studentRows) }}</div><div class="l">Total Alumnos</div></div>
        <div class="stat-item"><div class="v" style="color:#7c3aed;">{{ $scored->count() }}</div><div class="l">Calificados</div></div>
        <div class="stat-item"><div class="v green">{{ $approved->count() }}</div><div class="l">Aprobados</div></div>
        <div class="stat-item"><div class="v red">{{ $scored->count() - $approved->count() }}</div><div class="l">Desaprobados</div></div>
        <div class="stat-item">
            <div class="v {{ $avgGlobal === null ? 'gray' : ($avgGlobal < 11 ? 'red' : ($avgGlobal < 14 ? '' : 'green')) }}"
                 style="{{ $avgGlobal !== null && $avgGlobal >= 11 && $avgGlobal < 14 ? 'color:#d97706' : '' }}">
                {{ $avgGlobal !== null ? number_format($avgGlobal, 1) : '—' }}
            </div>
            <div class="l">Promedio</div>
        </div>
        @if($scored->count() > 0)
        <div class="stat-item">
            <div class="v {{ round($approved->count() / $scored->count() * 100) >= 51 ? 'green' : 'red' }}">
                {{ round($approved->count() / $scored->count() * 100) }}%
            </div>
            <div class="l">Tasa Aprobación</div>
        </div>
        @endif
    </div>

    {{-- Tabla --}}
    <div class="section">
        @if($items->isEmpty())
            <p style="color:#9ca3af; padding: 16px 0;">Sin ítems de calificación registrados.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th class="left" style="min-width:130px;">Alumno</th>
                    <th style="min-width:60px;">Código</th>
                    @foreach($items as $item)
                    <th style="min-width:65px; max-width:80px;">
                        {{ Str::limit($item->name, 14) }}
                        @if($item->weight > 0)<br><span style="font-size:9px; color:#9ca3af;">{{ $item->weight }}%</span>@endif
                    </th>
                    @endforeach
                    <th class="avg-col" style="min-width:60px;">Promedio</th>
                    <th style="min-width:70px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse(collect($studentRows)->sortByDesc('average') as $row)
                @php
                    $avg = $row['average'];
                    $avgClass = $avg === null ? 'score-none'
                        : ($avg < 11  ? 'score-red'
                        : ($avg < 14  ? 'score-amber'
                        : 'score-green'));
                @endphp
                <tr>
                    <td class="name-col">{{ $row['student']->name }}</td>
                    <td class="code-col">{{ $row['student']->alumnoProfile?->student_code ?? '' }}</td>
                    @foreach($items as $item)
                    @php
                        $score = $row['scores'][$item->id] ?? null;
                        $norm  = $score !== null ? round(($score / $item->max_score) * 20, 1) : null;
                        $sc = $norm === null ? 'score-none' : ($norm < 11 ? 'score-red' : ($norm < 14 ? 'score-amber' : 'score-green'));
                    @endphp
                    <td class="{{ $sc }}">{{ $norm !== null ? number_format($norm, 1) : '—' }}</td>
                    @endforeach
                    <td class="avg-col {{ $avgClass }}">{{ $avg !== null ? number_format($avg, 1) : '—' }}</td>
                    <td>
                        @if($avg !== null)
                            <span class="pill {{ $avg >= 11 ? 'pill-green' : 'pill-red' }}">
                                {{ $avg >= 11 ? 'Aprobado' : 'Desaprobado' }}
                            </span>
                        @else
                            <span class="score-none">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $items->count() + 4 }}" style="text-align:center; color:#9ca3af; padding:12px;">Sin alumnos.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        <span>PS-EDU — Reporte generado automáticamente</span>
        <span>{{ $course->name }} | {{ count($studentRows) }} alumno(s)</span>
    </div>

    <button class="print-btn" onclick="window.print()">
        &#x2399; Imprimir / Guardar PDF
    </button>

</body>
</html>
