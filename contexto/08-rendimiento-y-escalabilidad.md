# 08 — Rendimiento y Escalabilidad

## 1. Contexto de Carga

El sistema debe soportar:
- **200–300 estudiantes activos** por semestre
- **20–40 docentes** activos
- **2–5 administradores**

Picos de uso esperados:
- **Inicio de semestre**: matrículas masivas (Admin)
- **Durante clase**: múltiples alumnos accediendo al mismo curso simultáneamente (30–50 usuarios en 10 minutos)
- **Fecha límite de tarea**: múltiples entregas en la última hora
- **Publicación de notas**: spike de consultas de alumnos

> **Objetivo:** Respuesta < 200ms en el P95 bajo carga normal. Sin errores de timeout bajo picos esperados.

---

## 2. Estrategia de Base de Datos

### 2.1 Eager Loading (sin N+1 queries)

El problema más común y costoso en Laravel. **Siempre** cargar relaciones con `with()`.

```php
// ❌ MAL — N+1: genera una query por cada curso
$courses = Course::all();
foreach ($courses as $course) {
    echo $course->teacher->name; // query por cada iteración
}

// ✅ BIEN — 2 queries totales
$courses = Course::with('teacher')->get();

// ✅ BIEN — nested eager loading
$course = Course::with([
    'weeks' => fn($q) => $q->orderBy('number'),
    'weeks.materials' => fn($q) => $q->orderBy('order'),
    'weeks.tasks',
    'enrollments' => fn($q) => $q->where('status', 'active'),
    'enrollments.user'
])->findOrFail($id);
```

### 2.2 Select selectivo (no `SELECT *`)

```php
// ❌ MAL — trae todas las columnas incluyendo password, avatar, etc.
$users = User::where('role', 'alumno')->get();

// ✅ BIEN — solo lo necesario para la vista
$users = User::select('id', 'name', 'email', 'dni')
             ->where('role', 'alumno')
             ->get();
```

### 2.3 Paginación (obligatoria en listas)

Ninguna lista debe cargar todos los registros. Mínimo paginar con 15–25 elementos:

```php
// Listas de usuarios, cursos, entregas, etc.
$users = User::where('role', 'alumno')->paginate(20);

// Para AJAX infinite scroll (futuro)
$items = Task::where('week_id', $weekId)->simplePaginate(10);
```

### 2.4 Conteos con `withCount`

```php
// ❌ MAL — carga toda la relación para contar
$course->enrollments->count();

// ✅ BIEN — COUNT en SQL, sin cargar los registros
$courses = Course::withCount([
    'enrollments as students_count' => fn($q) => $q->where('status', 'active'),
    'weeks'
])->get();
```

### 2.5 Índices Críticos

Ver `04-base-de-datos.md` para el listado completo. Los más importantes:

```sql
-- Los alumnos consultan esto en cada carga de dashboard
INDEX(enrollments: user_id, status)

-- Los docentes consultan esto constantemente
INDEX(courses: teacher_id, status)

-- Búsqueda de usuarios en admin
INDEX(users: role, status)
-- Considerar FULLTEXT en (name, email, dni) si la búsqueda es frecuente

-- Notificaciones no leídas (badge en header, cada page load)
INDEX(notifications: notifiable_id, read_at)
```

---

## 3. Estrategia de Cache

### 3.1 Cache del modelo Setting

Ya implementado correctamente. El `Setting::get()` usa `Cache::rememberForever()`.
Al hacer `Setting::set()`, invalida la cache de esa clave.

```php
// Patrón correcto ya en uso
public static function get(string $key, $default = null)
{
    return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
        return static::where('key', $key)->value('value') ?? $default;
    });
}
```

### 3.2 Cache del semestre activo

El semestre activo se consulta en casi todas las páginas del admin. Cachear:

```php
// En el modelo Semester
public static function getActive(): ?self
{
    return Cache::remember('semester_active', 300, function () {
        return static::where('status', 'active')->first();
    });
}

// Al cambiar el semestre activo, invalidar
Cache::forget('semester_active');
```

### 3.3 Cache de conteos del dashboard

El dashboard admin hace varios COUNT. Cachear por 5 minutos:

```php
$stats = Cache::remember('admin_dashboard_stats', 300, function () {
    return [
        'total_students'  => User::where('role', 'alumno')->where('status', true)->count(),
        'total_teachers'  => User::where('role', 'docente')->where('status', true)->count(),
        'active_courses'  => Course::where('status', 'active')->count(),
        'total_enrollments' => Enrollment::where('status', 'active')->count(),
    ];
});
```

### 3.4 Upgrade de cache a Redis (producción recomendada)

Para mayor rendimiento en producción, reemplazar el driver `database` por Redis:

```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

```
# Instalación del driver
composer require predis/predis
```

Ningún cambio en el código de la aplicación — la abstracción de Laravel lo maneja.

---

## 4. Optimización de Sesiones

Las sesiones usan el driver `database`, escribiendo en la tabla `sessions` en cada request autenticado. Para escala media-alta:

- **Limpiar sesiones expiradas** con el comando `php artisan session:gc` en el scheduler.
- Si la carga crece, migrar sesiones a Redis también.

```php
// console.php — limpieza automática
Schedule::command('session:gc')->daily();
```

---

## 5. Optimización de Consultas de Archivos

Los archivos se almacenan en disco local. Para 200–300 alumnos subiendo tareas:

- Limitar tamaño de archivos de entrega: **máximo 10MB** por entrega (configurar en validación del controlador).
- Validar tipos MIME en el backend, no solo la extensión.
- Nombres de archivo: siempre generados por el sistema (`Str::random(40) . '.' . $ext`).
- Nunca usar el nombre original del archivo en la ruta de almacenamiento.

```php
// Almacenamiento seguro de entregas
$path = $file->storeAs(
    "submissions/{$task->id}",
    Str::random(40) . '.' . $file->getClientOriginalExtension(),
    'public'
);
```

---

## 6. Colas para Operaciones Pesadas

Las notificaciones y emails no deben ejecutarse en el ciclo de request-response. Usar colas:

```php
// Notificación en cola (no bloquea al usuario)
$user->notify(new TaskGraded($submission)->delay(now()->addSeconds(5)));

// Email en cola
Mail::to($user)->queue(new TaskDeadlineReminder($task));
```

Ejecutar el worker en el servidor:
```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Para producción, usar **Supervisor** para mantener el worker activo.

---

## 7. Optimización del Autoloader y Bootstrap Laravel

Comandos para producción:

```bash
# Optimizar autoloader de Composer
composer install --optimize-autoloader --no-dev

# Cachear configuración (evita leer .env en cada request)
php artisan config:cache

# Cachear rutas (evita parsear web.php en cada request)
php artisan route:cache

# Cachear vistas Blade compiladas
php artisan view:cache

# Ejecutar todo junto
php artisan optimize
```

> **Importante:** Ejecutar `php artisan optimize:clear` al hacer deploy para invalidar las caches optimizadas antes de regenerarlas.

---

## 8. Paginación y Carga de Vistas

### Limitar registros por defecto

| Lista | Por página |
|---|---|
| Usuarios (admin) | 20 |
| Cursos (admin/docente) | 15 |
| Matrículas | 20 |
| Anuncios | 10 |
| Entregas de tareas | 25 |
| Temas del foro | 15 |
| Respuestas del foro | 20 |
| Notificaciones | 10 |

### No cargar datos que no se muestran

Usar paginación siempre. Nunca `->get()` en listas paginadas.

---

## 9. Protección contra Abuso

### Rate Limiting

Laravel tiene rate limiting nativo. Aplicar en rutas sensibles:

```php
// En web.php o bootstrap/app.php
Route::middleware(['auth', 'throttle:60,1'])->group(...)  // 60 req/min

// Para el login (más restrictivo)
Route::post('/login', LoginController::class)
     ->middleware('throttle:10,5');  // 10 intentos cada 5 minutos
```

### Validación de archivos

```php
// En FormRequest o controlador — SIEMPRE validar
$request->validate([
    'file' => [
        'required',
        'file',
        'max:10240',  // 10MB
        'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,png,zip'
    ]
]);
```

### Prevención de IDOR

```php
// ❌ MAL — cualquier alumno autenticado puede acceder
public function show($submissionId) {
    $submission = Submission::findOrFail($submissionId);
    return view('...', compact('submission'));
}

// ✅ BIEN — solo el dueño puede ver su entrega
public function show($submissionId) {
    $submission = Submission::where('id', $submissionId)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
    return view('...', compact('submission'));
}
```

---

## 10. Monitoreo y Diagnóstico

### Herramientas disponibles
- **Laravel Pail** (dev): `php artisan pail` — tail de logs en tiempo real
- **Laravel Telescope** (dev opcional): UI para queries, jobs, cache hits/misses
- **Logs en producción**: `storage/logs/laravel.log` con rotación diaria

### Variables a monitorear en producción
| Métrica | Umbral de alarma |
|---|---|
| Tiempo de respuesta promedio | > 500ms |
| Queries por request | > 15 |
| Errores 500 | Cualquiera |
| Cola de jobs pendientes | > 100 |
| Espacio en disco | > 80% |
| Sesiones activas en BD | > 400 |

### Query logging en development
```php
// En AppServiceProvider — solo en local
if (app()->environment('local')) {
    DB::listen(function ($query) {
        if ($query->time > 200) {
            Log::warning('Slow query detected', [
                'sql'      => $query->sql,
                'bindings' => $query->bindings,
                'time_ms'  => $query->time,
            ]);
        }
    });
}
```

---

## 11. Checklist de Performance por Sprint

Antes de marcar una feature como completa, verificar:

- [ ] ¿Todas las relaciones usadas en la vista están en `with()`?
- [ ] ¿Las listas están paginadas?
- [ ] ¿Se usa `withCount()` en lugar de cargar colecciones para contar?
- [ ] ¿Los archivos subidos tienen validación de tamaño y tipo MIME?
- [ ] ¿Las notificaciones y emails se despachan en cola?
- [ ] ¿Los datos de alta frecuencia (settings, semestre activo) están cacheados?
- [ ] ¿Los índices necesarios están en la migración?
- [ ] ¿Se protege contra IDOR verificando ownership del recurso?
