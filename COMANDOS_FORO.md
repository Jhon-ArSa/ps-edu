# 🛠️ COMANDOS ÚTILES - SISTEMA DE FOROS

## 🚀 Despliegue Inicial

```bash
# 1. Ejecutar migraciones (si no están ejecutadas)
php artisan migrate

# 2. Verificar estado de migraciones
php artisan migrate:status | findstr forum

# 3. Limpiar cache de rutas y config
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Iniciar servidor
php artisan serve
```

---

## 📊 Consultas Útiles en Base de Datos

### Ver todos los temas del foro

```sql
SELECT 
    ft.id,
    ft.title,
    c.name as course,
    u.name as author,
    ft.replies_count,
    ft.is_pinned,
    ft.is_closed,
    ft.created_at
FROM forum_topics ft
JOIN courses c ON c.id = ft.course_id
JOIN users u ON u.id = ft.user_id
ORDER BY ft.created_at DESC;
```

### Ver temas con más respuestas

```sql
SELECT 
    ft.title,
    c.name as course,
    ft.replies_count
FROM forum_topics ft
JOIN courses c ON c.id = ft.course_id
ORDER BY ft.replies_count DESC
LIMIT 10;
```

### Ver respuestas de un tema

```sql
SELECT 
    fr.id,
    fr.body,
    u.name as author,
    u.role,
    fr.deleted_at,
    fr.created_at
FROM forum_replies fr
JOIN users u ON u.id = fr.user_id
WHERE fr.topic_id = 1 -- Cambiar ID
ORDER BY fr.created_at ASC;
```

### Verificar integridad de contadores

```sql
SELECT 
    ft.id,
    ft.title,
    ft.replies_count as stored_count,
    COUNT(fr.id) as actual_count,
    CASE 
        WHEN ft.replies_count = COUNT(fr.id) THEN '✅ OK'
        ELSE '❌ DESINCRONIZADO'
    END as status
FROM forum_topics ft
LEFT JOIN forum_replies fr ON fr.topic_id = ft.id AND fr.deleted_at IS NULL
GROUP BY ft.id, ft.title, ft.replies_count;
```

### Ver notificaciones de foro

```sql
SELECT 
    n.id,
    n.notifiable_id,
    u.name as user,
    JSON_EXTRACT(n.data, '$.title') as notification_title,
    JSON_EXTRACT(n.data, '$.body') as notification_body,
    n.read_at,
    n.created_at
FROM notifications n
JOIN users u ON u.id = n.notifiable_id
WHERE JSON_EXTRACT(n.data, '$.type') = 'forum_topic_created'
ORDER BY n.created_at DESC;
```

---

## 🔧 Mantenimiento

### Recalcular contadores (si están desincronizados)

```php
// Ejecutar en tinker: php artisan tinker

use App\Models\ForumTopic;
use App\Models\ForumReply;

ForumTopic::all()->each(function($topic) {
    $count = ForumReply::where('topic_id', $topic->id)
                       ->whereNull('deleted_at')
                       ->count();
    $topic->update(['replies_count' => $count]);
});

echo "✅ Contadores actualizados";
```

### Actualizar last_reply_at

```php
// php artisan tinker

use App\Models\ForumTopic;
use App\Models\ForumReply;

ForumTopic::all()->each(function($topic) {
    $lastReply = ForumReply::where('topic_id', $topic->id)
                           ->whereNull('deleted_at')
                           ->latest()
                           ->first();
    
    $topic->update([
        'last_reply_at' => $lastReply ? $lastReply->created_at : null
    ]);
});

echo "✅ last_reply_at actualizado";
```

### Limpiar soft deletes antiguos (opcional)

```sql
-- Eliminar permanentemente respuestas borradas hace más de 90 días
DELETE FROM forum_replies 
WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

## 🧪 Seeders de Prueba

### Crear seeder para foros

```bash
php artisan make:seeder ForumSeeder
```

```php
// database/seeders/ForumSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\Course;
use App\Models\User;

class ForumSeeder extends Seeder
{
    public function run()
    {
        $course = Course::first();
        $teacher = User::where('role', 'docente')->first();
        $students = User::where('role', 'alumno')->take(5)->get();

        // Crear 5 temas
        for ($i = 1; $i <= 5; $i++) {
            $topic = ForumTopic::create([
                'course_id' => $course->id,
                'user_id' => $i % 2 == 0 ? $teacher->id : $students->random()->id,
                'title' => "Tema de ejemplo #$i",
                'body' => "Este es el contenido del tema $i. Lorem ipsum dolor sit amet...",
                'is_pinned' => $i == 1,
                'is_closed' => $i == 5,
            ]);

            // Crear 3-8 respuestas por tema
            $replyCount = rand(3, 8);
            for ($j = 1; $j <= $replyCount; $j++) {
                ForumReply::create([
                    'topic_id' => $topic->id,
                    'user_id' => $students->random()->id,
                    'body' => "Respuesta #$j al tema $i. Gracias por compartir!",
                ]);
            }

            // Actualizar contadores
            $topic->update([
                'replies_count' => $replyCount,
                'last_reply_at' => now(),
            ]);
        }

        $this->command->info('✅ Foros de prueba creados');
    }
}
```

**Ejecutar:**
```bash
php artisan db:seed --class=ForumSeeder
```

---

## 🐛 Debugging

### Habilitar query log

```php
// En cualquier controlador

\DB::enableQueryLog();

// ... tu código ...

dd(\DB::getQueryLog());
```

### Ver rutas relacionadas con foros

```bash
php artisan route:list --name=forum
```

### Ver eventos disparados

```bash
php artisan event:list | findstr Forum
```

### Verificar permisos de storage

```bash
# Windows CMD
icacls storage /grant Everyone:(OI)(CI)F /T

# Windows PowerShell
Get-Acl storage | Set-Acl storage -Recurse
```

---

## 📈 Optimización

### Índices sugeridos (ya están en migración)

```sql
-- Verificar índices
SHOW INDEX FROM forum_topics;
SHOW INDEX FROM forum_replies;

-- Si falta alguno:
CREATE INDEX idx_course_pinned ON forum_topics(course_id, is_pinned, created_at);
CREATE INDEX idx_course_last_reply ON forum_topics(course_id, last_reply_at);
CREATE INDEX idx_topic_created ON forum_replies(topic_id, created_at);
```

### Cache de contadores (opcional)

```php
// config/cache.php - agregar
'forum_stats' => [
    'driver' => 'file',
    'ttl' => 3600, // 1 hora
],
```

```php
// En el controlador
$topicsCount = Cache::remember('course_' . $course->id . '_topics_count', 3600, function() use ($course) {
    return $course->forumTopics()->count();
});
```

---

## 🔐 Seguridad

### Verificar rate limiting

```bash
# Ver logs de throttling
findstr /C:"429" storage\logs\laravel.log
```

### Auditar permisos

```php
// php artisan tinker

use App\Models\ForumTopic;
use App\Models\User;

$topic = ForumTopic::first();
$student = User::where('role', 'alumno')->first();
$teacher = User::where('role', 'docente')->first();

// Probar permisos
$topic->canReply($student);  // true si matriculado
$topic->canReply($teacher);  // true si es su curso
$topic->canDelete($student); // false si no es su tema
```

---

## 📦 Backup

### Exportar temas y respuestas

```sql
-- Backup de forum_topics
SELECT * INTO OUTFILE 'C:/backup/forum_topics.csv'
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM forum_topics;

-- Backup de forum_replies
SELECT * INTO OUTFILE 'C:/backup/forum_replies.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM forum_replies;
```

### Restaurar desde backup

```sql
LOAD DATA INFILE 'C:/backup/forum_topics.csv'
INTO TABLE forum_topics
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

---

## 🎯 Comandos Rápidos del Día a Día

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar todo el cache
php artisan optimize:clear

# Ver información del sistema
php artisan about

# Verificar conexión DB
php artisan db:show

# Listar tablas
php artisan db:table forum_topics
php artisan db:table forum_replies

# Ejecutar tinker
php artisan tinker
```

---

## 📊 Reportes y Estadísticas

### Temas más activos por curso

```sql
SELECT 
    c.name as course,
    COUNT(ft.id) as total_topics,
    SUM(ft.replies_count) as total_replies,
    AVG(ft.replies_count) as avg_replies_per_topic
FROM courses c
LEFT JOIN forum_topics ft ON ft.course_id = c.id
GROUP BY c.id, c.name
ORDER BY total_replies DESC;
```

### Usuarios más activos en foros

```sql
SELECT 
    u.name,
    u.role,
    COUNT(DISTINCT ft.id) as topics_created,
    COUNT(DISTINCT fr.id) as replies_posted,
    COUNT(DISTINCT ft.id) + COUNT(DISTINCT fr.id) as total_activity
FROM users u
LEFT JOIN forum_topics ft ON ft.user_id = u.id
LEFT JOIN forum_replies fr ON fr.user_id = u.id
GROUP BY u.id, u.name, u.role
ORDER BY total_activity DESC
LIMIT 20;
```

### Actividad de foro por mes

```sql
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(id) as topics_created
FROM forum_topics
GROUP BY month
ORDER BY month DESC;
```

---

**¡Sistema de foros completamente operativo! 🎉**
