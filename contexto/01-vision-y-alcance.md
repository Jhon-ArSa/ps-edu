# 01 — Visión y Alcance del Sistema

## 1. Contexto Institucional

**PS-EDU** es la intranet académica de la **Facultad de Educación (FAEDU)** del posgrado de ADESA. Es un sistema web interno diseñado para centralizar la gestión académica, el aula virtual y la comunicación entre docentes, estudiantes y administración dentro del programa de posgrado.

No es una plataforma pública ni masiva. Es un entorno controlado para una comunidad de **200 a 300 estudiantes activos por semestre**, con un número reducido de docentes y un equipo administrativo pequeño.

---

## 2. Problema que Resuelve

Antes de este sistema, la gestión académica dependía de:
- Grupos de WhatsApp para comunicaciones y entrega de materiales
- Correo electrónico para tareas y resultados
- Hojas de cálculo para notas y registros
- Documentos compartidos sin control de versiones

Este sistema reemplaza esas prácticas dispersas con un entorno centralizado, seguro y trazable.

---

## 3. Visión

> Ser la plataforma de referencia para la gestión académica del posgrado de la Facultad de Educación, ofreciendo a docentes y estudiantes una experiencia digital simple, confiable y accesible desde cualquier dispositivo.

---

## 4. Objetivos del Sistema

### Objetivo General
Desarrollar una plataforma web de gestión académica y aula virtual que permita administrar la actividad educativa del posgrado de manera centralizada, segura y eficiente.

### Objetivos Específicos
1. Centralizar la gestión de usuarios, cursos y semestres académicos.
2. Proveer un aula virtual organizada por semanas para cada asignatura.
3. Gestionar materiales educativos, tareas, entregas y calificaciones.
4. Facilitar la comunicación interna mediante anuncios y foros.
5. Ofrecer al administrador reportes y supervisión de la actividad académica.
6. Mantener la plataforma disponible y con buen rendimiento para 200–300 usuarios.

---

## 5. Alcance

### Incluido en el sistema

| Área | Descripción |
|---|---|
| Autenticación | Login, recuperación de contraseña, roles, bloqueo de cuentas |
| Administración | Gestión de usuarios, cursos, semestres, matrículas, anuncios, configuración |
| Aula virtual | Semanas, materiales (PDF/PPT/video/link), tareas, entregas |
| Evaluaciones | Exámenes y cuestionarios en línea con preguntas de opción múltiple, V/F y respuesta corta |
| Calificaciones | Libreta de notas por curso, promedio automático, historial |
| Comunicación | Foro de discusión por curso, anuncios institucionales |
| Reportes | Estadísticas de actividad, notas promedio, entregas pendientes |
| Notificaciones | Alertas en sistema para tareas, calificaciones y anuncios |
| Perfil | Perfil personal, escalafón docente, datos del alumno |

### Fuera del alcance (por ahora)
- Chat en tiempo real (mensajería instantánea)
- Videoconferencias integradas (se enlazan como recursos externos: Zoom, Meet)
- App móvil nativa
- Integración con sistemas externos (SUNEDU, MINEDU, ERP institucional)
- Pagos o matrículas financieras

---

## 6. Usuarios del Sistema

### Administrador / Directivo
Gestiona la plataforma completa. Registra usuarios, crea semestres, asigna docentes, matricula estudiantes, supervisa actividad y genera reportes. Puede haber más de un administrador.

### Docente
Gestiona su aula virtual. Sube materiales, crea tareas y evaluaciones, califica estudiantes, publica anuncios del curso y participa en foros.

### Alumno (Estudiante)
Accede a sus cursos matriculados. Ve materiales, entrega tareas, responde evaluaciones, consulta sus calificaciones y participa en foros.

---

## 7. Estructura Académica

El sistema organiza la información de forma jerárquica:

```
Institución (FAEDU)
 └── Semestre (ej. 2025-I)
      └── Asignatura / Curso
           ├── Docente responsable
           ├── Estudiantes matriculados
           └── Contenido semanal (Semanas 1–16)
                ├── Materiales (PDF, PPT, video, link)
                ├── Tareas + Entregas
                └── Evaluaciones en línea
```

**Ejemplo real:**
```
Semestre: 2025-I
  Asignatura: Investigación Educativa I
  Docente: Dr. Marcos Quispe
  Semana 1
    - Sílabo (PDF)
    - Presentación de introducción (PPT)
    - Video de bienvenida (link YouTube)
    - Tarea 1: "Planteamiento del problema" (entrega: 15/03/2025)
  Semana 2
    - Lectura obligatoria (PDF)
    - Evaluación diagnóstica (cuestionario en línea, 10 min)
```

---

## 8. Restricciones Conocidas

| Restricción | Descripción |
|---|---|
| Hosting | AWS RDS (MySQL) como base de datos; servidor de aplicaciones a definir |
| Recursos de archivo | Almacenamiento en disco público del servidor (sin S3 inicialmente) |
| Mail | Actualmente en modo `log`; para producción se debe configurar SMTP real |
| Tiempo real | No se usan WebSockets; las notificaciones son por polling |
| Concurrencia | Diseñado para 200–300 usuarios *activos* (no simultáneos en el mismo instante) |

---

## 9. Criterios de Éxito

- Un docente puede cargar materiales y crear tareas sin asistencia técnica.
- Un estudiante puede acceder a sus cursos, entregar una tarea y ver su calificación en menos de 3 clics.
- El sistema responde en menos de 2 segundos bajo carga normal.
- El administrador puede ver el estado académico general desde el dashboard.
- Las calificaciones quedan registradas y son auditables.
