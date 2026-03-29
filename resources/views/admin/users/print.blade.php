<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            color: #1f2937;
        }
        h1 {
            margin: 0;
            font-size: 20px;
        }
        .meta {
            margin-top: 8px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: 700;
        }
        .empty {
            text-align: center;
            color: #6b7280;
            padding: 20px;
        }
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Imprimir / Guardar como PDF</button>

    <h1>Lista de usuarios (resultados filtrados)</h1>
    <div class="meta">
        <div>Total exportado: {{ $users->count() }} usuarios</div>
        <div>Generado: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Codigo</th>
                <th>Correo</th>
                <th>DNI</th>
                <th>Telefono</th>
                <th>Programa</th>
                <th>Promocion</th>
                <th>Estado</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->alumnoProfile?->code ?? '-' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->dni ?? '-' }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->alumnoProfile?->program ?? '-' }}</td>
                    <td>{{ $user->alumnoProfile?->promotion_year ?? '-' }}</td>
                    <td>{{ $user->status ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty">No hay usuarios para exportar con los filtros actuales.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
