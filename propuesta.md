Sistema Propuesto
Plataforma de Gestión Académica y Aula Virtual
Objetivo
Desarrollar una plataforma web que permita administrar asignaturas, docentes, estudiantes, materiales académicos, tareas, evaluaciones y comunicación educativa dentro de la facultad.
El sistema permitirá:
•	Gestionar cursos por semestre
•	Compartir materiales educativos
•	Gestionar tareas y evaluaciones
•	Registrar notas
•	Facilitar comunicación docente-estudiante
•	Supervisar el cumplimiento académico
________________________________________
1. Actores del sistema
1️⃣ Administrador / Directivo
Responsable de la gestión general del sistema.
Funciones:
•	Registrar docentes
•	Registrar estudiantes
•	Crear asignaturas
•	Asignar docentes a asignaturas
•	Gestionar semestres académicos
•	Supervisar actividad docente
•	Generar reportes académicos
•	Administrar usuarios
________________________________________
2️⃣ Docente
Responsable del curso.
Funciones:
•	Gestionar su aula virtual
•	Subir materiales académicos
•	Crear tareas
•	Crear evaluaciones
•	Calificar estudiantes
•	Registrar notas
•	Ver progreso de estudiantes
•	Comunicarse con estudiantes
________________________________________
3️⃣ Estudiante
Usuario que recibe el contenido académico.
Funciones:
•	Acceder a sus asignaturas
•	Ver materiales
•	Descargar archivos
•	Entregar tareas
•	Ver sus calificaciones
•	Participar en foros o chats
________________________________________
2. Estructura académica del sistema
Los sistemas reales organizan todo de forma jerárquica:
Facultad
   └── Semestre
          └── Asignatura
                 └── Docente
                        └── Estudiantes
                               └── Contenido semanal
Ejemplo:
Semestre: 2025-I

Asignatura: Programación II
Docente: Ing. Pérez

Semana 1
- Sílabo
- Presentación
- Video
- Tarea 1

Semana 2
- Lectura
- Diapositivas
- Tarea 2
________________________________________
3. Módulos del sistema
Un sistema profesional normalmente tiene varios módulos.
________________________________________
1️⃣ Módulo de Autenticación
Permite acceder al sistema.
Funciones:
•	Inicio de sesión
•	Recuperación de contraseña
•	Cierre de sesión
•	Gestión de roles
Roles:
•	Administrador
•	Docente
•	Estudiante
________________________________________
2️⃣ Módulo de Gestión Académica
Permite administrar la estructura educativa.
Funciones:
•	Crear semestres académicos
•	Crear asignaturas
•	Registrar docentes
•	Registrar estudiantes
•	Asignar docentes a cursos
•	Matricular estudiantes
Ejemplo:
Curso	Docente
Programación II	Pérez
Matemática	Gómez
________________________________________
3️⃣ Módulo de Aula Virtual
Es el núcleo del sistema.
Cada asignatura tiene su aula virtual.
Contenido organizado por semanas:
Semana	Contenido
1	Sílabus, PDF
2	Diapositivas
3	Video
El docente puede subir:
•	documentos
•	enlaces
•	videos
•	presentaciones
________________________________________
4️⃣ Módulo de Materiales Educativos
Permite subir recursos.
Tipos de recursos:
•	PDF
•	Word
•	PowerPoint
•	Videos
•	Links
•	Formularios
Funciones:
Docente:
•	subir material
•	editar material
•	eliminar material
Estudiante:
•	ver material
•	descargar material
________________________________________
5️⃣ Módulo de Tareas
Similar a lo que hacen los LMS reales.
Docente puede:
•	crear tarea
•	definir fecha límite
•	definir tipo de entrega
Estudiante puede:
•	subir archivo
•	editar entrega
•	ver estado
Estado de tarea:
Estado	Descripción
Pendiente	No entregada
Entregada	Subida
Calificada	Evaluada
________________________________________
6️⃣ Módulo de Evaluaciones
Este módulo muchas veces no lo mencionan los profesores pero es muy importante.
Permite crear evaluaciones como:
•	exámenes
•	cuestionarios
•	pruebas
Tipos de preguntas:
•	opción múltiple
•	verdadero/falso
•	respuesta corta
•	ensayo
Funciones:
Docente:
•	crear examen
•	definir tiempo
•	definir puntaje
Estudiante:
•	responder evaluación
•	ver resultados
________________________________________
7️⃣ Módulo de Calificaciones (Notas)
Este módulo es clave en sistemas reales.
Permite registrar las notas de:
•	tareas
•	exámenes
•	participación
Ejemplo de tabla de notas:
Estudiante	Tarea 1	Examen	Final
Ana	15	16	16
Luis	12	14	13
Funciones:
Docente:
•	registrar notas
•	editar notas
•	calcular promedio
Estudiante:
•	ver sus notas
•	ver progreso
________________________________________
8️⃣ Módulo de Comunicación
Los sistemas educativos incluyen comunicación interna.
Opciones:
Chat del curso
Permite enviar mensajes.
Foro de discusión
Permite debatir temas.
Ejemplo:
Tema: Dudas sobre la tarea 1
Estudiantes comentan.
________________________________________
9️⃣ Módulo de Anuncios
Docente puede publicar anuncios del curso.
Ejemplo:
Examen el viernes
Los estudiantes reciben notificación.
________________________________________
🔟 Módulo de Supervisión Académica
Este módulo es para directivos.
Permite verificar:
•	cursos activos
•	docentes activos
•	materiales subidos
Ejemplo de reporte:
Curso	Docente	Materiales
Física	Pérez	✔
Álgebra	Gómez	❌
________________________________________
11️⃣ Módulo de Reportes
Genera estadísticas del sistema.
Ejemplos:
•	estudiantes matriculados
•	tareas entregadas
•	promedio de notas
•	actividad docente
________________________________________
4. Requerimientos funcionales
Ejemplo formal:
RF01 – Gestión de usuarios
El sistema debe permitir registrar usuarios con roles de administrador, docente o estudiante.
________________________________________
RF02 – Gestión de asignaturas
El sistema debe permitir crear y administrar asignaturas académicas.
________________________________________
RF03 – Gestión de matrícula
El sistema debe permitir asignar estudiantes a cursos.
________________________________________
RF04 – Gestión de materiales
El sistema debe permitir a los docentes subir materiales educativos.
________________________________________
RF05 – Gestión de tareas
El sistema debe permitir crear y gestionar tareas.
________________________________________
RF06 – Evaluaciones
El sistema debe permitir crear evaluaciones y registrar respuestas.
________________________________________
RF07 – Registro de notas
El sistema debe permitir registrar y visualizar calificaciones.
________________________________________
RF08 – Comunicación
El sistema debe permitir comunicación docente-estudiante.
________________________________________
RF09 – Reportes
El sistema debe generar reportes académicos.
________________________________________
5. Requerimientos no funcionales
RNF01 – Seguridad
Control de acceso mediante autenticación.
________________________________________
RNF02 – Disponibilidad
El sistema debe estar disponible en línea.
________________________________________
RNF03 – Usabilidad
Interfaz simple para estudiantes y docentes.
________________________________________
RNF04 – Rendimiento
El sistema debe soportar múltiples usuarios simultáneos.
________________________________________
6. Ideas profesionales que podrías agregar
Para que tu sistema se vea más avanzado:
Notificaciones
•	aviso de tareas
•	aviso de calificaciones
________________________________________
Calendario académico
Muestra:
•	tareas
•	exámenes
•	eventos
________________________________________
Progreso del estudiante
El estudiante ve:
Curso completado: 60%
________________________________________
Integración con video
Links de:
•	Google Meet
•	Zoom
________________________________________
7. Posible estructura de base de datos
Tablas principales:
•	usuarios
•	roles
•	estudiantes
•	docentes
•	asignaturas
•	matriculas
•	semanas
•	materiales
•	tareas
•	entregas
•	evaluaciones
•	preguntas
•	respuestas
•	notas
•	anuncios
•	mensajes

