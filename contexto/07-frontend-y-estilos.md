# 07 — Frontend y Estilos

## 1. Filosofía Frontend

El sistema utiliza una arquitectura **server-rendered con mejoras progresivas**:

- **Blade** renderiza HTML completo en el servidor.
- **Tailwind CSS v4** maneja todo el estilo — sin CSS personalizado por componente.
- **Alpine.js v3** agrega interactividad declarativa donde se necesita (modales, menús, alerts).
- **Axios** maneja las pocas peticiones AJAX (búsquedas, toggles, reordenamientos).
- **Sin frameworks SPA** — no hay Vue, React ni Inertia. No se necesitan.

> **Regla de oro:** Si se puede hacer en Blade + Alpine, no se escribe JavaScript adicional.

---

## 2. Sistema de Diseño

### Paleta de Colores

Definida en `resources/css/app.css` como variables CSS nativas via Tailwind v4 `@theme`:

```css
@theme {
  /* Color primario institucional — azul */
  --color-primary-50:  #eff6ff;
  --color-primary-100: #dbeafe;
  --color-primary-200: #bfdbfe;
  --color-primary-300: #93c5fd;
  --color-primary-400: #60a5fa;
  --color-primary-500: #3b82f6;
  --color-primary-600: #2563eb;  /* ← uso principal */
  --color-primary-700: #1d4ed8;
  --color-primary-800: #1e40af;
  --color-primary-900: #1e3a8a;
  --color-primary-950: #172554;

  /* Color acento — ámbar/dorado FAEDU */
  --color-accent-400: #fbbf24;
  --color-accent-500: #f59e0b;  /* ← uso principal */
  --color-accent-600: #d97706;

  /* Tipografía */
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
}
```

**Uso en vistas:** `bg-primary-600`, `text-primary-700`, `border-accent-500`, etc.

### Tipografía

| Nivel | Clase Tailwind | Uso |
|---|---|---|
| H1 — Título de página | `text-2xl font-bold text-gray-900` | Encabezado de cada sección |
| H2 — Subtítulo | `text-lg font-semibold text-gray-800` | Encabezados de tarjetas |
| H3 — Label de sección | `text-sm font-medium text-gray-700` | Labels, subtítulos menores |
| Body | `text-sm text-gray-600` | Texto de contenido general |
| Caption | `text-xs text-gray-500` | Metadatos, fechas, tags |
| Link | `text-primary-600 hover:text-primary-800` | — |

### Espaciado
- Padding interno de cards: `p-6`
- Gap entre elementos de lista: `space-y-4`
- Margin entre secciones: `mb-8`
- Padding de página: `px-4 sm:px-6 lg:px-8 py-6`

---

## 3. Organización del CSS (`app.css`)

**Todo el CSS está en un solo archivo.** No se crean archivos CSS adicionales.

```css
/* resources/css/app.css — estructura */

@import 'tailwindcss';

/* 1. Theme tokens (variables CSS del sistema de diseño) */
@theme {
  --font-sans: ...;
  --color-primary-*: ...;
  --color-accent-*: ...;
}

/* 2. Utilidades Alpine.js */
[x-cloak] { display: none !important; }

/* 3. Componentes base (solo lo que Tailwind no puede expresar inline) */
@layer components {
  /* Botones */
  .btn-primary { @apply inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors; }
  .btn-secondary { @apply inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors; }
  .btn-danger { @apply inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors; }
  .btn-ghost { @apply inline-flex items-center px-3 py-1.5 text-gray-600 text-sm rounded-lg hover:bg-gray-100 transition-colors; }

  /* Campos de formulario */
  .form-input { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .form-select { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500; }
  .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
  .form-error { @apply text-xs text-red-600 mt-1; }

  /* Cards */
  .card { @apply bg-white rounded-xl shadow-sm border border-gray-200; }
  .card-header { @apply px-6 py-4 border-b border-gray-200; }
  .card-body { @apply p-6; }

  /* Badges de estado */
  .badge { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium; }
  .badge-green { @apply badge bg-green-100 text-green-800; }
  .badge-yellow { @apply badge bg-yellow-100 text-yellow-800; }
  .badge-red { @apply badge bg-red-100 text-red-800; }
  .badge-gray { @apply badge bg-gray-100 text-gray-700; }
  .badge-blue { @apply badge bg-blue-100 text-blue-800; }
}

/* 4. Estilos de impresión (para reportes) */
@media print {
  .no-print { display: none !important; }
  body { font-size: 12px; }
}
```

### Reglas para agregar CSS
1. **Primero intenta con clases Tailwind directamente en el HTML.**
2. Si el mismo conjunto de clases se repite 3+ veces con el mismo significado semántico → extraer a `@layer components`.
3. **Nunca** crear un archivo `.css` adicional.
4. **Nunca** usar `!important` salvo en `[x-cloak]`.
5. No usar colores en hex directamente en Blade — siempre via variables del tema.

---

## 4. Organización del JavaScript (`app.js`)

**Todo el JS de la app vive en `app.js`.** No se crean archivos JS por página o módulo.

```javascript
// resources/js/app.js

import './bootstrap';   // Axios + CSRF token
import Alpine from 'alpinejs';

// Registrar componentes Alpine globales aquí
// (si crecen mucho, extraer a ./alpine/nombreComponente.js y importar)

window.Alpine = Alpine;
Alpine.start();
```

```javascript
// resources/js/bootstrap.js
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token automático en todas las peticiones AJAX
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
```

### Patrones Alpine.js en uso

**Sidebar móvil:**
```html
<div x-data="{ sidebarOpen: false }">
  <button @click="sidebarOpen = true">Menú</button>
  <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">...</nav>
  <div x-show="sidebarOpen" @click="sidebarOpen = false" class="overlay"></div>
</div>
```

**Flash messages con auto-dismiss:**
```html
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0">
  {{ session('success') }}
</div>
```

**Modal de confirmación de borrado:**
```html
<div x-data="{ open: false, url: '' }">
  <button @click="open = true; url = '/admin/users/5'" type="button">Eliminar</button>
  <div x-show="open" x-cloak class="modal-overlay">
    <form :action="url" method="POST">
      @csrf @method('DELETE')
      <button type="submit">Confirmar</button>
      <button @click="open = false" type="button">Cancelar</button>
    </form>
  </div>
</div>
```

**Toggle de estado AJAX:**
```html
<button @click="
  axios.patch('/admin/users/' + userId + '/toggle-status')
    .then(r => { isActive = r.data.status })
" :class="isActive ? 'bg-green-500' : 'bg-gray-300'">
</button>
```

---

## 5. Componentes Blade

Los componentes Blade evitan la duplicación de HTML complejo. Están en `resources/views/components/`.

### Componentes existentes
- `<x-sidebar-link route="docente.dashboard" icon="home">Dashboard</x-sidebar-link>`

### Componentes pendientes a crear

**`<x-alert type="success|error|warning|info">`**
```blade
{{-- Uso --}}
<x-alert type="success">{{ session('success') }}</x-alert>
```

**`<x-badge color="green|yellow|red|gray|blue">`**
```blade
{{-- Uso --}}
<x-badge color="green">Activo</x-badge>
```

**`<x-empty-state icon="..." message="...">`**
```blade
{{-- Uso --}}
<x-empty-state icon="book" message="No hay cursos registrados aún." />
```

**`<x-modal id="..." title="...">`**
```blade
{{-- Uso --}}
<x-modal id="confirm-delete" title="¿Eliminar curso?">
  ...contenido del modal...
</x-modal>
```

**`<x-stat-card label="..." value="..." icon="..." color="...">`**
```blade
{{-- Uso en dashboards --}}
<x-stat-card label="Alumnos activos" value="245" icon="users" color="blue" />
```

---

## 6. Layouts

### `layouts/app.blade.php` — Layout principal autenticado

Estructura:
```
┌──────────────────────────────────────────────────────┐
│  SIDEBAR (fijo, 256px)   │  CONTENIDO PRINCIPAL       │
│  ── Logo institucional   │  ── Top bar (breadcrumb)   │
│  ── Navegación por rol   │  ── Flash messages         │
│  ── ...                  │  ── @yield('content')       │
│  ── Avatar + dropdown    │                             │
│  (overlay en mobile)     │  (scroll independiente)     │
└──────────────────────────────────────────────────────┘
```

Secciones Blade disponibles:
- `@yield('title')` — título de la pestaña del navegador
- `@yield('breadcrumb')` — migas de pan en el top bar
- `@yield('content')` — contenido de la página
- `@yield('scripts')` — scripts específicos de la página (al final del body)

### `layouts/auth.blade.php` — Layout de autenticación

Split-panel: izquierda decorativa (marca institucional) + derecha con el formulario. Responsivo: en mobile solo se muestra el panel del formulario.

---

## 7. Responsividad

El sistema usa **mobile-first** con los breakpoints de Tailwind:

| Breakpoint | Ancho | Comportamiento |
|---|---|---|
| `sm` | 640px | Formularios en una columna → dos columnas |
| `md` | 768px | Sidebar oculto → visible fijo |
| `lg` | 1024px | Tablas con columnas adicionales visibles |
| `xl` | 1280px | Dashboards con más cards por fila |

Reglas:
- El sidebar es overlay en mobile (< md) y fijo en desktop (≥ md).
- Las tablas tienen scroll horizontal en mobile (`overflow-x-auto`).
- Los grids de estadísticas son 1 col en mobile, 2 en sm, 4 en lg.

---

## 8. Optimización del Bundle

**Tailwind v4 JIT** — Solo genera CSS para las clases realmente usadas en los archivos Blade/JS. El CSS de producción no supera 25–40kb (gzipped ~8kb).

**Vite** en producción:
- `npm run build` genera bundles con hash para cache-busting.
- JS split automático por chunks.
- Assets versionados en `public/build/`.

**No incluir en el bundle:**
- Librerías de terceros grandes (usar CDN con fallback o solo si son esenciales).
- Imágenes en JS (referencias por ruta en Blade).

**Carga diferida de scripts:**
```html
{{-- Solo en vistas que lo necesiten, via @section('scripts') --}}
@section('scripts')
<script>
// Script específico de esta página únicamente
</script>
@endsection
```
