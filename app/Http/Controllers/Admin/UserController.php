<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocenteProfile;
use App\Models\AlumnoProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('dni', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'min:8', 'confirmed', new \App\Rules\StrongPassword()],
            'role'     => 'required|in:admin,docente,alumno',
            'dni'      => 'nullable|string|max:20|unique:users,dni',
            'phone'    => 'nullable|string|max:20',
            'status'   => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);
        
        // Guardar la contraseña en texto plano temporalmente para el email
        $temporaryPassword = $validated['password'];

        $user = User::create($validated);

        if ($user->isDocente()) {
            DocenteProfile::create([
                'user_id'          => $user->id,
                'title'            => $request->input('title'),
                'degree'           => $request->input('degree'),
                'specialty'        => $request->input('specialty'),
                'category'         => $request->input('category'),
                'years_of_service' => $request->input('years_of_service'),
                'bio'              => $request->input('bio'),
            ]);
        } elseif ($user->isAlumno()) {
            AlumnoProfile::create([
                'user_id'        => $user->id,
                'code'           => $request->input('code'),
                'promotion_year' => $request->input('promotion_year'),
                'program'        => $request->input('program'),
            ]);
        }

        // Enviar email de bienvenida con credenciales
        $user->notify(new \App\Notifications\WelcomeUserNotification(
            $user->name,
            $user->email,
            $temporaryPassword,
            $user->role
        ));

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente. Se ha enviado un email con las credenciales de acceso.');
    }

    public function show(User $user)
    {
        $user->load(['docenteProfile', 'alumnoProfile', 'coursesTaught', 'enrollments.course']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['docenteProfile', 'alumnoProfile']);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,docente,alumno',
            'dni'   => 'nullable|string|max:20|unique:users,dni,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:8', 'confirmed', new \App\Rules\StrongPassword()]
            ]);
            $validated['password'] = $request->password;
        }

        $user->update($validated);

        // Update profile
        if ($user->isDocente()) {
            $user->docenteProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['title', 'degree', 'specialty', 'category', 'years_of_service', 'bio'])
            );
        } elseif ($user->isAlumno()) {
            $user->alumnoProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['code', 'promotion_year', 'program'])
            );
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puede eliminarse a sí mismo.');
        }

        $user->update(['status' => false]);
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario desactivado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);
        return response()->json([
            'status'  => $user->status,
            'message' => $user->status ? 'Usuario activado' : 'Usuario desactivado',
        ]);
    }

    public function importForm()
    {
        return view('admin.users.import');
    }

    public function importTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios');

        // ── Columnas ────────────────────────────────────────────────────────
        $columns = [
            'A' => ['nombre',         'Nombre completo',              'Ana Torres'],
            'B' => ['email',          'Correo electrónico',           'ana.torres@ejemplo.com'],
            'C' => ['contrasena',     'Contraseña (mín. 8 chars)',    'clave1234'],
            'D' => ['rol',            'admin / docente / alumno',     'docente'],
            'E' => ['dni',            'DNI (opcional)',                '12345678'],
            'F' => ['telefono',       'Teléfono (opcional)',           '987654321'],
            'G' => ['titulo',         'Título docente (opcional)',     'Mg.'],
            'H' => ['grado',          'Grado académico (opcional)',    'Maestría'],
            'I' => ['especialidad',   'Especialidad (opcional)',       'Sistemas'],
            'J' => ['categoria',      'Categoría docente (opcional)', 'Asociado'],
            'K' => ['anios_servicio', 'Años de servicio (opcional)',  '3'],
            'L' => ['codigo_alumno',  'Código alumno (opcional)',     ''],
            'M' => ['anio_promocion', 'Año de promoción (opcional)',  ''],
            'N' => ['programa',       'Programa / mención (opcional)',''],
        ];

        // ── Fila 1: sub-encabezado descriptivo ───────────────────────────
        foreach ($columns as $col => [$key, $desc, $example]) {
            $sheet->setCellValue("{$col}1", $desc);
        }
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font'      => ['bold' => false, 'italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Fila 2: encabezados de la tabla (nombres de columna del CSV) ─
        foreach ($columns as $col => [$key, $desc, $example]) {
            $sheet->setCellValue("{$col}2", $key);
        }

        // ── Fila 3: fila de ejemplo ──────────────────────────────────────
        foreach ($columns as $col => [$key, $desc, $example]) {
            $sheet->setCellValue("{$col}3", $example);
        }

        // ── Tabla de Excel ───────────────────────────────────────────────
        $table = new Table();
        $table->setName('Usuarios');
        $table->setRange('A2:N3');

        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $tableStyle->setShowFirstColumn(false);
        $tableStyle->setShowLastColumn(false);
        $tableStyle->setShowRowStripes(true);
        $tableStyle->setShowColumnStripes(false);
        $table->setStyle($tableStyle);
        $sheet->addTable($table);

        // ── Anchos de columna ─────────────────────────────────────────────
        $widths = ['A'=>28,'B'=>32,'C'=>22,'D'=>14,'E'=>14,'F'=>16,
                   'G'=>14,'H'=>18,'I'=>20,'J'=>18,'K'=>18,'L'=>20,'M'=>18,'N'=>24];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // ── Colores de sección en fila descriptiva ───────────────────────
        // General (A-F): azul claro
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EFF6FF');
        // Docente (G-K): ámbar claro
        $sheet->getStyle('G1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFBEB');
        // Alumno (L-N): verde claro
        $sheet->getStyle('L1:N1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0FDF4');

        // ── Hoja de referencia: roles válidos ────────────────────────────
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referencia');
        $refSheet->setCellValue('A1', 'Roles válidos');
        $refSheet->setCellValue('A2', 'admin');
        $refSheet->setCellValue('A3', 'docente');
        $refSheet->setCellValue('A4', 'alumno');
        $refSheet->getStyle('A1')->getFont()->setBold(true);
        $refSheet->setCellValue('C1', 'Notas');
        $refSheet->setCellValue('C2', '• Los campos con * son obligatorios.');
        $refSheet->setCellValue('C3', '• Emails o DNIs duplicados se omiten.');
        $refSheet->setCellValue('C4', '• La contraseña debe tener al menos 8 caracteres.');
        $refSheet->setCellValue('C5', '• Columnas G–K solo aplican para rol "docente".');
        $refSheet->setCellValue('C6', '• Columnas L–N solo aplican para rol "alumno".');
        $refSheet->getStyle('C1')->getFont()->setBold(true);
        $refSheet->getColumnDimension('A')->setWidth(16);
        $refSheet->getColumnDimension('C')->setWidth(55);
        foreach (['A1','C1'] as $cell) {
            $refSheet->getStyle($cell)->getFont()->setBold(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        // ── Respuesta de descarga ────────────────────────────────────────
        $writer = new Xlsx($spreadsheet);
        $filename = 'plantilla_usuarios.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ], [
            'csv_file.required' => 'Seleccione un archivo.',
            'csv_file.max'      => 'El archivo no debe superar los 5 MB.',
        ]);

        $file      = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'xlsx', 'xls', 'txt'])) {
            return back()->with('error', 'Formato no soportado. Use CSV o Excel (.xlsx).');
        }

        $rows = [];

        if ($extension === 'xlsx' || $extension === 'xls') {
            // ── Leer Excel ────────────────────────────────────────────────
            $reader      = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $data        = $sheet->toArray(null, true, true, false);

            // Buscar la fila que contiene el encabezado (busca "nombre" o "email")
            $headerRow = null;
            foreach ($data as $i => $row) {
                $normalized = array_map(fn($v) => strtolower(trim((string)$v)), $row);
                if (in_array('nombre', $normalized) && in_array('email', $normalized)) {
                    $headerRow = $normalized;
                    $startIdx  = $i + 1;
                    break;
                }
            }
            if ($headerRow === null) {
                return back()->with('error', 'No se encontró la fila de encabezados en el Excel. Asegúrese de usar la plantilla descargada.');
            }
            for ($i = $startIdx; $i < count($data); $i++) {
                $rec = [];
                foreach ($headerRow as $j => $col) {
                    $rec[$col] = trim((string)($data[$i][$j] ?? ''));
                }
                $rows[] = $rec;
            }
        } else {
            // ── Leer CSV ──────────────────────────────────────────────────
            $handle = fopen($file->getRealPath(), 'r');
            $bom    = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);

            $rawHeaders = fgetcsv($handle);
            if (!$rawHeaders) {
                fclose($handle);
                return back()->with('error', 'El archivo CSV está vacío o mal formado.');
            }
            $headers = array_map(fn($h) => strtolower(trim($h)), $rawHeaders);

            while (($data = fgetcsv($handle)) !== false) {
                $rec = [];
                foreach ($headers as $j => $col) {
                    $rec[$col] = trim($data[$j] ?? '');
                }
                $rows[] = $rec;
            }
            fclose($handle);
        }

        // ── Procesar filas ────────────────────────────────────────────────
        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        foreach ($rows as $rec) {
            $row++;
            if (empty(array_filter($rec))) continue;

            $name     = $rec['nombre']     ?? '';
            $email    = $rec['email']      ?? '';
            $password = $rec['contrasena'] ?? '';
            $role     = $rec['rol']        ?? '';

            if ($name === '' || $email === '' || $password === '' || $role === '') {
                $errors[] = "Fila {$row}: faltan campos obligatorios (nombre, email, contraseña, rol).";
                continue;
            }
            if (!in_array($role, ['admin', 'docente', 'alumno'])) {
                $errors[] = "Fila {$row}: rol «{$role}» inválido. Use: admin, docente o alumno.";
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Fila {$row}: el email «{$email}» no tiene formato válido.";
                continue;
            }
            if (strlen($password) < 8) {
                $errors[] = "Fila {$row}: la contraseña debe tener al menos 8 caracteres.";
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $skipped++;
                $errors[] = "Fila {$row}: el email «{$email}» ya existe (omitido).";
                continue;
            }

            $dni = ($rec['dni'] ?? '') !== '' ? $rec['dni'] : null;
            if ($dni && User::where('dni', $dni)->exists()) {
                $skipped++;
                $errors[] = "Fila {$row}: el DNI «{$dni}» ya existe (omitido).";
                continue;
            }

            try {
                $user = User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => $role,
                    'dni'      => $dni,
                    'phone'    => ($rec['telefono'] ?? '') !== '' ? $rec['telefono'] : null,
                    'status'   => true,
                ]);

                if ($user->isDocente()) {
                    DocenteProfile::create([
                        'user_id'          => $user->id,
                        'title'            => ($rec['titulo']        ?? '') ?: null,
                        'degree'           => ($rec['grado']         ?? '') ?: null,
                        'specialty'        => ($rec['especialidad']  ?? '') ?: null,
                        'category'         => ($rec['categoria']     ?? '') ?: null,
                        'years_of_service' => is_numeric($rec['anios_servicio'] ?? '') ? (int) $rec['anios_servicio'] : null,
                    ]);
                } elseif ($user->isAlumno()) {
                    AlumnoProfile::create([
                        'user_id'        => $user->id,
                        'code'           => ($rec['codigo_alumno'] ?? '') ?: null,
                        'promotion_year' => is_numeric($rec['anio_promocion'] ?? '') ? (int) $rec['anio_promocion'] : null,
                        'program'        => ($rec['programa']      ?? '') ?: null,
                    ]);
                }

                // Enviar email de bienvenida con credenciales
                $user->notify(new \App\Notifications\WelcomeUserNotification(
                    $user->name,
                    $user->email,
                    $password, // Contraseña en texto plano antes de hashear
                    $user->role
                ));

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Fila {$row}: error al crear «{$email}» — " . $e->getMessage();
            }
        }

        Cache::forget('admin_dashboard_stats');

        $summary = "{$imported} usuario(s) importado(s)";
        if ($skipped > 0) $summary .= ", {$skipped} omitido(s) por duplicado";

        return redirect()->route('admin.users.index')
            ->with('import_summary', $summary)
            ->with('import_errors', $errors);
    }
}
