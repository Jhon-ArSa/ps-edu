# Contexto del Sistema — PS-EDU

Documentación técnica y funcional completa del sistema de Intranet Académica **PS-EDU** de la Facultad de Educación (FAEDU) — ADESA.

> Este directorio es la fuente de verdad del sistema. Toda decisión de diseño, arquitectura y funcionalidad está registrada aquí.

---

## Índice de Documentos

| # | Archivo | Descripción |
|---|---|---|
| 1 | [01-vision-y-alcance.md](./01-vision-y-alcance.md) | Visión, objetivos, alcance y contexto institucional |
| 2 | [02-arquitectura-tecnica.md](./02-arquitectura-tecnica.md) | Stack tecnológico, patrones y decisiones de arquitectura |
| 3 | [03-modulos-del-sistema.md](./03-modulos-del-sistema.md) | Especificación completa de todos los módulos |
| 4 | [04-base-de-datos.md](./04-base-de-datos.md) | Esquema completo de base de datos, relaciones e índices |
| 5 | [05-roles-y-permisos.md](./05-roles-y-permisos.md) | Roles, matriz de permisos y modelo de autorización |
| 6 | [06-estructura-del-proyecto.md](./06-estructura-del-proyecto.md) | Estructura de carpetas, convenciones y organización del código |
| 7 | [07-frontend-y-estilos.md](./07-frontend-y-estilos.md) | Estrategia frontend, sistema de diseño, CSS/JS y componentes |
| 8 | [08-rendimiento-y-escalabilidad.md](./08-rendimiento-y-escalabilidad.md) | Optimización para 200–300 usuarios concurrentes |
| 9 | [09-requerimientos.md](./09-requerimientos.md) | Requerimientos funcionales y no funcionales formales |

---

## Estado del Sistema

| Módulo | Estado |
|---|---|
| Autenticación y sesiones | ✅ Implementado |
| Gestión de usuarios (Admin) | ✅ Implementado |
| Gestión de cursos | ✅ Implementado |
| Aula virtual (semanas, materiales) | ✅ Implementado |
| Tareas (creación docente) | ✅ Implementado |
| Matrículas | ✅ Implementado |
| Anuncios / Intranet | ✅ Implementado |
| Perfil docente (Escalafón) | ✅ Implementado |
| Configuración del sistema | ✅ Implementado |
| Semestres académicos | 🔲 Pendiente |
| Entrega de tareas (alumno) | 🔲 Pendiente |
| Calificaciones / Libreta de notas | 🔲 Pendiente |
| Evaluaciones en línea | 🔲 Pendiente |
| Foro de discusión | 🔲 Pendiente |
| Notificaciones en sistema | 🔲 Pendiente |
| Reportes y estadísticas | 🔲 Pendiente |
| Calendario académico | 🔲 Pendiente |

---

## Información del Proyecto

| Campo | Valor |
|---|---|
| Versión | 1.0.0-beta |
| Framework | Laravel 12.x |
| PHP | 8.2+ |
| Base de datos | MySQL 8 (AWS RDS) |
| Última actualización | 2026-03-06 |
| Capacidad objetivo | 200–300 estudiantes activos |
