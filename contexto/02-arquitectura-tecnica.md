# 02 — Arquitectura Técnica

## 1. Stack Tecnológico

| Capa | Tecnología | Versión | Justificación |
|---|---|---|---|
| Framework backend | Laravel | 12.x | Ecosistema maduro, ORM potente, middleware, políticas de autorización |
| Lenguaje | PHP | 8.2+ | Requerido por Laravel 12 |
| Base de datos | MySQL | 8.0 | Producción en AWS RDS; confiabilidad y soporte a largo plazo |
| CSS Framework | Tailwind CSS | 4.x | Utilitario, sin CSS muerto en producción, JIT por defecto |
| JavaScript | Alpine.js | 3.x | Reactividad ligera sin SPA; ideal para Blade |
| Build tool | Vite | 7.x | HMR rápido, integración nativa con Laravel |
| HTTP client | Axios | 1.x | Peticiones AJAX para endpoints JSON internos |
| Testing DB | SQLite | in-memory | Tests rápidos sin dependencia de MySQL |
| Colas | Database driver | — | Suficiente para emails y notificaciones en esta escala |
| Cache | Database / File | — | Se puede migrar a Redis si la carga lo requiere |
| Sesiones | Database | — | Persistencia entre reinicios del servidor |

---

## 2. Patrón de Arquitectura

El sistema sigue el patrón **MVC (Model-View-Controller)** de Laravel con algunas extensiones para mantener el código organizado a medida que el sistema crece:

```
Request → Middleware (auth, role) → Controller → Model/Action → View (Blade)
```

### Principios de diseño aplicados

**Controllers delgados**: La lógica de negocio no vive en los controladores. Los controladores orquestan; los modelos y clases de acción operan.

**Modelos ricos**: Los modelos tienen accessors, scopes y relaciones declaradas. Lógica de presentación simple (badges, URLs) vive en el modelo.

**Policies para autorización**: Las reglas de acceso a recursos están en `Policies`, no dispersas en controladores con `if` anidados.

**Componentes Blade**: La UI se construye con componentes reutilizables (`x-sidebar-link`, `x-alert`, etc.) para evitar duplicación de HTML.

**Sin SPA**: No hay Vue, React ni Inertia. La aplicación es completamente server-rendered con Blade. Interactividad ligera con Alpine.js.

---

## 3. Estructura de Capas

```
┌─────────────────────────────────────────────┐
│              CAPA DE PRESENTACIÓN            │
│   Blade Views + Tailwind CSS + Alpine.js     │
│   Layouts: app.blade.php, auth.blade.php     │
└────────────────────┬────────────────────────┘
                     │ HTTP Request / Response
┌────────────────────▼────────────────────────┐
│              CAPA DE APLICACIÓN              │
│   Middleware → Controllers → Policies        │
│   Namespaces: Admin/, Docente/, Alumno/      │
└────────────────────┬────────────────────────┘
                     │ Eloquent ORM
┌────────────────────▼────────────────────────┐
│              CAPA DE DOMINIO                 │
│   Models (User, Course, Week, Task, etc.)    │
│   Scopes, Accessors, Relationships           │
└────────────────────┬────────────────────────┘
                     │ PDO / Query Builder
┌────────────────────▼────────────────────────┐
│              CAPA DE DATOS                   │
│   MySQL 8 (AWS RDS) + File Storage (local)   │
└─────────────────────────────────────────────┘
```

---

## 4. Autenticación y Autorización

### Autenticación
- Sistema nativo de Laravel (`Auth` facade + sesiones)
- Sin Breeze, Jetstream ni Sanctum — login personalizado
- Verificación de `status` (cuenta activa/bloqueada) antes de permitir acceso
- `remember_me` con cookie de larga duración
- Recuperación de contraseña via `Password` broker con expiración de 60 minutos

### Autorización — Dos capas
1. **RoleMiddleware** (`role:admin|docente|alumno`): acceso por grupo al área
2. **Policies** (ej. `CoursePolicy`): acceso granular a recursos específicos

```php
// Capa 1: grupo de rutas
Route::middleware(['auth', 'role:docente'])->prefix('docente')->group(...)

// Capa 2: recurso individual
$this->authorize('manage', $course); // CoursePolicy::manage()
```

---

## 5. Flujo de una Petición Típica

**Caso: Docente accede a su curso**

```
1. GET /docente/cursos/5
2. Middleware: auth → ✅ sesión válida
3. Middleware: role:docente → ✅ rol correcto
4. Docente\CourseController::show($id=5)
5. Course::with(['weeks.materials', 'weeks.tasks'])->findOrFail(5)
6. $this->authorize('manage', $course) → CoursePolicy::manage()
7. return view('docente.cursos.show', compact('course'))
8. Blade renderiza con layout app.blade.php
9. Respuesta HTML al navegador
```

---

## 6. Decisiones de Arquitectura Clave

### ¿Por qué no SPA (Vue/React/Inertia)?
El perfil de usuario (docentes y alumnos de posgrado) no requiere UX tipo aplicación de escritorio. El costo de complejidad de una SPA no se justifica. Blade + Alpine.js cubre el 95% de los casos de interactividad (modales, menús desplegables, alertas dinámicas).

### ¿Por qué Alpine.js y no jQuery?
Alpine.js es declarativo, ligero (15kb), se integra naturalmente con Blade y no introduce deuda técnica. jQuery sería excesivo para los casos de uso de este sistema.

### ¿Por qué Tailwind v4 y no Bootstrap?
Tailwind v4 con JIT genera solo el CSS necesario (< 20kb en producción). Bootstrap incluye ~150kb de CSS y JS que mayoritariamente no se usan. Tailwind también facilita el theming institucional con variables CSS nativas.

### ¿Por qué colas con database driver y no Redis?
Para 200–300 usuarios y operaciones como envío de emails y notificaciones, el driver de base de datos es suficiente. Si la carga crece o se requiere procesamiento en tiempo real, se puede migrar a Redis sin cambios de código.

### ¿Por qué almacenamiento local y no S3?
Para la primera versión del sistema, el almacenamiento local (`public` disk) simplifica el deploy y reduce costos. La abstracción de Laravel (`Storage` facade) permite migrar a S3 en el futuro sin cambios en la lógica de negocio.

---

## 7. Configuración de Entornos

| Parámetro | Development | Production |
|---|---|---|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `DB_CONNECTION` | `mysql` (AWS RDS) | `mysql` (AWS RDS) |
| `CACHE_STORE` | `database` | `redis` (recomendado) |
| `SESSION_DRIVER` | `database` | `database` |
| `QUEUE_CONNECTION` | `database` | `database` |
| `MAIL_MAILER` | `log` | `smtp` (configurar) |
| `BCRYPT_ROUNDS` | `12` | `12` |

---

## 8. Convenciones de Código

- **Idioma del código**: Inglés (nombres de variables, métodos, clases)
- **Idioma de la UI**: Español (mensajes, labels, rutas, comentarios)
- **PSR-12**: Estilo de código con Laravel Pint (`./vendor/bin/pint`)
- **Nombres de controladores**: Singular, sufijo `Controller` (ej. `CourseController`)
- **Nombres de vistas**: Snake_case, organizadas por rol/módulo (ej. `docente/cursos/show.blade.php`)
- **Nombres de rutas**: Kebab-case en español, con prefijo de rol (ej. `docente.cursos.index`)
- **Commits**: Mensajes en español, convencional (feat, fix, chore, refactor)

---

## 9. Testing

Estado actual: solo tests de ejemplo (placeholder). Estrategia recomendada:

| Tipo | Herramienta | Prioridad |
|---|---|---|
| Tests de feature (HTTP) | PHPUnit + `RefreshDatabase` | Alta |
| Tests unitarios (modelos) | PHPUnit | Media |
| Tests de browser (E2E) | Laravel Dusk | Baja (fase posterior) |

**Base de datos para tests**: SQLite in-memory (`phpunit.xml` ya configurado). Los tests deben ser independientes y usar `RefreshDatabase` o `DatabaseTransactions`.
