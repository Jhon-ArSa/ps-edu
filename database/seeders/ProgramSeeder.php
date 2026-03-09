<?php

namespace Database\Seeders;

use App\Models\CurriculumItem;
use App\Models\Mention;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing docente as coordinator, or first admin
        $coordinator = User::where('role', 'docente')->first()
                        ?? User::where('role', 'admin')->first();

        // ═══════════════════════════════════════════════════════
        // 1. MAESTRÍA EN EDUCACIÓN (con 4 menciones + propedéutico)
        // ═══════════════════════════════════════════════════════
        $me = Program::create([
            'name'               => 'Maestría en Educación',
            'code'               => 'ME',
            'degree_type'        => 'maestria',
            'description'        => 'Programa de Maestría en Educación con mención en cuatro especialidades. Incluye un semestre propedéutico de nivelación y tres semestres de especialización.',
            'duration_semesters' => 3,
            'has_propedeutic'    => true,
            'total_credits'      => 72,
            'resolution'         => 'RR N° 001-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        // Propedéutico compartido (semester 0, mention_id = null)
        $this->seedSharedCurriculum($me->id, [
            0 => [
                'Epistemología de la Educación',
                'Estadística Aplicada a la Investigación Educativa',
                'Metodología de la Investigación Científica',
            ],
        ]);

        $this->seedMention($me, 'Gestión Educativa', 'Forma profesionales especializados en la administración y gestión de instituciones educativas.', 1, [
            1 => ['Teoría de la Gestión Educativa', 'Planificación Estratégica Educativa', 'Gestión del Talento Humano en Educación', 'Seminario de Investigación I'],
            2 => ['Gestión de la Calidad Educativa', 'Política y Legislación Educativa', 'Liderazgo y Cultura Organizacional', 'Seminario de Investigación II'],
            3 => ['Evaluación y Acreditación Institucional', 'Gestión Financiera en Educación', 'Taller de Tesis'],
        ]);

        $this->seedMention($me, 'Enseñanza Estratégica', 'Desarrolla competencias en estrategias didácticas y metodologías innovadoras de enseñanza-aprendizaje.', 2, [
            1 => ['Teorías del Aprendizaje y la Enseñanza', 'Estrategias Didácticas Innovadoras', 'Tecnología Educativa', 'Seminario de Investigación I'],
            2 => ['Diseño Curricular por Competencias', 'Evaluación del Aprendizaje', 'Neuroeducación y Procesos Cognitivos', 'Seminario de Investigación II'],
            3 => ['Innovación Pedagógica y Creatividad', 'Didáctica de la Especialidad', 'Taller de Tesis'],
        ]);

        $this->seedMention($me, 'Psicología Educativa', 'Profundiza en los procesos psicológicos que intervienen en el aprendizaje y la formación integral del estudiante.', 3, [
            1 => ['Psicología del Desarrollo y del Aprendizaje', 'Orientación y Tutoría Educativa', 'Psicometría y Evaluación Psicológica', 'Seminario de Investigación I'],
            2 => ['Problemas de Aprendizaje y Atención a la Diversidad', 'Psicología Social en el Aula', 'Intervención Psicopedagógica', 'Seminario de Investigación II'],
            3 => ['Neuropsicología Educativa', 'Convivencia Escolar y Bienestar Emocional', 'Taller de Tesis'],
        ]);

        $this->seedMention($me, 'Educación Superior', 'Orienta la formación de docentes universitarios con enfoque en la mejora de la enseñanza superior.', 4, [
            1 => ['Docencia Universitaria', 'Currículo y Didáctica en Educación Superior', 'Gestión Académica Universitaria', 'Seminario de Investigación I'],
            2 => ['Evaluación y Acreditación en Educación Superior', 'Educación Virtual y a Distancia', 'Responsabilidad Social Universitaria', 'Seminario de Investigación II'],
            3 => ['Políticas de Educación Superior', 'Investigación en Educación Superior', 'Taller de Tesis'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 2. MAESTRÍA EN DIDÁCTICA DE LA EDUCACIÓN SUPERIOR
        // ═══════════════════════════════════════════════════════
        $mdes = Program::create([
            'name'               => 'Maestría en Didáctica de la Educación Superior',
            'code'               => 'MDES',
            'degree_type'        => 'maestria',
            'description'        => 'Programa orientado a fortalecer las competencias pedagógicas y didácticas de docentes del nivel superior, integrando metodologías activas, tecnologías educativas y evaluación formativa.',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 64,
            'resolution'         => 'RR N° 003-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($mdes->id, [
            1 => ['Fundamentos de la Didáctica Superior', 'Psicología del Aprendizaje Adulto', 'Diseño Instruccional', 'Metodología de la Investigación I'],
            2 => ['Estrategias de Enseñanza en Educación Superior', 'Evaluación por Competencias', 'TIC Aplicadas a la Docencia Superior', 'Metodología de la Investigación II'],
            3 => ['Innovación Curricular Universitaria', 'Tutoría y Acompañamiento Académico', 'Práctica Pedagógica Universitaria', 'Seminario de Tesis I'],
            4 => ['Gestión del Conocimiento en la Universidad', 'Internacionalización de la Educación Superior', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 3. MAESTRÍA EN ADMINISTRACIÓN DE LA EDUCACIÓN
        // ═══════════════════════════════════════════════════════
        $mae = Program::create([
            'name'               => 'Maestría en Administración de la Educación',
            'code'               => 'MAE',
            'degree_type'        => 'maestria',
            'description'        => 'Forma líderes educativos con competencias en planificación, organización, dirección y evaluación de instituciones educativas de todos los niveles.',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 68,
            'resolution'         => 'RR N° 004-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($mae->id, [
            1 => ['Teoría de la Administración Educativa', 'Planificación y Gestión Estratégica', 'Economía de la Educación', 'Metodología de la Investigación I'],
            2 => ['Gestión de Recursos Humanos en Educación', 'Marco Normativo y Políticas Educativas', 'Gestión de Proyectos Educativos', 'Metodología de la Investigación II'],
            3 => ['Liderazgo Pedagógico y Directivo', 'Gestión de la Calidad y Acreditación', 'Clima Organizacional e Institucional', 'Seminario de Tesis I'],
            4 => ['Gerencia Social y Educativa', 'Evaluación de Instituciones Educativas', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 4. MAESTRÍA EN EDUCACIÓN INTERCULTURAL BILINGÜE
        // ═══════════════════════════════════════════════════════
        $meib = Program::create([
            'name'               => 'Maestría en Educación Intercultural Bilingüe',
            'code'               => 'MEIB',
            'degree_type'        => 'maestria',
            'description'        => 'Programa que forma investigadores y docentes especializados en educación intercultural bilingüe, con enfoque en la valoración de las lenguas originarias y la diversidad cultural peruana.',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 64,
            'resolution'         => 'RR N° 005-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($meib->id, [
            1 => ['Fundamentos de la Educación Intercultural', 'Lingüística Aplicada a la EIB', 'Cosmovisión Andina y Amazónica', 'Metodología de la Investigación I'],
            2 => ['Didáctica de Lenguas Originarias', 'Políticas Lingüísticas y Educativas', 'Currículo Intercultural', 'Metodología de la Investigación II'],
            3 => ['Producción de Materiales en Lenguas Originarias', 'Identidad Cultural y Educación', 'Sociolingüística Educativa', 'Seminario de Tesis I'],
            4 => ['Gestión de la EIB en Contextos Rurales', 'Revitalización de Lenguas Originarias', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 5. MAESTRÍA EN TECNOLOGÍA EDUCATIVA
        // ═══════════════════════════════════════════════════════
        $mte = Program::create([
            'name'               => 'Maestría en Tecnología Educativa',
            'code'               => 'MTE',
            'degree_type'        => 'maestria',
            'description'        => 'Forma profesionales capaces de diseñar, implementar y evaluar soluciones tecnológicas para la mejora de los procesos de enseñanza-aprendizaje en todos los niveles educativos.',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 64,
            'resolution'         => 'RR N° 006-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($mte->id, [
            1 => ['Fundamentos de la Tecnología Educativa', 'Diseño de Entornos Virtuales de Aprendizaje', 'Alfabetización Digital y Mediática', 'Metodología de la Investigación I'],
            2 => ['Desarrollo de Recursos Educativos Digitales', 'Inteligencia Artificial en Educación', 'Gamificación y Aprendizaje Basado en Juegos', 'Metodología de la Investigación II'],
            3 => ['E-Learning y Educación a Distancia', 'Analítica del Aprendizaje (Learning Analytics)', 'Robótica Educativa', 'Seminario de Tesis I'],
            4 => ['Transformación Digital en Instituciones Educativas', 'Evaluación de Tecnologías Educativas', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 6. MAESTRÍA EN INVESTIGACIÓN Y DOCENCIA UNIVERSITARIA
        // ═══════════════════════════════════════════════════════
        $midu = Program::create([
            'name'               => 'Maestría en Investigación y Docencia Universitaria',
            'code'               => 'MIDU',
            'degree_type'        => 'maestria',
            'description'        => 'Programa que integra la investigación científica con la práctica docente universitaria, formando profesionales con capacidad crítica e innovadora para la producción del conocimiento.',
            'duration_semesters' => 4,
            'has_propedeutic'    => true,
            'total_credits'      => 72,
            'resolution'         => 'RR N° 007-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($midu->id, [
            0 => ['Epistemología de las Ciencias', 'Estadística para la Investigación', 'Redacción Científica'],
            1 => ['Teoría de la Educación Universitaria', 'Diseño y Evaluación Curricular', 'Investigación Educativa I', 'Didáctica Universitaria'],
            2 => ['Desarrollo de Competencias Docentes', 'Investigación Educativa II', 'Ética y Deontología Profesional', 'Gestión del Conocimiento'],
            3 => ['Investigación Cuantitativa Aplicada', 'Investigación Cualitativa Aplicada', 'Tutoría y Mentoría Universitaria', 'Seminario de Tesis I'],
            4 => ['Publicación y Difusión Científica', 'Práctica de Investigación', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 7. MAESTRÍA EN EDUCACIÓN INICIAL
        // ═══════════════════════════════════════════════════════
        $mei = Program::create([
            'name'               => 'Maestría en Educación Inicial',
            'code'               => 'MEI',
            'degree_type'        => 'maestria',
            'description'        => 'Forma especialistas en la atención integral de la primera infancia, con enfoque en el desarrollo infantil, la estimulación temprana y la pedagogía para niños de 0 a 5 años.',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 64,
            'resolution'         => 'RR N° 008-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($mei->id, [
            1 => ['Desarrollo Infantil Integral', 'Neurociencia y Educación Temprana', 'Currículo en Educación Inicial', 'Metodología de la Investigación I'],
            2 => ['Didáctica de la Educación Inicial', 'Psicomotricidad y Expresión Corporal', 'Familia y Comunidad en Educación Infantil', 'Metodología de la Investigación II'],
            3 => ['Estimulación Temprana y Atención a la Diversidad', 'Juego y Creatividad en la Infancia', 'Literatura Infantil y Animación a la Lectura', 'Seminario de Tesis I'],
            4 => ['Políticas de Atención a la Primera Infancia', 'Evaluación del Desarrollo Infantil', 'Seminario de Tesis II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 8. DOCTORADO EN CIENCIAS DE LA EDUCACIÓN
        // ═══════════════════════════════════════════════════════
        $dce = Program::create([
            'name'               => 'Doctorado en Ciencias de la Educación',
            'code'               => 'DCE',
            'degree_type'        => 'doctorado',
            'description'        => 'Programa de Doctorado orientado a la investigación educativa avanzada y la producción de conocimiento científico en el campo de la educación.',
            'duration_semesters' => 6,
            'has_propedeutic'    => false,
            'total_credits'      => 96,
            'resolution'         => 'RR N° 009-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        $this->seedSharedCurriculum($dce->id, [
            1 => ['Epistemología y Filosofía de la Educación', 'Metodología de la Investigación Educativa I', 'Teorías y Paradigmas Educativos', 'Seminario Doctoral I'],
            2 => ['Problemas Contemporáneos de la Educación', 'Metodología de la Investigación Educativa II', 'Políticas Públicas en Educación', 'Seminario Doctoral II'],
            3 => ['Investigación Cualitativa en Educación', 'Estadística Multivariada', 'Currículo y Evaluación Educativa', 'Seminario Doctoral III'],
            4 => ['Investigación Cuantitativa Avanzada', 'Educación Comparada', 'Tecnología e Innovación Educativa', 'Seminario Doctoral IV'],
            5 => ['Diseño y Validación de Instrumentos', 'Ética en la Investigación Educativa', 'Taller de Tesis Doctoral I'],
            6 => ['Publicación Científica y Difusión del Conocimiento', 'Taller de Tesis Doctoral II'],
        ]);

        // ═══════════════════════════════════════════════════════
        // 9. SEGUNDA ESPECIALIDAD EN DIDÁCTICA UNIVERSITARIA
        // ═══════════════════════════════════════════════════════
        Program::create([
            'name'               => 'Segunda Especialidad en Didáctica Universitaria',
            'code'               => 'SEDU',
            'degree_type'        => 'segunda_especialidad',
            'description'        => 'Programa de Segunda Especialidad dirigido a profesionales que desean obtener formación complementaria en estrategias de enseñanza para el nivel universitario.',
            'duration_semesters' => 2,
            'has_propedeutic'    => false,
            'total_credits'      => 40,
            'resolution'         => 'RR N° 010-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);

        // ═══════════════════════════════════════════════════════
        // 10. DIPLOMADO EN GESTIÓN ESCOLAR
        // ═══════════════════════════════════════════════════════
        Program::create([
            'name'               => 'Diplomado en Gestión Escolar',
            'code'               => 'DGE',
            'degree_type'        => 'diplomado',
            'description'        => 'Programa de formación continua para directivos y docentes en gestión y liderazgo de instituciones educativas de educación básica.',
            'duration_semesters' => 1,
            'has_propedeutic'    => false,
            'total_credits'      => 24,
            'resolution'         => 'RR N° 011-2024-UNCP',
            'coordinator_id'     => $coordinator?->id,
            'status'             => 'active',
        ]);
    }

    /**
     * Seed a mention with its curriculum items.
     */
    private function seedMention(Program $program, string $name, string $description, int $order, array $semesters): void
    {
        $mention = $program->mentions()->create([
            'name'        => $name,
            'description' => $description,
            'order'       => $order,
            'status'      => 'active',
        ]);

        foreach ($semesters as $sem => $courses) {
            foreach ($courses as $i => $courseName) {
                CurriculumItem::create([
                    'program_id'      => $program->id,
                    'mention_id'      => $mention->id,
                    'semester_number'  => $sem,
                    'course_name'     => $courseName,
                    'credits'         => 4,
                    'is_elective'     => false,
                    'order'           => $i,
                ]);
            }
        }
    }

    /**
     * Seed shared curriculum items (no mention) grouped by semester.
     */
    private function seedSharedCurriculum(int $programId, array $semesters): void
    {
        foreach ($semesters as $sem => $courses) {
            foreach ($courses as $i => $name) {
                CurriculumItem::create([
                    'program_id'      => $programId,
                    'mention_id'      => null,
                    'semester_number'  => $sem,
                    'course_name'     => $name,
                    'credits'         => 4,
                    'is_elective'     => false,
                    'order'           => $i,
                ]);
            }
        }
    }
}
