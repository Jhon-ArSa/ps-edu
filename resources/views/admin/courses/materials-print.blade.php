<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiales - {{ $course->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; font-size: 11pt; line-height: 1.4; color: #1f2937; padding: 1.5rem; }
        .header { text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 1rem; }
        .header h1 { font-size: 20pt; margin-bottom: 0.5rem; color: #1e40af; }
        .header p { color: #6b7280; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        thead { background: #f3f4f6; }
        th { text-align: left; padding: 8px; font-size: 9pt; text-transform: uppercase; color: #4b5563; border-bottom: 2px solid #d1d5db; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 10pt; vertical-align: top; }
        .week-header { background: #eff6ff; font-weight: bold; color: #1e40af; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8pt; font-weight: 600; }
        .badge-file { background: #dbeafe; color: #1e40af; }
        .badge-link { background: #d1fae5; color: #065f46; }
        .badge-video { background: #fee2e2; color: #991b1b; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $course->name }}</h1>
        <p><strong>Código:</strong> {{ $course->code }} | <strong>Docente:</strong> {{ $course->teacher->name ?? '—' }}</p>
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($course->weeks->isEmpty())
        <p style="text-align: center; color: #9ca3af; margin-top: 3rem;">No hay semanas programadas en este curso.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Semana</th>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 35%;">Título</th>
                    <th style="width: 37%;">Descripción</th>
                    <th style="width: 10%;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course->weeks->sortBy('number') as $week)
                    @if($week->materials->isNotEmpty())
                        <tr class="week-header">
                            <td colspan="5">
                                {{ $week->title }}
                            </td>
                        </tr>
                        @foreach($week->materials->sortBy('order') as $material)
                            <tr>
                                <td style="text-align: center;">{{ $week->number }}</td>
                                <td>
                                    <span class="badge badge-{{ $material->type }}">
                                        @if($material->type === 'file') 📄 Archivo
                                        @elseif($material->type === 'link') 🔗 Enlace
                                        @else 🎬 Video
                                        @endif
                                    </span>
                                </td>
                                <td><strong>{{ $material->title }}</strong></td>
                                <td style="color: #6b7280;">{{ $material->description ?? '—' }}</td>
                                <td style="font-size: 9pt; color: #9ca3af;">{{ $material->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        // Auto-abrir diálogo de impresión al cargar
        window.onload = () => window.print();
    </script>
</body>
</html>
