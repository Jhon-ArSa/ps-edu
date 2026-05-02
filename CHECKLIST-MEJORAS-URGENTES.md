# ✅ CHECKLIST — Mejoras Urgentes PS-EDU

**Fecha:** 30 de abril de 2026  
**Prioridad:** 🔴 ALTA — Implementar en las próximas 2 semanas

---

## 🎯 OBJETIVO

Implementar las **5 mejoras más críticas** que transformarán el sistema de **8.0/10 a 9.0/10** en solo 2 semanas.

---

## 📋 FASE 1: QUICK WINS (Semana 1)

### ✅ DÍA 1: Matriculación Masiva

**Objetivo:** Matricular 200 alumnos en 5 minutos (antes: 2 horas)

#### Tareas
- [ ] Crear migración para tabla `enrollment_imports` (log de importaciones)
- [ ] Crear `app/Imports/EnrollmentsImport.php` con Laravel Excel
- [ ] Agregar ruta `POST /admin/enrollments/import`
- [ ] Crear vista `resources/views/admin/enrollments/import.blade.php`
- [ ] Agregar botón "Importar Matrículas" en `/admin/enrollments`
- [ ] Crear plantilla Excel de ejemplo (`storage/templates/matriculas-ejemplo.xlsx`)
- [ ] Validar: alumno existe, curso existe, no duplicados
- [ ] Mostrar reporte: X creadas, Y errores
- [ ] Escribir test: `test_admin_can_import_enrollments_from_excel()`

#### Entregables
- ✅ Vista de importación funcional
- ✅ Plantilla Excel descargable
- ✅ Validación de datos
- ✅ Reporte de errores claro
- ✅ 1 test

**Tiempo estimado:** 6-8 horas

---

### ✅ DÍA 2: Carga Masiva de Usuarios

**Objetivo:** Registrar 200 usuarios en 10 minutos (antes: 3 horas)

#### Tareas
- [ ] Crear `app/Imports/UsersImport.php`
- [ ] Agregar ruta `POST /admin/users/import`
- [ ] Crear vista `resources/views/admin/users/import.blade.php`
- [ ] Agregar botón "Importar Usuarios" en `/admin/users`
- [ ] Crear plantilla Excel de ejemplo (`storage/templates/usuarios-ejemplo.xlsx`)
- [ ] Validar: email único, DNI único, código único (alumnos)
- [ ] Generar contraseña temporal: `password123`
- [ ] Crear perfil automático (AlumnoProfile o DocenteProfile)
- [ ] Enviar email con credenciales: `WelcomeNotification`
- [ ] Mostrar reporte: X creados, Y errores
- [ ] Escribir test: `test_admin_can_import_users_from_excel()`

#### Entregables
- ✅ Vista de importación funcional
- ✅ Plantilla Excel descargable
- ✅ Validación estricta
- ✅ Email de bienvenida
- ✅ 1 test

**Tiempo estimado:** 6-8 horas

---

### ✅ DÍA 3: Exportación de Reportes

**Objetivo:** Exportar libreta de notas a Excel/PDF

#### Tareas
- [ ] Instalar dependencias: `composer require maatwebsite/excel barryvdh/laravel-dompdf`
- [ ] Crear `app/Exports/GradesExport.php`
- [ ] Agregar ruta `GET /docente/courses/{course}/grades/export-excel`
- [ ] Agregar ruta `GET /docente/courses/{course}/grades/export-pdf`
- [ ] Agregar botones en vista de libreta de notas
- [ ] Diseñar formato Excel: código, nombre, notas, promedio
- [ ] Diseñar formato PDF: logo institucional, tabla profesional
- [ ] Agregar filtros: por semana, por tipo de evaluación
- [ ] Escribir test: `test_docente_can_export_grades_to_excel()`
- [ ] Escribir test: `test_docente_can_export_grades_to_pdf()`

#### Entregables
- ✅ Exportación a Excel funcional
- ✅ Exportación a PDF funcional
- ✅ Formato profesional
- ✅ 2 tests

**Tiempo estimado:** 6-8 horas

---

### ✅ DÍAS 4-5: Búsqueda Global

**Objetivo:** Buscar cualquier cosa en 5 segundos (Ctrl+K)

#### Tareas
- [ ] Crear ruta `GET /search?q=...`
- [ ] Crear controlador `SearchController@index`
- [ ] Implementar búsqueda en: cursos, alumnos, docentes, tareas, evaluaciones, anuncios
- [ ] Limitar resultados: 5 por categoría
- [ ] Agregar barra de búsqueda en header (componente Alpine.js)
- [ ] Implementar modal de resultados con Alpine.js
- [ ] Agregar atajo de teclado: Ctrl+K (Windows) / Cmd+K (Mac)
- [ ] Agrupar resultados por tipo
- [ ] Agregar iconos por tipo de resultado
- [ ] Resaltar texto coincidente
- [ ] Escribir test: `test_users_can_search_globally()`
- [ ] Escribir test: `test_search_respects_permissions()`

#### Entregables
- ✅ Barra de búsqueda en header
- ✅ Modal de resultados
- ✅ Atajo Ctrl+K funcional
- ✅ Búsqueda con AJAX
- ✅ 2 tests

**Tiempo estimado:** 12-16 horas

---

## 📋 FASE 2: ESTABILIZACIÓN (Semana 2)

### ✅ DÍAS 6-8: Tests para Evaluaciones

**Objetivo:** Cobertura 0% → 80% en módulo de evaluaciones

#### Tests a Implementar (15 tests)
- [ ] `test_docente_can_create_evaluation()`
- [ ] `test_docente_can_add_multiple_choice_question()`
- [ ] `test_docente_can_add_true_false_question()`
- [ ] `test_docente_can_add_short_answer_question()`
- [ ] `test_docente_cannot_edit_published_evaluation()`
- [ ] `test_alumno_can_start_evaluation_if_enrolled()`
- [ ] `test_alumno_cannot_start_evaluation_if_not_enrolled()`
- [ ] `test_alumno_cannot_start_if_max_attempts_reached()`
- [ ] `test_evaluation_auto_submits_when_time_expires()`
- [ ] `test_multiple_choice_questions_are_auto_graded()`
- [ ] `test_short_answer_questions_require_manual_grading()`
- [ ] `test_alumno_can_see_results_if_enabled()`
- [ ] `test_alumno_cannot_see_results_if_disabled()`
- [ ] `test_docente_can_delete_draft_evaluation()`
- [ ] `test_docente_cannot_delete_published_evaluation()`

#### Entregables
- ✅ 15 tests pasando
- ✅ Cobertura 80%+ en EvaluationController

**Tiempo estimado:** 18-24 horas

---

### ✅ DÍAS 9-10: Calendario Académico

**Objetivo:** Vista unificada de todas las fechas importantes

#### Tareas
- [ ] Instalar: `npm install @fullcalendar/core @fullcalendar/daygrid`
- [ ] Crear migración: `calendar_events` table
- [ ] Crear modelo `CalendarEvent`
- [ ] Crear controlador `CalendarController`
- [ ] Agregar rutas: `GET /calendario`, `POST /calendario/events`
- [ ] Crear vista `resources/views/calendario/index.blade.php`
- [ ] Integrar FullCalendar con Alpine.js
- [ ] Generar eventos automáticos: tareas (rojo), evaluaciones (naranja)
- [ ] Permitir eventos manuales: feriados, exámenes (solo admin)
- [ ] Agregar filtros: por curso, por tipo
- [ ] Agregar modal para crear/editar eventos
- [ ] Sincronizar con fechas de tareas/evaluaciones
- [ ] Escribir test: `test_users_can_view_calendar()`
- [ ] Escribir test: `test_admin_can_create_manual_events()`

#### Entregables
- ✅ Calendario mensual funcional
- ✅ Eventos automáticos (tareas, evaluaciones)
- ✅ Eventos manuales (admin)
- ✅ Filtros por curso/tipo
- ✅ 2 tests

**Tiempo estimado:** 12-16 horas

---

## 📊 RESUMEN DE ENTREGABLES

### Semana 1: Quick Wins
| Día | Mejora | Tiempo | Tests |
|-----|--------|--------|-------|
| 1 | Matriculación masiva | 6-8h | 1 |
| 2 | Carga masiva usuarios | 6-8h | 1 |
| 3 | Exportación reportes | 6-8h | 2 |
| 4-5 | Búsqueda global | 12-16h | 2 |
| **Total** | **4 mejoras** | **30-40h** | **6 tests** |

### Semana 2: Estabilización
| Día | Mejora | Tiempo | Tests |
|-----|--------|--------|-------|
| 6-8 | Tests evaluaciones | 18-24h | 15 |
| 9-10 | Calendario académico | 12-16h | 2 |
| **Total** | **2 mejoras** | **30-40h** | **17 tests** |

### **TOTAL: 6 mejoras, 60-80 horas, 23 tests**

---

## ✅ CHECKLIST DE VERIFICACIÓN

### Antes de Empezar
- [ ] Crear branch: `git checkout -b feature/mejoras-urgentes`
- [ ] Backup de base de datos: `php artisan backup:run`
- [ ] Configurar entorno de desarrollo
- [ ] Instalar dependencias: `composer install && npm install`

### Durante Implementación
- [ ] Commit frecuente: cada funcionalidad completada
- [ ] Escribir tests antes de implementar (TDD)
- [ ] Ejecutar tests: `php artisan test` después de cada cambio
- [ ] Verificar cobertura: `php artisan test --coverage`
- [ ] Documentar cambios en `CHANGELOG.md`

### Después de Cada Día
- [ ] Push a GitHub: `git push origin feature/mejoras-urgentes`
- [ ] Code review (si hay equipo)
- [ ] Probar manualmente en navegador
- [ ] Verificar responsividad móvil

### Antes de Merge
- [ ] Todos los tests pasan: `php artisan test`
- [ ] Cobertura mínima 70%: `php artisan test --coverage --min=70`
- [ ] Sin errores de linter: `./vendor/bin/pint`
- [ ] Probar en staging
- [ ] Actualizar documentación
- [ ] Merge a `main`: `git merge feature/mejoras-urgentes`

---

## 🎯 CRITERIOS DE ÉXITO

### Técnicos
- ✅ 6 mejoras implementadas y funcionando
- ✅ 23 tests nuevos (total: 55 tests)
- ✅ Cobertura de tests: 60% → 75%+
- ✅ Cero errores críticos
- ✅ Tiempo de respuesta <500ms

### Funcionales
- ✅ Matriculación de 200 alumnos en <5 minutos
- ✅ Carga de 200 usuarios en <10 minutos
- ✅ Exportación de reportes funcional
- ✅ Búsqueda global en <5 segundos
- ✅ Calendario académico visible y útil

### Negocio
- ✅ Ahorro de 10+ horas/semana en tareas administrativas
- ✅ Reducción de errores en matriculación
- ✅ Mejora en satisfacción de usuarios
- ✅ Sistema listo para 200-300 usuarios

---

## 🚨 RIESGOS Y MITIGACIÓN

| Riesgo | Probabilidad | Mitigación |
|--------|--------------|------------|
| Tests rompen funcionalidad existente | Media | Ejecutar suite completa antes de merge |
| Importación con datos incorrectos | Alta | Validación estricta + reporte de errores |
| Búsqueda lenta con muchos resultados | Media | Limitar a 5 resultados por categoría |
| Calendario confunde usuarios | Baja | Tutorial en primera visita |

---

## 📞 SOPORTE

**Desarrollador:** Kiro AI  
**Fecha:** 30 de abril de 2026  
**Versión:** 1.0

---

## 🚀 ¿LISTO PARA EMPEZAR?

```bash
# 1. Crear branch
git checkout -b feature/mejoras-urgentes

# 2. Instalar dependencias
composer require maatwebsite/excel barryvdh/laravel-dompdf
npm install @fullcalendar/core @fullcalendar/daygrid

# 3. Empezar con Día 1: Matriculación Masiva
php artisan make:import EnrollmentsImport --model=Enrollment

# ¡Vamos! 🚀
```

---

**¡Éxito en la implementación! 💪**
