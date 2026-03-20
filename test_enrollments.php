<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$enrollments = App\Models\Enrollment::with('student.alumnoProfile', 'course')->take(5)->get();

echo "=== VERIFICACIÓN DE MATRÍCULAS ===\n\n";

foreach ($enrollments as $enrollment) {
    echo "Alumno: " . ($enrollment->student->name ?? 'N/A') . "\n";
    echo "  Email: " . ($enrollment->student->email ?? 'N/A') . "\n";
    echo "  Tiene perfil: " . ($enrollment->student->alumnoProfile ? 'SÍ' : 'NO') . "\n";
    echo "  Año ingreso: " . ($enrollment->student->alumnoProfile?->promotion_year ?? 'NO DEFINIDO') . "\n";
    echo "  Curso: " . ($enrollment->course->name ?? 'N/A') . "\n";
    echo "  Estado: " . $enrollment->status . "\n";
    echo "---\n";
}
