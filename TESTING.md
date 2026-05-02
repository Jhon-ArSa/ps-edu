# 🧪 GUÍA DE TESTING — PS-EDU

## 📋 Índice

1. [Configuración del Entorno de Testing](#configuración-del-entorno-de-testing)
2. [Ejecutar Tests](#ejecutar-tests)
3. [Escribir Nuevos Tests](#escribir-nuevos-tests)
4. [Cobertura de Tests](#cobertura-de-tests)
5. [Tests Implementados](#tests-implementados)
6. [Mejores Prácticas](#mejores-prácticas)

---

## 🔧 Configuración del Entorno de Testing

### 1. Base de Datos de Testing

Los tests usan SQLite en memoria para mayor velocidad. La configuración ya está en `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### 2. Verificar Configuración

```bash
# Ver configuración de PHPUnit
cat phpunit.xml

# Verificar que las dependencias están instaladas
composer install
```

---

## ▶️ Ejecutar Tests

### Todos los Tests

```bash
# Ejecutar toda la suite
php artisan test

# O con PHPUnit directamente
./vendor/bin/phpunit
```

### Tests Específicos

```bash
# Por archivo
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Por método
php artisan test --filter test_users_can_authenticate_with_valid_credentials

# Por grupo (si se usan @group annotations)
php artisan test --group authentication
```

### Con Cobertura de Código

```bash
# Requiere Xdebug instalado
php artisan test --coverage

# Cobertura mínima requerida
php artisan test --coverage --min=60
```

### Modo Verbose

```bash
# Ver detalles de cada test
php artisan test --verbose

# Ver queries SQL ejecutadas
php artisan test --verbose --debug
```

---

## ✍️ Escribir Nuevos Tests

### Estructura de un Test

```php
<?php

namespace Tests\Feature\MiModulo;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiModuloTest extends TestCase
{
    use RefreshDatabase; // Resetea la BD entre tests

    public function test_descripcion_clara_de_lo_que_prueba(): void
    {
        // 1. ARRANGE — Preparar datos
        $user = User::factory()->create(['role' => 'admin']);

        // 2. ACT — Ejecutar acción
        $response = $this->actingAs($user)->get('/admin/dashboard');

        // 3. ASSERT — Verificar resultado
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }
}
```

### Crear Nuevo Test

```bash
# Test de Feature (HTTP)
php artisan make:test MiModulo/MiModuloTest

# Test Unitario
php artisan make:test MiModulo/MiModuloTest --unit
```

### Factories para Datos de Prueba

```php
// Crear usuario básico
$user = User::factory()->create();

// Con atributos específicos
$admin = User::factory()->create(['role' => 'admin']);

// Múltiples registros
$students = User::factory()->count(10)->create(['role' => 'alumno']);

// Con relaciones
$course = Course::factory()
    ->forTeacher($teacher)
    ->create();
```

---

## 📊 Cobertura de Tests

### Tests Implementados Actualmente

| Módulo | Tests | Cobertura Estimada |
|--------|-------|-------------------|
| Autenticación | ✅ 7 tests | ~80% |
| Políticas (CoursePolicy) | ✅ 9 tests | ~90% |
| Matrículas | ✅ 7 tests | ~70% |
| Entregas de Tareas | ✅ 9 tests | ~75% |
| **TOTAL** | **32 tests** | **~60%** |

### Prioridades para Nuevos Tests

#### 🔴 ALTA PRIORIDAD

1. **Evaluaciones en Línea**
   - Iniciar evaluación
   - Responder preguntas
   - Envío automático al vencer tiempo
   - Cálculo de puntaje

2. **Calificaciones**
   - Cálculo de promedios
   - Ponderación de notas
   - Exportación de libreta

3. **Foro**
   - Crear tema
   - Responder
   - Permisos de moderación

#### 🟡 MEDIA PRIORIDAD

4. **Materiales**
   - Subir archivos
   - Reordenar
   - Validación de tipos

5. **Anuncios**
   - Targeting por rol
   - Programación de publicación

6. **Notificaciones**
   - Envío correcto
   - Marcado como leída

#### 🟢 BAJA PRIORIDAD

7. **Reportes**
8. **Perfil de Usuario**
9. **Soporte Técnico**

---

## 🎯 Tests Implementados

### 1. Autenticación (`tests/Feature/Auth/AuthenticationTest.php`)

- ✅ Renderizar pantalla de login
- ✅ Login con credenciales válidas
- ✅ Rechazo de contraseña incorrecta
- ✅ Bloqueo de cuentas inactivas
- ✅ Redirección por rol
- ✅ Logout
- ✅ Regeneración de sesión

### 2. Políticas de Curso (`tests/Feature/Policies/CoursePolicyTest.php`)

- ✅ Admin puede gestionar cualquier curso
- ✅ Docente puede gestionar su curso
- ✅ Docente NO puede gestionar curso ajeno
- ✅ Alumno NO puede gestionar cursos
- ✅ Admin puede ver cualquier curso
- ✅ Docente puede ver su curso
- ✅ Alumno matriculado puede ver curso
- ✅ Alumno NO matriculado NO puede ver curso
- ✅ Alumno retirado NO puede ver curso

### 3. Matrículas (`tests/Feature/Enrollment/EnrollmentTest.php`)

- ✅ Docente puede matricular en su curso
- ✅ Docente NO puede matricular en curso ajeno
- ✅ Prevención de matrículas duplicadas
- ✅ Re-matrícula reactiva matrícula retirada
- ✅ Docente puede retirar alumno
- ✅ Alumno solo ve sus matrículas activas

### 4. Entregas de Tareas (`tests/Feature/Submission/SubmissionTest.php`)

- ✅ Alumno matriculado puede entregar
- ✅ Alumno NO matriculado NO puede entregar
- ✅ Docente puede calificar entrega
- ✅ Docente NO puede calificar entrega de otro curso
- ✅ Alumno puede actualizar entrega antes de calificación
- ✅ Alumno NO puede actualizar entrega calificada
- ✅ Validación de tamaño de archivo

---

## 🏆 Mejores Prácticas

### 1. Nombres de Tests Descriptivos

```php
// ❌ MAL
public function test_login(): void

// ✅ BIEN
public function test_users_can_authenticate_with_valid_credentials(): void
```

### 2. Usar RefreshDatabase

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MiTest extends TestCase
{
    use RefreshDatabase; // Limpia BD entre tests
}
```

### 3. Arrange-Act-Assert

```php
public function test_example(): void
{
    // ARRANGE — Preparar
    $user = User::factory()->create();
    
    // ACT — Actuar
    $response = $this->actingAs($user)->get('/dashboard');
    
    // ASSERT — Verificar
    $response->assertStatus(200);
}
```

### 4. Un Concepto por Test

```php
// ❌ MAL — Prueba múltiples cosas
public function test_user_management(): void
{
    // Crea usuario
    // Edita usuario
    // Elimina usuario
}

// ✅ BIEN — Un test por concepto
public function test_admin_can_create_user(): void { }
public function test_admin_can_edit_user(): void { }
public function test_admin_can_delete_user(): void { }
```

### 5. Usar Factories en Lugar de Crear Manualmente

```php
// ❌ MAL
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);

// ✅ BIEN
$user = User::factory()->create(['role' => 'admin']);
```

### 6. Limpiar Archivos de Prueba

```php
use Illuminate\Support\Facades\Storage;

protected function setUp(): void
{
    parent::setUp();
    Storage::fake('public'); // Filesystem falso
}
```

### 7. Assertions Específicas

```php
// ❌ MAL — Assertion genérica
$this->assertTrue($user->isAdmin());

// ✅ BIEN — Assertion específica
$this->assertEquals('admin', $user->role);
```

---

## 🚀 Integración Continua (CI)

### GitHub Actions (Ejemplo)

Crear `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, pdo_sqlite
          
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist
        
      - name: Run Tests
        run: php artisan test --coverage --min=60
```

---

## 📝 Checklist Pre-Producción

Antes de desplegar a producción, verificar:

- [ ] Todos los tests pasan: `php artisan test`
- [ ] Cobertura mínima 60%: `php artisan test --coverage --min=60`
- [ ] No hay warnings de deprecación
- [ ] Tests de políticas de autorización completos
- [ ] Tests de módulos críticos (entregas, evaluaciones, calificaciones)
- [ ] Validación de archivos subidos
- [ ] Rate limiting funciona correctamente

---

## 🆘 Troubleshooting

### Error: "Database file not found"

```bash
# Verificar configuración en phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Error: "Class not found"

```bash
# Regenerar autoload
composer dump-autoload
```

### Tests Lentos

```bash
# Usar SQLite en memoria (ya configurado)
# Evitar usar RefreshDatabase si no es necesario
# Usar DatabaseTransactions para tests que no modifican esquema
```

### Factories No Funcionan

```bash
# Verificar que el factory existe
ls database/factories/

# Regenerar autoload
composer dump-autoload
```

---

## 📚 Recursos Adicionales

- [Laravel Testing Documentation](https://laravel.com/docs/12.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Test Driven Laravel](https://course.testdrivenlaravel.com/)
- Documentación del proyecto: `contexto/09-requerimientos.md`

---

**Última actualización:** 2026-04-29
