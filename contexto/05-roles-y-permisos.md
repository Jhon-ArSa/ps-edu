# 05 — Roles y Permisos

## 1. Roles del Sistema

El sistema tiene **3 roles** definidos en el campo `role` de la tabla `users`:

| Rol | Identificador | Ruta base | Descripción |
|---|---|---|---|
| Administrador | `admin` | `/admin` | Gestión completa del sistema |
| Docente | `docente` | `/docente` | Gestión del aula virtual y evaluación |
| Alumno | `alumno` | `/alumno` | Acceso al contenido de sus cursos |

---

## 2. Mecanismo de Autorización

### Capa 1 — Middleware de Rol (`RoleMiddleware`)

Protege grupos de rutas completos. Si el rol no coincide, retorna HTTP 403.

```php
// Registro en bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => RoleMiddleware::class]);
})

// Uso en rutas
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...)
Route::middleware(['auth', 'role:docente'])->prefix('docente')->group(...)
Route::middleware(['auth', 'role:alumno'])->prefix('alumno')->group(...)
```

### Capa 2 — Policies (Autorización por Recurso)

Protege operaciones sobre instancias específicas. Registradas en `AppServiceProvider`.

```php
// CoursePolicy: ¿puede este usuario operar sobre este curso?
Gate::policy(Course::class, CoursePolicy::class);
```

**Métodos de la CoursePolicy:**

| Método | Admin | Docente (propio) | Docente (ajeno) | Alumno (matriculado) |
|---|:---:|:---:|:---:|:---:|
| `manage` | ✅ | ✅ | ❌ | ❌ |
| `view` | ✅ | ✅ | ❌ | ✅ |

---

## 3. Matriz de Permisos por Módulo

### Usuarios

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Ver lista de usuarios | ✅ | ❌ | ❌ |
| Crear usuario | ✅ | ❌ | ❌ |
| Editar usuario | ✅ | ❌ | ❌ |
| Activar / desactivar usuario | ✅ | ❌ | ❌ |
| Eliminar usuario | ✅ | ❌ | ❌ |
| Ver/editar su propio perfil | ✅ | ✅ | ✅ |

### Semestres

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Crear / editar semestre | ✅ | ❌ | ❌ |
| Activar semestre | ✅ | ❌ | ❌ |
| Cerrar semestre | ✅ | ❌ | ❌ |
| Ver semestres (lectura) | ✅ | ❌ | ❌ |

### Cursos

| Acción | Admin | Docente (propio) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Crear curso | ✅ | ❌ | ❌ |
| Editar curso | ✅ | ❌ | ❌ |
| Eliminar curso | ✅ | ❌ | ❌ |
| Ver lista de cursos propios | ✅ | ✅ | — |
| Ver detalle del curso | ✅ | ✅ | ✅ |

### Matrículas

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Matricular alumno en curso | ✅ | ✅ (en su curso) | ❌ |
| Retirar alumno de curso | ✅ | ✅ (en su curso) | ❌ |
| Ver lista de matrículas | ✅ | ✅ (en sus cursos) | ❌ |
| Ver sus propios cursos matriculados | — | — | ✅ |

### Semanas

| Acción | Admin | Docente (en su curso) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Crear semana | ❌ | ✅ | ❌ |
| Editar semana | ❌ | ✅ | ❌ |
| Eliminar semana | ❌ | ✅ | ❌ |
| Ver semanas del curso | ✅ | ✅ | ✅ |

### Materiales

| Acción | Admin | Docente (en su curso) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Subir / crear material | ❌ | ✅ | ❌ |
| Editar material | ❌ | ✅ | ❌ |
| Reordenar materiales | ❌ | ✅ | ❌ |
| Eliminar material | ❌ | ✅ | ❌ |
| Ver y descargar material | ✅ | ✅ | ✅ |

### Tareas

| Acción | Admin | Docente (en su curso) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Crear tarea | ❌ | ✅ | ❌ |
| Editar tarea | ❌ | ✅ | ❌ |
| Eliminar tarea | ❌ | ✅ | ❌ |
| Ver entregas de todos los alumnos | ❌ | ✅ | ❌ |
| Calificar entrega | ❌ | ✅ | ❌ |
| Ver tarea (descripción) | ✅ | ✅ | ✅ |
| Entregar tarea (subir archivo) | ❌ | ❌ | ✅ |
| Ver mi propia entrega y nota | ❌ | ❌ | ✅ |

### Evaluaciones

| Acción | Admin | Docente (en su curso) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Crear / editar evaluación | ❌ | ✅ | ❌ |
| Crear preguntas | ❌ | ✅ | ❌ |
| Ver resultados de todos | ❌ | ✅ | ❌ |
| Rendir evaluación | ❌ | ❌ | ✅ |
| Ver su propio resultado | ❌ | ❌ | ✅ |

### Calificaciones

| Acción | Admin | Docente (en su curso) | Alumno |
|---|:---:|:---:|:---:|
| Ver libreta de notas del curso | ✅ | ✅ | ❌ |
| Ingresar nota manual | ❌ | ✅ | ❌ |
| Exportar libreta | ✅ | ✅ | ❌ |
| Ver sus propias notas | ❌ | ❌ | ✅ |

### Anuncios

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Crear anuncio institucional | ✅ | ❌ | ❌ |
| Editar / eliminar anuncio | ✅ | ❌ | ❌ |
| Ver anuncios relevantes | ✅ | ✅ | ✅ |

### Foro

| Acción | Admin | Docente (en su curso) | Alumno (matriculado) |
|---|:---:|:---:|:---:|
| Crear tema | ❌ | ✅ | ✅ |
| Responder tema | ❌ | ✅ | ✅ |
| Fijar / cerrar tema | ❌ | ✅ | ❌ |
| Eliminar respuesta propia | ❌ | ✅ | ✅ |
| Eliminar cualquier respuesta | ❌ | ✅ (moderador) | ❌ |
| Supervisar todos los foros | ✅ | ❌ | ❌ |

### Notificaciones

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Ver sus propias notificaciones | ✅ | ✅ | ✅ |
| Marcar como leída | ✅ | ✅ | ✅ |
| Recibir notificaciones del sistema | ✅ | ✅ | ✅ |

### Reportes

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Ver reportes globales del semestre | ✅ | ❌ | ❌ |
| Ver reporte de su propio curso | ❌ | ✅ | ❌ |
| Exportar reportes | ✅ | ✅ (solo su curso) | ❌ |

### Configuración

| Acción | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Ver / editar configuración global | ✅ | ❌ | ❌ |

---

## 4. Helpers de Rol en el Modelo User

```php
// Uso en Blade y controladores
$user->isAdmin()    // role === 'admin'
$user->isDocente()  // role === 'docente'
$user->isAlumno()   // role === 'alumno'

// Uso en Blade
@if(auth()->user()->isDocente())
    <a href="{{ route('docente.escalafon') }}">Mi escalafón</a>
@endif
```

---

## 5. Redirección por Rol

Al ingresar al sistema, cada usuario es redirigido automáticamente a su dashboard:

```
admin   → /admin/dashboard
docente → /docente/dashboard
alumno  → /alumno/dashboard
```

Configurado en `bootstrap/app.php` via la closure `redirectUsersTo` del middleware `guest`.

---

## 6. Consideraciones de Seguridad

- Las rutas no solo validan el rol del grupo; cada controlador verifica que el recurso pertenezca al usuario autenticado (evita IDOR — Insecure Direct Object Reference).
- El docente no puede acceder a cursos de otro docente aunque conozca el ID.
- El alumno no puede ver tareas ni materiales de cursos donde no está matriculado.
- La verificación se hace con **Policies**, no con `if user_id === $model->user_id` en el controlador.
- Los tokens CSRF están activos en todos los formularios POST/PUT/DELETE.
- Los archivos subidos se guardan con nombres generados por el sistema (`Storage::put` sin nombre original de usuario) para evitar path traversal.
