<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte - {{ $course->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #fff; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 16px 20px; border-bottom: 2px solid #1e40af; }
        .institution { font-size: 15px; font-weight: 700; color: #1e40af; }
        .report-title { font-size: 13px; font-weight: 700; margin-top: 2px; }
        .report-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .print-date { text-align: right; font-size: 10px; color: #9ca3af; }

        .stats-bar { display: flex; gap: 16px; padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-item .v { font-size: 18px; font-weight: 800; }
        .stat-item .l { font-size: 10px; color: #6b7280; }
        .blue { color: #1d4ed8; }
        .green { color: #15803d; }
        .amber { color: #d97706; }

        .section { padding: 14px 20px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 16px; }
        thead th { background: #f3f4f6; text-align: center; padding: 6px 5px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; }
        thead th.left { text-align: left; padding-left: 8px; }
        tbody td { padding: 6px 5px; border-bottom: 1px solid #f3f4f6; text-align: center; vertical-align: middle; }
        tbody td.name-col { text-align: left; padding-left: 8px; font-weight: 500; }
        tbody tr:nth-child(even) { background: #fafafa; }

        .pill { display: inline-block; padding: 1px 6px; border-radius: 999px; font-size: 9.5px; font-weight: 700; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }
        .pill-green { background: #dcfce7; color: #15803d; }
        .pill-gray { background: #f3f4f6; color: #6b7280; }

        .footer { padding: 10px 20px; border-top: 1px solid #e5e7eb; font-size: 9.5px; color: #9ca3af; display: flex; justify-content: space-between; margin-top: 8px; }
        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1e40af; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }

        @media print {
            .print-btn { display: none !important; }
            @page { margin: 8mm 10mm; }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div>
            <div class="institution">PS-EDU - Reporte de Entregas</div>
            <div class="report-title">{{ $course->name }}</div>
            <div class="report-meta">
                Codigo: <strong>{{ $course->code }}</strong>
                @if($course->teacher)
                    | Docente: <strong>{{ $course->teacher->name }}</strong>
                @endif
            </div>
        </div>
        <div class="print-date">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    @php
        $totalStudents = count($studentRows);
        $totalTasks = $tasks->count();
        $totalExpected = $totalStudents * $totalTasks;
        $submitted = 0;
        $reviewed = 0;
        foreach ($studentRows as $row) {
            foreach ($tasks as $task) {
                $submission = $row['submissions'][$task->id] ?? null;
                if ($submission) {
                    $submitted++;
                    if ($submission->isGraded()) {
                        $reviewed++;
                    }
                }
            }
        }
        $pending = max($totalExpected - $submitted, 0);
    @endphp

    <div class="stats-bar">
        <div class="stat-item"><div class="v blue">{{ $totalStudents }}</div><div class="l">Alumnos</div></div>
        <div class="stat-item"><div class="v">{{ $totalTasks }}</div><div class="l">Tareas</div></div>
        <div class="stat-item"><div class="v blue">{{ $submitted }}</div><div class="l">Entregadas</div></div>
        <div class="stat-item"><div class="v green">{{ $reviewed }}</div><div class="l">Revisadas</div></div>
        <div class="stat-item"><div class="v amber">{{ $pending }}</div><div class="l">Sin Entrega</div></div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th class="left" style="min-width:150px;">Alumno</th>
                    @foreach($tasks as $task)
                    <th style="min-width:70px;">S{{ $task->week_number }}</th>
                    @endforeach
                    <th style="min-width:75px;">Entregadas</th>
                    <th style="min-width:75px;">Revisadas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentRows as $row)
                <tr>
                    <td class="name-col">{{ $row['student']->name }}</td>
                    @foreach($tasks as $task)
                        @php $submission = $row['submissions'][$task->id] ?? null; @endphp
                        <td>
                            @if($submission)
                                @if($submission->isGraded())
                                    <span class="pill pill-green">Revisada</span>
                                @else
                                    <span class="pill pill-blue">Entregada</span>
                                @endif
                            @else
                                <span class="pill pill-gray">Sin entrega</span>
                            @endif
                        </td>
                    @endforeach
                    <td>{{ $row['submitted_count'] }}/{{ $tasks->count() }}</td>
                    <td>{{ $row['reviewed_count'] }}/{{ $tasks->count() }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $tasks->count() + 3 }}" style="text-align:center; color:#9ca3af; padding:12px;">Sin alumnos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <span>PS-EDU - Reporte generado automaticamente</span>
        <span>{{ $course->name }}</span>
    </div>

    <button class="print-btn" onclick="window.print()">Imprimir / Guardar PDF</button>

</body>
</html>
