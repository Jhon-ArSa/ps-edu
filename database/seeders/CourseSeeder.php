<?php

namespace Database\Seeders;

use App\Models\AlumnoProfile;
use App\Models\Course;
use App\Models\CurriculumItem;
use App\Models\DocenteProfile;
use App\Models\Enrollment;
use App\Models\Mention;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════
        // 1. SEMESTRES ACADÉMICOS
        // ══════════════════════════════════════════════════════════════
        Semester::create([
            'name'        => '2025-II',
            'year'        => 2025,
            'period'      => 'II',
            'start_date'  => '2025-08-18',
            'end_date'    => '2025-12-19',
            'status'      => 'closed',
            'description' => 'Semestre académico 2025-II (agosto – diciembre)',
        ]);

        $semestre = Semester::create([
            'name'        => '2026-I',
            'year'        => 2026,
            'period'      => 'I',
            'start_date'  => '2026-03-16',
            'end_date'    => '2026-07-17',
            'status'      => 'active',
            'description' => 'Semestre académico 2026-I (marzo – julio)',
        ]);

        // ══════════════════════════════════════════════════════════════
        // 2. DOCENTES ADICIONALES
        // ══════════════════════════════════════════════════════════════
        $docentes = $this->createDocentes();

        // Incluir el docente de prueba ya existente
        $docenteExistente = User::where('email', 'docente@facultad.edu.pe')->first();
        if ($docenteExistente) {
            $docentes->prepend($docenteExistente);
        }

        // ══════════════════════════════════════════════════════════════
        // 3. ALUMNOS ADICIONALES (agrupados por programa)
        // ══════════════════════════════════════════════════════════════
        $alumnoGroups = $this->createAlumnos();

        // Incluir la alumna de prueba en ME
        $alumnaExistente = User::where('email', 'alumno@facultad.edu.pe')->first();
        if ($alumnaExistente) {
            $alumnoGroups['ME']->prepend($alumnaExistente);
        }

        // ══════════════════════════════════════════════════════════════
        // 4. CURSOS DEL SEMESTRE 2026-I
        // ══════════════════════════════════════════════════════════════
        $this->createCourses($semestre, $docentes, $alumnoGroups);
    }

    // ─── Docentes ─────────────────────────────────────────────────────

    private function createDocentes(): Collection
    {
        $data = [
            ['Dra. Ana María Quispe Huamán',       'aquispe@facultad.edu.pe',    '20123456', 'Dra.', 'Doctora en Ciencias de la Educación',      'Gestión Educativa',          'Principal',  20],
            ['Dr. Carlos Alberto Rojas Espinoza',   'crojas@facultad.edu.pe',     '20234567', 'Dr.',  'Doctor en Educación',                      'Investigación Educativa',    'Principal',  18],
            ['Mg. Rosa Elena Vargas Mendoza',       'rvargas@facultad.edu.pe',    '20345678', 'Mg.',  'Magíster en Psicología Educativa',         'Psicología Educativa',       'Asociado',   12],
            ['Dr. Manuel Enrique Torres Cárdenas',  'mtorres@facultad.edu.pe',    '20456789', 'Dr.',  'Doctor en Administración de la Educación', 'Administración Educativa',   'Principal',  16],
            ['Mg. Lucía Fernanda Huamaní Ramos',    'lhuamani@facultad.edu.pe',   '20567890', 'Mg.',  'Magíster en Tecnología Educativa',         'TIC en Educación',           'Asociado',   8],
            ['Dr. Pedro Luis Cóndor Yauri',         'pcondor@facultad.edu.pe',    '20678901', 'Dr.',  'Doctor en Ciencias de la Educación',       'Educación Intercultural',    'Principal',  22],
            ['Dra. Martha Isabel Parraga Solano',   'mparraga@facultad.edu.pe',   '20789012', 'Dra.', 'Doctora en Educación',                     'Didáctica Universitaria',    'Principal',  14],
            ['Mg. Jorge Antonio Espejo Robles',     'jespejo@facultad.edu.pe',    '20890123', 'Mg.',  'Magíster en Educación',                    'Currículo y Evaluación',     'Asociado',   10],
            ['Dr. Víctor Hugo Meza Palomino',       'vmeza@facultad.edu.pe',      '20901234', 'Dr.',  'Doctor en Educación',                      'Epistemología',              'Principal',  25],
            ['Mg. Carmen Rosa Gutiérrez Ávila',     'cgutierrez@facultad.edu.pe', '21012345', 'Mg.',  'Magíster en Educación Inicial',            'Educación Inicial',          'Asociado',   9],
            ['Dr. Roberto Ángel Suárez Poma',       'rsuarez@facultad.edu.pe',    '21123456', 'Dr.',  'Doctor en Ciencias de la Educación',       'Estadística Aplicada',       'Asociado',   13],
            ['Mg. Yolanda Patricia Ccanto Mallma',  'yccanto@facultad.edu.pe',    '21234567', 'Mg.',  'Magíster en Lingüística Aplicada',         'Educación Bilingüe',         'Auxiliar',   6],
        ];

        $docentes = collect();

        foreach ($data as [$name, $email, $dni, $title, $degree, $specialty, $category, $years]) {
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => 'docente',
                'dni'      => $dni,
                'status'   => true,
            ]);

            DocenteProfile::create([
                'user_id'          => $user->id,
                'title'            => $title,
                'degree'           => $degree,
                'specialty'        => $specialty,
                'category'         => $category,
                'years_of_service' => $years,
                'bio'              => 'Docente de la Escuela de Posgrado de la Facultad de Educación – UNCP.',
            ]);

            $docentes->push($user);
        }

        return $docentes;
    }

    // ─── Alumnos ──────────────────────────────────────────────────────

    private function createAlumnos(): array
    {
        // [nombre, email, DNI, código, programa_texto, grupo]
        $data = [
            // ── ME – Gestión Educativa ──
            ['Luis Alberto Flores Ramos',       'lflores@correo.edu.pe',      '40100001', '2026-002', 'Maestría en Educación – Gestión Educativa',       'ME', 'ME-GE'],
            ['Patricia Sonia Aquino López',      'paquino@correo.edu.pe',      '40100002', '2026-003', 'Maestría en Educación – Gestión Educativa',       'ME', 'ME-GE'],
            ['Fernando José Cárdenas Rivera',    'fcardenas@correo.edu.pe',    '40100003', '2026-004', 'Maestría en Educación – Gestión Educativa',       'ME', 'ME-GE'],
            // ── ME – Enseñanza Estratégica ──
            ['Diana Carolina Mendoza Paucar',    'dmendoza@correo.edu.pe',     '40100004', '2026-005', 'Maestría en Educación – Enseñanza Estratégica',   'ME', 'ME-EE'],
            ['Hugo Martín Salazar Tovar',        'hsalazar@correo.edu.pe',     '40100005', '2026-006', 'Maestría en Educación – Enseñanza Estratégica',   'ME', 'ME-EE'],
            ['Carmen Lucía Tapia Hidalgo',       'ctapia@correo.edu.pe',       '40100006', '2026-007', 'Maestría en Educación – Enseñanza Estratégica',   'ME', 'ME-EE'],
            // ── ME – Psicología Educativa ──
            ['Claudia Teresa Ortega Vilcas',     'cortega@correo.edu.pe',      '40100007', '2026-008', 'Maestría en Educación – Psicología Educativa',    'ME', 'ME-PE'],
            ['Andrés Felipe Huayta Crisóstomo',  'ahuayta@correo.edu.pe',     '40100008', '2026-009', 'Maestría en Educación – Psicología Educativa',    'ME', 'ME-PE'],
            ['Milagros del Pilar Gómez Taype',   'mgomez@correo.edu.pe',      '40100009', '2026-010', 'Maestría en Educación – Psicología Educativa',    'ME', 'ME-PE'],
            // ── ME – Educación Superior ──
            ['Ricardo Iván Poma Caso',           'rpoma@correo.edu.pe',        '40100010', '2026-011', 'Maestría en Educación – Educación Superior',      'ME', 'ME-ES'],
            ['Gabriela Luz Valencia Inga',       'gvalencia@correo.edu.pe',    '40100011', '2026-012', 'Maestría en Educación – Educación Superior',      'ME', 'ME-ES'],
            // ── MDES ──
            ['Óscar Raúl Lizárraga Matos',       'olizarraga@correo.edu.pe',  '40100012', '2026-013', 'Maestría en Didáctica de la Educación Superior',  'MDES', null],
            ['Sonia Beatriz Chávez Aliaga',      'schavez@correo.edu.pe',      '40100013', '2026-014', 'Maestría en Didáctica de la Educación Superior',  'MDES', null],
            ['Édgar William Rojas Cunyas',       'erojas@correo.edu.pe',       '40100014', '2026-015', 'Maestría en Didáctica de la Educación Superior',  'MDES', null],
            // ── MAE ──
            ['Elena Patricia Capcha Huamán',     'ecapcha@correo.edu.pe',      '40100015', '2026-016', 'Maestría en Administración de la Educación',      'MAE', null],
            ['Santiago Iván Contreras Lazo',      'scontreras@correo.edu.pe',  '40100016', '2026-017', 'Maestría en Administración de la Educación',      'MAE', null],
            ['Rosa Angélica Medina Torres',       'rmedina@correo.edu.pe',     '40100017', '2026-018', 'Maestría en Administración de la Educación',      'MAE', null],
            // ── MEIB ──
            ['Juana Rosa Ticllasuca Curo',       'jticllasuca@correo.edu.pe',  '40100018', '2026-019', 'Maestría en Educación Intercultural Bilingüe',    'MEIB', null],
            ['Néstor Abel Huanca Pari',          'nhuanca@correo.edu.pe',      '40100019', '2026-020', 'Maestría en Educación Intercultural Bilingüe',    'MEIB', null],
            // ── MTE ──
            ['Karen Ysabel Porras Véliz',        'kporras@correo.edu.pe',      '40100020', '2026-021', 'Maestría en Tecnología Educativa',                'MTE', null],
            ['Julián David Orellana Sinche',      'jorellana@correo.edu.pe',   '40100021', '2026-022', 'Maestría en Tecnología Educativa',                'MTE', null],
            ['María Esperanza Calderón Zamudio',  'mcalderon@correo.edu.pe',   '40100022', '2026-023', 'Maestría en Tecnología Educativa',                'MTE', null],
            // ── MIDU ──
            ['Héctor Julio Navarro Povis',       'hnavarro@correo.edu.pe',     '40100023', '2026-024', 'Maestría en Investigación y Docencia Universitaria', 'MIDU', null],
            ['Verónica Inés Araujo Casas',       'varaujo@correo.edu.pe',      '40100024', '2026-025', 'Maestría en Investigación y Docencia Universitaria', 'MIDU', null],
            // ── MEI ──
            ['Luz Marina Canchaya Obregón',      'lcanchaya@correo.edu.pe',    '40100025', '2026-026', 'Maestría en Educación Inicial',                   'MEI', null],
            ['Flor de María Surichaqui Porta',   'fsurichaqui@correo.edu.pe',  '40100026', '2026-027', 'Maestría en Educación Inicial',                   'MEI', null],
            ['Maribel Sofía Huamán Ccora',       'mhuaman@correo.edu.pe',      '40100027', '2026-028', 'Maestría en Educación Inicial',                   'MEI', null],
            // ── DCE ──
            ['Mg. Alejandro León Meza',          'aleon@correo.edu.pe',        '40100028', '2026-029', 'Doctorado en Ciencias de la Educación',           'DCE', null],
            ['Mg. Silvia Campos Hinostroza',     'scampos@correo.edu.pe',      '40100029', '2026-030', 'Doctorado en Ciencias de la Educación',           'DCE', null],
            ['Mg. Raúl Esteban Yaranga Solano',  'ryaranga@correo.edu.pe',     '40100030', '2026-031', 'Doctorado en Ciencias de la Educación',           'DCE', null],
        ];

        $groups = [];

        foreach ($data as [$name, $email, $dni, $code, $programText, $group, $subGroup]) {
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => 'alumno',
                'dni'      => $dni,
                'status'   => true,
            ]);

            AlumnoProfile::create([
                'user_id'        => $user->id,
                'code'           => $code,
                'promotion_year' => 2026,
                'program'        => $programText,
            ]);

            // Agregar al grupo del programa
            $groups[$group] ??= collect();
            $groups[$group]->push($user);

            // Agregar al sub-grupo de mención (ME solamente)
            if ($subGroup) {
                $groups[$subGroup] ??= collect();
                $groups[$subGroup]->push($user);
            }
        }

        return $groups;
    }

    // ─── Creación de cursos ───────────────────────────────────────────

    private function createCourses(Semester $semestre, Collection $docentes, array $alumnoGroups): void
    {
        $docenteIdx = 0;

        $mentionAbbr = [
            'Gestión Educativa'     => 'GE',
            'Enseñanza Estratégica' => 'EE',
            'Psicología Educativa'  => 'PE',
            'Educación Superior'    => 'ES',
        ];

        $programs = Program::where('status', 'active')
            ->whereIn('code', ['ME', 'MDES', 'MAE', 'MEIB', 'MTE', 'MIDU', 'MEI', 'DCE'])
            ->get();

        foreach ($programs as $program) {
            $studentsForProgram = $alumnoGroups[$program->code] ?? collect();

            if ($program->code === 'ME') {
                // ── Propedéutico (compartido, sem 0) ──
                $propItems = CurriculumItem::where('program_id', $program->id)
                    ->where('semester_number', 0)
                    ->whereNull('mention_id')
                    ->orderBy('order')
                    ->get();

                foreach ($propItems as $item) {
                    $course = $this->makeCourse(
                        $item,
                        sprintf('ME-P%02d', $item->order + 1),
                        $docentes, $docenteIdx++, $semestre, $program
                    );
                    $this->enroll($course, $studentsForProgram);
                }

                // ── Semestre 1 por cada mención ──
                $mentions = Mention::where('program_id', $program->id)->orderBy('order')->get();

                foreach ($mentions as $mention) {
                    $abbr     = $mentionAbbr[$mention->name] ?? 'XX';
                    $students = $alumnoGroups["ME-{$abbr}"] ?? collect();

                    $items = CurriculumItem::where('program_id', $program->id)
                        ->where('mention_id', $mention->id)
                        ->where('semester_number', 1)
                        ->orderBy('order')
                        ->get();

                    foreach ($items as $item) {
                        $course = $this->makeCourse(
                            $item,
                            sprintf('ME-%s-1%02d', $abbr, $item->order + 1),
                            $docentes, $docenteIdx++, $semestre, $program
                        );
                        $this->enroll($course, $students);
                    }
                }

                continue;
            }

            // ── Programas sin mención ──
            $semNum = $program->has_propedeutic ? 0 : 1;

            $items = CurriculumItem::where('program_id', $program->id)
                ->where('semester_number', $semNum)
                ->whereNull('mention_id')
                ->orderBy('order')
                ->get();

            if ($items->isEmpty()) {
                continue;
            }

            $semLabel = $semNum === 0 ? 'P' : (string) $semNum;

            foreach ($items as $item) {
                $course = $this->makeCourse(
                    $item,
                    sprintf('%s-%s%02d', $program->code, $semLabel, $item->order + 1),
                    $docentes, $docenteIdx++, $semestre, $program
                );
                $this->enroll($course, $studentsForProgram);
            }
        }
    }

    private function makeCourse(
        CurriculumItem $item,
        string $code,
        Collection $docentes,
        int $idx,
        Semester $semestre,
        Program $program,
    ): Course {
        $teacher = $docentes[$idx % $docentes->count()];

        return Course::create([
            'name'        => $item->course_name,
            'code'        => $code,
            'description' => "Curso correspondiente al {$item->semester_label} del programa {$program->name}.",
            'teacher_id'  => $teacher->id,
            'semester_id' => $semestre->id,
            'program_id'  => $program->id,
            'cycle'       => $item->semester_number ?: null,
            'year'        => $semestre->year,
            'semester'    => $semestre->period,
            'status'      => 'active',
        ]);
    }

    private function enroll(Course $course, Collection $students): void
    {
        foreach ($students as $student) {
            Enrollment::create([
                'course_id'   => $course->id,
                'user_id'     => $student->id,
                'status'      => 'active',
                'enrolled_at' => now(),
            ]);
        }
    }
}
