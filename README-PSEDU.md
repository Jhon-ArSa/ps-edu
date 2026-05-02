# 🎓 PS-EDU — Sistema de Gestión Académica FAEDU

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8">
</p>

## 📖 Descripción

**PS-EDU** es la plataforma de gestión académica y aula virtual de la **Facultad de Educación (FAEDU)** del posgrado de ADESA. Sistema web diseñado para centralizar la gestión académica, facilitar la enseñanza virtual y mejorar la comunicación entre docentes, estudiantes y administración.

### 🎯 Características Principales

- 👥 **Gestión de Usuarios** — Administración completa de docentes y estudiantes
- 📚 **Aula Virtual** — Organización por semanas con materiales, tareas y evaluaciones
- 📝 **Evaluaciones en Línea** — Exámenes con múltiples tipos de preguntas y cronómetro
- 📊 **Calificaciones** — Libreta de notas con cálculo automático de promedios
- 💬 **Foro de Discusión** — Comunicación asíncrona por curso
- 📢 **Anuncios** — Sistema de comunicados con targeting avanzado
- 🔔 **Notificaciones** — Alertas en tiempo real de eventos importantes
- 📈 **Reportes** — Estadísticas y análisis de actividad académica

---

## 🚀 Inicio Rápido

### Requisitos Previos

- PHP 8.2 o superior
- Composer 2.x
- Node.js 18.x o superior
- MySQL 8.0 o superior
- Redis (recomendado para producción)

### Instalación Local

```bash
# 1. Clonar repositorio
git clone https://github.com/tu-org/psedu-plataforma.git
cd psedu-plataforma

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_DATABASE=psedu
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones
php artisan migrate

# 6. Crear symlink de storage
php artisan storage:link

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
```

Acceder a: `http://localhost:8000`

---

## 📁 Estructura del Proyecto

```
psedu-plataforma/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controladores del administrador
│   │   │   ├── Docente/        # Controladores del docente
│   │   │   └── Alumno/         # Controladores del estudiante
│   │   ├── Middleware/
│   │   └── Policies/           # Políticas de autorización
│   ├── Models/                 # Modelos Eloquent
│   └── Notifications/          # Notificaciones del sistema
├── database/
│   ├── factories/              # Factories para testing
│   ├── migrations/             # Migraciones de BD
│   └── seeders/                # Seeders
├── resources/
│   ├── css/
│   │   └── app.css            # Tailwind CSS + estilos personalizados
│   ├── js/
│   │   └── app.js             # Alpine.js + lógica frontend
│   └── views/
│       ├── admin/             # Vistas del administrador
│       ├── docente/           # Vistas del docente
│       ├── alumno/            # Vistas del estudiante
│       └── layouts/           # Layouts compartidos
├── routes/
│   └── web.php                # Rutas de la aplicación
├── tests/
│   ├── Feature/               # Tests de integración
│   └── Unit/                  # Tests unitarios
├── contexto/                  # 📚 Documentación técnica completa
│   ├── 01-vision-y-alcance.md
│   ├── 02-arquitectura-tecnica.md
│   ├── 03-modulos-del-sistema.md
│   ├── 04-base-de-datos.md
│   ├── 05-roles-y-permisos.md
│   ├── 06-estructura-del-proyecto.md
│   ├── 07-frontend-y-estilos.md
│   ├── 08-rendimiento-y-escalabilidad.md
│   └── 09-requerimientos.md
├── deploy.sh                  # Script de deployment
├── supervisor.conf            # Configuración de Supervisor
├── crontab.txt               # Configuración de Cron
├── TESTING.md                # Guía de testing
└── DEPLOYMENT.md             # Guía de deployment
```

---

## 👥 Roles del Sistema

### 🔴 Administrador
- Gestión completa de usuarios, cursos y semestres
- Supervisión de actividad académica
- Generación de reportes institucionales
- Configuración del sistema

### 🔵 Docente
- Gestión de aula virtual (materiales, tareas, evaluaciones)
- Calificación de entregas y evaluaciones
- Comunicación con estudiantes (anuncios, foro)
- Reportes de su curso

### 🟢 Alumno
- Acceso a materiales de sus cursos
- Entrega de tareas
- Realización de evaluaciones en línea
- Consulta de calificaciones
- Participación en foros

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Con cobertura
php artisan test --coverage

# Tests específicos
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Ver guía completa
cat TESTING.md
```

### Cobertura Actual

- ✅ Autenticación: 80%
- ✅ Políticas: 90%
- ✅ Matrículas: 70%
- ✅ Entregas: 75%
- **Total: ~60%**

---

## 🚀 Deployment

### Producción

```bash
# Usar script automatizado
./deploy.sh

# O seguir guía manual
cat DEPLOYMENT.md
```

### Servicios Requeridos

- **Nginx/Apache** — Servidor web
- **Supervisor** — Workers de colas
- **Cron** — Laravel Scheduler
- **Redis** — Cache y sesiones (recomendado)

---

## 📚 Documentación

### Documentación Técnica Completa

Toda la documentación del sistema está en `/contexto/`:

1. **[Visión y Alcance](contexto/01-vision-y-alcance.md)** — Objetivos y contexto institucional
2. **[Arquitectura Técnica](contexto/02-arquitectura-tecnica.md)** — Stack y decisiones de diseño
3. **[Módulos del Sistema](contexto/03-modulos-del-sistema.md)** — Especificación de funcionalidades
4. **[Base de Datos](contexto/04-base-de-datos.md)** — Esquema completo y relaciones
5. **[Roles y Permisos](contexto/05-roles-y-permisos.md)** — Matriz de autorización
6. **[Estructura del Proyecto](contexto/06-estructura-del-proyecto.md)** — Organización del código
7. **[Frontend y Estilos](contexto/07-frontend-y-estilos.md)** — Sistema de diseño
8. **[Rendimiento](contexto/08-rendimiento-y-escalabilidad.md)** — Optimizaciones
9. **[Requerimientos](contexto/09-requerimientos.md)** — Requerimientos funcionales y no funcionales

### Guías Adicionales

- **[TESTING.md](TESTING.md)** — Guía completa de testing
- **[DEPLOYMENT.md](DEPLOYMENT.md)** — Guía de deployment y configuración

### 📊 Análisis y Mejoras del Sistema (Nuevo)

**⭐ Empezar aquí:** [RESUMEN-1-PAGINA.md](RESUMEN-1-PAGINA.md) — Resumen ejecutivo de 1 página

**Documentos completos:**
1. **[RESUMEN-EJECUTIVO-MEJORAS.md](RESUMEN-EJECUTIVO-MEJORAS.md)** — Resumen ejecutivo completo
2. **[ANALISIS-MEJORAS-SISTEMA.md](ANALISIS-MEJORAS-SISTEMA.md)** — Análisis detallado de 16 mejoras
3. **[PLAN-ACCION-MEJORAS.md](PLAN-ACCION-MEJORAS.md)** — Plan de implementación paso a paso
4. **[CHECKLIST-MEJORAS-URGENTES.md](CHECKLIST-MEJORAS-URGENTES.md)** — Checklist de mejoras críticas
5. **[TABLA-COMPARATIVA-MEJORAS.md](TABLA-COMPARATIVA-MEJORAS.md)** — Comparación antes/después

**Estado actual:** 8.0/10 — Sistema funcionalmente completo  
**Objetivo:** 9.5/10 — Sistema de clase mundial  
**Tiempo:** 2-8 semanas según alcance

### 📧 Configuración de Usuarios y Email (Nuevo)

**⭐ Guía rápida:** [CONFIGURACION-USUARIOS-EMAIL.md](CONFIGURACION-USUARIOS-EMAIL.md)

**Funcionalidades implementadas:**
- ✅ Usuario admin principal: `upeducacionuncp@gmail.com`
- ✅ Envío automático de credenciales por email al crear usuarios
- ✅ Recuperación de contraseña por email
- ✅ Configuración de Gmail lista para usar
- ✅ **Emails profesionales con logo institucional** ⭐ NUEVO
- ✅ **Diseño responsive y moderno** ⭐ NUEVO

**Documentos:**
1. **[EMAILS-PROFESIONALES-COMPLETADO.md](EMAILS-PROFESIONALES-COMPLETADO.md)** ⭐ — Emails con logo
2. **[CONFIGURACION-USUARIOS-EMAIL.md](CONFIGURACION-USUARIOS-EMAIL.md)** — Guía completa
3. **[CONFIGURACION-EMAIL.md](CONFIGURACION-EMAIL.md)** — Configuración detallada de Gmail
4. **[crear-admin.sh](crear-admin.sh)** — Script para crear usuario admin

---

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 12.x** — Framework PHP
- **MySQL 8.0** — Base de datos relacional
- **Redis** — Cache y colas (producción)

### Frontend
- **Tailwind CSS 4.0** — Framework CSS utilitario
- **Alpine.js 3.x** — Interactividad ligera
- **Blade** — Motor de plantillas

### Herramientas
- **Vite 7.x** — Build tool
- **Composer** — Gestor de dependencias PHP
- **npm** — Gestor de dependencias JS

---

## 🔐 Seguridad

- ✅ Autenticación nativa de Laravel
- ✅ Autorización con Policies
- ✅ CSRF protection
- ✅ Rate limiting en login
- ✅ Contraseñas con bcrypt (12 rounds)
- ✅ Validación de archivos subidos
- ✅ Protección contra IDOR

---

## 📊 Capacidad

Diseñado para soportar:
- **200-300 estudiantes activos** por semestre
- **20-40 docentes** activos
- **50+ usuarios concurrentes** sin degradación

---

## 🤝 Equipo de Desarrollo

| Desarrollador | Módulos |
|---------------|---------|
| **Juan** | Semestres, Entregas de Tareas |
| **Jhon** | Evaluaciones en Línea, Foro |
| **Zair** | Calificaciones, Notificaciones, Reportes |

---

## 📝 Licencia

Este proyecto es propiedad de **ADESA - Facultad de Educación**.

---

## 📞 Soporte

Para soporte técnico o consultas:
- **Email:** soporte@adesa.edu.pe
- **Documentación:** Ver carpeta `/contexto/`
- **Issues:** Reportar en el repositorio

---

## 🎯 Roadmap

### ✅ Completado (v1.0.0-beta)
- ✅ Sistema de autenticación
- ✅ Gestión de usuarios y cursos
- ✅ Aula virtual completa (16 semanas, materiales, tareas)
- ✅ Evaluaciones en línea (4 tipos de preguntas)
- ✅ Calificaciones (libreta de notas)
- ✅ Foro de discusión
- ✅ Notificaciones (8 tipos de eventos)
- ✅ Reportes básicos
- ✅ **17/17 módulos implementados (100% funcionalidad)**

### 🔄 En Desarrollo (v1.5.0) — Próximas 2 semanas
- 🔴 **Carga masiva de usuarios** (Excel, 200 en 10 min)
- 🔴 **Matriculación masiva** (Excel, 200 en 5 min)
- 🔴 **Exportación de reportes** (Excel/PDF)
- 🔴 **Búsqueda global** (Ctrl+K)
- 🔴 **Tests críticos** (60% → 80% cobertura)
- 🔴 **Calendario académico** (vista mensual + eventos)

### 📋 Planificado (v2.0.0) — Próximos 2 meses
- 🟡 Recordatorios automáticos por email
- 🟡 Historial de auditoría
- 🟡 Estadísticas avanzadas (6 gráficos)
- 🟢 Notificaciones en tiempo real (WebSockets)
- 🟢 Modo oscuro
- 🟢 PWA (App móvil instalable)

### 🔮 Futuro (v3.0.0)
- Chat en tiempo real
- Integración con Google Classroom
- 2FA para administradores
- Almacenamiento en AWS S3

**Ver análisis completo:** [ANALISIS-MEJORAS-SISTEMA.md](ANALISIS-MEJORAS-SISTEMA.md)

---

<p align="center">
  Desarrollado con ❤️ para la Facultad de Educación de la uncp  — ADESA
</p>
