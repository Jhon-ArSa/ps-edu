# 04 — Esquema de Base de Datos

## 1. Diagrama General de Relaciones

```
semesters
    │
    ▼ (1:N)
courses ──────────────────────────────────────────────┐
    │ teacher_id (FK → users)                          │
    │                                              enrollments
    ▼ (1:N)                                           (N:M users ↔ courses)
weeks
    ├──► materials (1:N)
    ├──► tasks (1:N)
    │         └──► submissions (1:N → users)
    └──► evaluations (1:N)
              ├──► questions (1:N)
              │         └──► question_options (1:N)
              └──► evaluation_attempts (1:N → users)
                        └──► attempt_answers (1:N)

users
    ├──► docente_profiles (1:1)
    ├──► alumno_profiles (1:1)
    ├──► announcements (1:N — author_id)
    ├──► notifications (1:N)
    └──► forum_topics / forum_replies (1:N)

courses
    ├──► forum_topics (1:N)
    │         └──► forum_replies (1:N)
    └──► grade_items (1:N)
              └──► grades (1:N → users)

settings (key-value store, sin relaciones)
```

---

## 2. Tablas del Sistema

### `users`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
name            VARCHAR(255) NOT NULL
email           VARCHAR(255) UNIQUE NOT NULL
email_verified_at TIMESTAMP NULL
password        VARCHAR(255) NOT NULL
role            ENUM('admin','docente','alumno') NOT NULL
dni             VARCHAR(20) NULL
phone           VARCHAR(20) NULL
avatar          VARCHAR(255) NULL              -- ruta relativa en disco público
status          TINYINT(1) DEFAULT 1           -- 1=activo, 0=bloqueado
remember_token  VARCHAR(100) NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(email)
  INDEX(role)
  INDEX(status)
```

### `docente_profiles`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
title           VARCHAR(100) NULL              -- ej. "Dr.", "Mg.", "Lic."
degree          VARCHAR(255) NULL              -- grado académico
specialty       VARCHAR(255) NULL
category        VARCHAR(100) NULL              -- ej. "Principal", "Asociado"
years_of_service INT NULL
bio             TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(user_id)
```

### `alumno_profiles`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
code            VARCHAR(50) UNIQUE NOT NULL    -- código de matrícula
promotion_year  YEAR NULL
program         VARCHAR(255) NULL              -- ej. "Maestría en Educación"
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(user_id)
  UNIQUE(code)
```

### `semesters` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
name            VARCHAR(50) NOT NULL           -- ej. "2025-I"
year            YEAR NOT NULL
period          ENUM('I','II') NOT NULL
start_date      DATE NOT NULL
end_date        DATE NOT NULL
status          ENUM('active','closed') DEFAULT 'closed'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(year, period)
  INDEX(status)
```

> **Regla de negocio:** Solo puede haber un semestre con `status = 'active'` a la vez. Se maneja con una transacción al activar.

### `courses`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
semester_id     BIGINT UNSIGNED NULL FK → semesters(id) ON DELETE SET NULL  *(nuevo)*
name            VARCHAR(255) NOT NULL
code            VARCHAR(50) UNIQUE NOT NULL
description     TEXT NULL
teacher_id      BIGINT UNSIGNED NOT NULL FK → users(id) ON RESTRICT  -- no borrar docente con cursos
program         VARCHAR(255) NULL
cycle           VARCHAR(50) NULL               -- ej. "Primer ciclo"
year            YEAR NULL
semester        ENUM('I','II') NULL            -- legado, se mantendrá para compatibilidad
status          ENUM('active','inactive') DEFAULT 'active'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(code)
  INDEX(teacher_id)
  INDEX(semester_id)
  INDEX(status)
```

### `enrollments`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
course_id       BIGINT UNSIGNED NOT NULL FK → courses(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
enrolled_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
status          ENUM('active','dropped','inactive') DEFAULT 'active'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(course_id, user_id)
  INDEX(user_id)
  INDEX(status)
```

### `weeks`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
course_id       BIGINT UNSIGNED NOT NULL FK → courses(id) ON DELETE CASCADE
number          TINYINT UNSIGNED NOT NULL      -- 1 a 16
title           VARCHAR(255) NOT NULL
description     TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(course_id, number)
```

### `materials`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
week_id         BIGINT UNSIGNED NOT NULL FK → weeks(id) ON DELETE CASCADE
type            ENUM('file','link','video') NOT NULL
title           VARCHAR(255) NOT NULL
description     TEXT NULL
file_path       VARCHAR(500) NULL              -- solo si type = file
url             VARCHAR(2048) NULL             -- solo si type = link/video
order           TINYINT UNSIGNED DEFAULT 0
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(week_id, order)
```

### `tasks`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
week_id         BIGINT UNSIGNED NOT NULL FK → weeks(id) ON DELETE CASCADE
title           VARCHAR(255) NOT NULL
description     TEXT NULL
instructions    TEXT NULL
due_date        DATETIME NULL
max_score       DECIMAL(4,1) DEFAULT 20.0
file_path       VARCHAR(500) NULL              -- archivo de instrucciones (opcional)
status          ENUM('active','inactive') DEFAULT 'active'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(week_id)
  INDEX(status)
  INDEX(due_date)
```

### `submissions` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
task_id         BIGINT UNSIGNED NOT NULL FK → tasks(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
file_path       VARCHAR(500) NULL
comments        TEXT NULL
submitted_at    TIMESTAMP NULL
status          ENUM('pending','submitted','graded') DEFAULT 'pending'
score           DECIMAL(4,1) NULL
feedback        TEXT NULL
graded_at       TIMESTAMP NULL
graded_by       BIGINT UNSIGNED NULL FK → users(id) ON DELETE SET NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(task_id, user_id)
  INDEX(user_id)
  INDEX(status)
  INDEX(graded_at)
```

### `evaluations` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
week_id         BIGINT UNSIGNED NOT NULL FK → weeks(id) ON DELETE CASCADE
title           VARCHAR(255) NOT NULL
description     TEXT NULL
instructions    TEXT NULL
time_limit      SMALLINT UNSIGNED NULL         -- en minutos; NULL = sin límite
start_at        DATETIME NULL                  -- NULL = disponible inmediatamente
end_at          DATETIME NULL                  -- NULL = sin fecha de cierre
max_attempts    TINYINT UNSIGNED DEFAULT 1
show_results    TINYINT(1) DEFAULT 0           -- mostrar respuestas correctas al alumno
status          ENUM('draft','active','closed') DEFAULT 'draft'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(week_id)
  INDEX(status)
  INDEX(start_at, end_at)
```

### `questions` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
evaluation_id   BIGINT UNSIGNED NOT NULL FK → evaluations(id) ON DELETE CASCADE
type            ENUM('multiple_single','multiple_multi','true_false','short') NOT NULL
body            TEXT NOT NULL
order           TINYINT UNSIGNED DEFAULT 0
points          DECIMAL(4,1) DEFAULT 1.0
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(evaluation_id, order)
```

### `question_options` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
question_id     BIGINT UNSIGNED NOT NULL FK → questions(id) ON DELETE CASCADE
body            VARCHAR(500) NOT NULL
is_correct      TINYINT(1) DEFAULT 0
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(question_id)
```

### `evaluation_attempts` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
evaluation_id   BIGINT UNSIGNED NOT NULL FK → evaluations(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
started_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
submitted_at    TIMESTAMP NULL
score           DECIMAL(5,2) NULL              -- calculado al enviar
status          ENUM('in_progress','completed','expired') DEFAULT 'in_progress'
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(evaluation_id, user_id)
  INDEX(status)
```

### `attempt_answers` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
attempt_id      BIGINT UNSIGNED NOT NULL FK → evaluation_attempts(id) ON DELETE CASCADE
question_id     BIGINT UNSIGNED NOT NULL FK → questions(id) ON DELETE CASCADE
option_id       BIGINT UNSIGNED NULL FK → question_options(id) ON DELETE SET NULL
text_answer     TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(attempt_id, question_id)
```

### `announcements`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
title           VARCHAR(255) NOT NULL
content         TEXT NOT NULL
author_id       BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
target_role     ENUM('all','docente','alumno') DEFAULT 'all'
published_at    TIMESTAMP NULL                 -- NULL = borrador
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(author_id)
  INDEX(target_role)
  INDEX(published_at)
```

### `forum_topics` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
course_id       BIGINT UNSIGNED NOT NULL FK → courses(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
title           VARCHAR(255) NOT NULL
body            TEXT NOT NULL
is_pinned       TINYINT(1) DEFAULT 0
is_closed       TINYINT(1) DEFAULT 0
replies_count   INT UNSIGNED DEFAULT 0         -- denormalizado para performance
last_reply_at   TIMESTAMP NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(course_id, is_pinned, created_at)
  INDEX(user_id)
```

### `forum_replies` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
topic_id        BIGINT UNSIGNED NOT NULL FK → forum_topics(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
body            TEXT NOT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(topic_id, created_at)
  INDEX(user_id)
```

### `grade_items` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
course_id       BIGINT UNSIGNED NOT NULL FK → courses(id) ON DELETE CASCADE
name            VARCHAR(255) NOT NULL
type            ENUM('task','evaluation','participation','oral','final') NOT NULL
reference_id    BIGINT UNSIGNED NULL           -- task_id o evaluation_id si aplica
weight          DECIMAL(5,2) DEFAULT 0         -- porcentaje del promedio final
max_score       DECIMAL(4,1) DEFAULT 20.0
order           TINYINT UNSIGNED DEFAULT 0
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(course_id, order)
  INDEX(type, reference_id)
```

### `grades` *(nuevo)*
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
grade_item_id   BIGINT UNSIGNED NOT NULL FK → grade_items(id) ON DELETE CASCADE
user_id         BIGINT UNSIGNED NOT NULL FK → users(id) ON DELETE CASCADE
score           DECIMAL(4,1) NOT NULL
comments        TEXT NULL
graded_by       BIGINT UNSIGNED NULL FK → users(id) ON DELETE SET NULL
graded_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  UNIQUE(grade_item_id, user_id)
  INDEX(user_id)
```

### `notifications` *(Laravel nativo)*
```sql
id              CHAR(36) PK                    -- UUID
type            VARCHAR(255)                   -- clase de notificación
notifiable_type VARCHAR(255)                   -- polimórfico
notifiable_id   BIGINT UNSIGNED
data            JSON
read_at         TIMESTAMP NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP

INDEXES:
  INDEX(notifiable_type, notifiable_id)
  INDEX(read_at)
```

### `settings`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PK
key             VARCHAR(255) UNIQUE NOT NULL
value           TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## 3. Tablas del Framework (Laravel core)

| Tabla | Uso |
|---|---|
| `password_reset_tokens` | Tokens de recuperación de contraseña |
| `sessions` | Sesiones de usuario en base de datos |
| `cache` | Cache con driver database |
| `jobs` | Cola de trabajos pendientes |
| `failed_jobs` | Trabajos fallidos para diagnóstico |

---

## 4. Índices Críticos para Performance

Los siguientes índices son **fundamentales** para evitar full table scans con 200–300 usuarios:

```sql
-- Consultas frecuentes del alumno
ALTER TABLE enrollments ADD INDEX idx_user_status (user_id, status);

-- Consultas frecuentes del docente
ALTER TABLE courses ADD INDEX idx_teacher_status (teacher_id, status);

-- Búsqueda de entregas por tarea
ALTER TABLE submissions ADD INDEX idx_task_status (task_id, status);

-- Notificaciones no leídas
ALTER TABLE notifications ADD INDEX idx_notifiable_read (notifiable_id, read_at);

-- Semestre activo (lectura constante)
ALTER TABLE semesters ADD INDEX idx_status (status);

-- Foro por curso, ordenado
ALTER TABLE forum_topics ADD INDEX idx_course_pinned_date (course_id, is_pinned, created_at);

-- Anuncios publicados por rol
ALTER TABLE announcements ADD INDEX idx_role_published (target_role, published_at);
```

---

## 5. Convenciones de Nomenclatura

| Elemento | Convención | Ejemplo |
|---|---|---|
| Tablas | Plural, snake_case | `forum_topics` |
| Columnas FK | `{tabla_singular}_id` | `course_id` |
| Enums | Minúsculas, guion bajo | `in_progress` |
| Timestamps | `created_at`, `updated_at` siempre | — |
| Soft delete | No se usa; se usa campo `status` | `status = inactive` |
| Booleanos | TINYINT(1) | `is_pinned`, `is_correct` |
