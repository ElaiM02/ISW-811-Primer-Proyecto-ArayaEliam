# Estudiante: Edgar Eliam Araya Alvarado

# Aplicaciones Web Utilizando Software Libre

# Proyeccto-1 Laravel From Scratch 2026

# Entregable #2

# Fecha 29/06/2026

# Misael Matamoros Soto

# Authentication and Authorization, segunda parte

## Authorization Using Gates

## ¿Qué es un Gate?

Un Gate es una regla de autorización que determina si un usuario puede realizar una acción específica. Se define en `app/Providers/AppServiceProvider.php`.

## Definir un Gate

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('view-admin', function (User $user) {
        return $user->id === 1; // solo el usuario con ID 1 es admin
    });
}
```

## Método isAdmin en el modelo User

```php
public function isAdmin(): bool
{
    return $this->id === 1; // o verificar un campo 'role'
}
```

Usando el método en el Gate:

```php
Gate::define('view-admin', function (User $user) {
    return $user->isAdmin();
});
```

## Proteger rutas con Gates

**Opción 1 — En la ruta:**
```php
Route::get('/admin', function () {
    return 'Área privada';
})->can('view-admin');
```

![proteccion Admin](Images-entregable02/Auth%20Middleware%202.1%20Bloqueo%20hacia%20Admin.png)
**Opción 2 — Dentro del controlador o closure:**
```php
Route::get('/admin', function () {
    Gate::authorize('view-admin'); // lanza 403 si no autorizado
    return 'Área privada';
});
```
![proteccion Admin opcion2](Images-entregable02/Auth%20Middleware%202.2%20Opcion%202%20bloqueo.png)

## Controlar el código de respuesta

```php
use Illuminate\Auth\Access\Response;

Gate::define('view-admin', function (User $user) {
    return $user->isAdmin()
        ? Response::allow()
        : Response::denyAsNotFound(); // retorna 404 en lugar de 403
});
```

![retorno de codigos](Images-entregable02/Auth%20Middleware%202.3%20%20Control%20de%20codigos.png)

| Método | Código |
|---|---|
| `Response::allow()` | 200 |
| `Response::deny()` | 403 |
| `Response::denyAsNotFound()` | 404 |

Por defecto Laravel requiere un usuario autenticado para correr un Gate. Si el usuario no está logueado, retorna `false` automáticamente sin ejecutar el closure.

---
---

# Authorization Using Policies

## ¿Qué es una Policy?

Una Policy es como un controlador para las reglas de autorización de un modelo específico. Agrupa todas las reglas de acceso relacionadas a ese modelo.

## Crear una Policy

```bash
php artisan make:policy IdeaPolicy --model=Idea
```

Esto genera `app/Policies/IdeaPolicy.php`.

## Definir reglas en la Policy

```php
<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IdeaPolicy
{
    // ¿Puede el usuario actualizar/modificar esta idea?
    public function update(User $user, Idea $idea): bool
    {
        return $user->is($idea->user); // compara si es el mismo usuario
        // o también: return $user->id === $idea->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
```

## Usar la Policy en el controlador

```php
use Illuminate\Support\Facades\Gate;

// Autorizar con Gate (lanza 403 si no autorizado)
public function show(Idea $idea)
{
    Gate::authorize('update', $idea);
    return view('ideas.show', compact('idea'));
}

// Cuando no hay instancia del modelo (create)
public function create()
{
    Gate::authorize('create', Idea::class);
    return view('ideas.create');
}
```

## Proteger rutas con middleware

```php
Route::get('/ideas/{idea}', [IdeaController::class, 'show'])
    ->can('update', 'idea');
```

## Diferencia entre Gates y Policies

| | Gates | Policies |
|---|---|---|
| Uso | Reglas generales | Reglas por modelo |
| Definición | `AppServiceProvider` | Clase dedicada |
| Equivalente a | Route closure | Controlador |

## Regla de oro

Protege **todas** las acciones relevantes: `show`, `edit`, `update` y `destroy`. Un usuario no debería poder ver, editar ni eliminar recursos que no le pertenecen.

---
---


# Digging Deeper

## ¿Por qué usar Vite en lugar de CDN?

El CDN es útil para demos, pero en producción se recomienda instalar los paquetes localmente y compilarlos para optimizar el tamaño de los archivos.

## Configuración inicial

Laravel incluye `vite.config.js` por defecto:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

## Instalar dependencias

```bash
npm install
```

## Instalar DaisyUI localmente

```bash
npm install daisyui
```
![Instalar DaisyUI](Images-entregable02/Vite%203.2%20Instalar%20Daisyui.png)
En `resources/css/app.css`:

```css
@import "tailwindcss";
@plugin "daisyui";

/* Colores personalizados */
@theme {
    --color-primary: #your-color;
}
```

![Color letra](Images-entregable02/Vite%203.1%20Color%20de%20la%20letra.png)

## Reemplazar CDN por directiva Blade

En `layout.blade.php` reemplazar los links del CDN por:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Comandos principales

```bash
# Desarrollo con hot reload (refresca el browser automáticamente)
npm run dev

# Producción (compila y optimiza los archivos)
npm run build
```

## JavaScript personalizado

En `resources/js/app.js`:

```js
// Aquí va tu JavaScript personalizado
// También puedes usar frameworks como Vue o React
```

Si tus cambios de CSS no se reflejan en el navegador, probablemente olvidaste correr `npm run dev`.

---
---

# Notificaciones en Laravel

## ¿Qué son las notificaciones?

Permiten notificar a un usuario cuando ocurre algo en la aplicación (nuevo registro, pago, publicación, etc.). Se pueden enviar por email, base de datos, SMS, entre otros.

## Configuración inicial

Crear la tabla de notificaciones:

```bash
php artisan make:notifications-table
php artisan migrate
```
![Crear tabla notifications](Images-entregable02/Notifications%203.1%20Crear%20tabla%20notifications.png)

## Crear una notificación

```bash
php artisan make:notification IdeaPublished
```

![Crear notifications](Images-entregable02/Notifications%203.2%20crear%20notificacion.png)

Esto genera `app/Notifications/IdeaPublished.php`:

```php
class IdeaPublished extends Notification
{
    public function __construct(public Idea $idea) {}

    // Canal de envío
    public function via(object $notifiable): array
    {
        return ['mail']; // email, database, sms
    }

    // Estructura del email
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('¡Hola!')
            ->line('Publicaste una nueva idea: ' . $this->idea->description)
            ->action('Leerla', url('/ideas/' . $this->idea->id))
            ->line('Gracias por usar nuestra app.');
    }
}
```

## Enviar la notificación en el controlador

```php
use App\Notifications\IdeaPublished;

public function store(StoreIdeaRequest $request)
{
    $idea = Auth::user()->ideas()->create([
        'description' => $request->input('description'),
        'status'      => 'pending',
    ]);

    // Notificar al usuario
    Auth::user()->notify(new IdeaPublished($idea));

    return redirect('/ideas');
}
```

## El trait Notifiable

El modelo `User` ya incluye el trait `Notifiable` por defecto:

```php
class User extends Authenticatable
{
    use Notifiable; // ← permite usar ->notify()
}
```

## Configuración de email en `.env`

**Para desarrollo local con Mailpit:**
```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=admin@tuapp.com
```

**Por defecto Laravel registra emails en:**
```
storage/logs/laravel.log
```

## Probar desde Tinker

```bash
php artisan tinker
```

```php
$user = User::find(1);
$idea = Idea::latest()->first();
$user->notify(new \App\Notifications\IdeaPublished($idea));

// Ver notificaciones del usuario
$user->notifications;
$user->unreadNotifications;
```

![Mailpit](Images-entregable02/Notifications%203.3%20Mailpit.png)
## Canales disponibles

| Canal | Descripción |
|---|---|
| `mail` | Envío por email |
| `database` | Notificaciones en sitio |
| `vonage` | SMS |
| Comunidad | Docenas de drivers adicionales |

---
---

# Colas y Jobs en Laravel

## ¿Por qué usar colas?

Las colas permiten ejecutar tareas largas **en segundo plano** sin hacer esperar al usuario. Ejemplo: enviar 50 emails a un equipo no debería bloquear la respuesta al usuario.

## Conceptos clave

| Término | Analogía | Descripción |
|---|---|---|
| **Job** | Orden de pizza | La tarea que necesita hacerse |
| **Queue** | Pila de órdenes | Contenedor que almacena los jobs |
| **Worker** | El pizzero | Quien toma y ejecuta los jobs |

## Convertir una notificación en queued

Solo implementar la interfaz `ShouldQueue`:

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class IdeaPublished extends Notification implements ShouldQueue
{
    // Sin cambios adicionales, Laravel la encola automáticamente
}
```

![Queue work](Images-entregable02/queue%203.1%20Queue%20work.png)

## Crear un Job

```bash
php artisan make:job UpdateIdeaStatistics
```

Esto genera `app/Jobs/UpdateIdeaStatistics.php`:

![Queue UpdateIdeaStatistics](Images-entregable02/queue%203.2%20Crear%20updateideastatistics.png)

```php
class UpdateIdeaStatistics implements ShouldQueue
{
    public function handle(): void
    {
        // Tarea larga que se ejecuta en segundo plano
        logger('El job está siendo procesado');
    }
}
```

## Despachar un Job

```php
UpdateIdeaStatistics::dispatch();
```

## Configurar el driver de cola en `.env`

```env
QUEUE_CONNECTION=database  # guarda los jobs en la base de datos
# QUEUE_CONNECTION=sync    # ejecuta inmediatamente (sin cola real)
```

## Correr un worker

```bash
# Inicia un worker que procesa los jobs
php artisan queue:work

# Para más rendimiento, abrir múltiples terminales
php artisan queue:work  # terminal 1
php artisan queue:work  # terminal 2
php artisan queue:work  # terminal 3
```

## Comandos útiles de cola

```bash
php artisan queue:work    # procesar jobs
php artisan queue:retry   # reintentar jobs fallidos
php artisan queue:monitor # monitorear el tamaño
php artisan queue:clear   # limpiar la cola
```

Poner un job en la cola **no significa que se procese**. Necesitas tener un worker corriendo para que los jobs se ejecuten.

# How to Get Started Testing Your Code (Testing con Pest PHP)

Pest es un framework de testing sobre PHPUnit, con sintaxis más limpia. Laravel ya lo incluye. Sirve para automatizar las pruebas que hacemos manualmente en el navegador.

## Correr los tests

```bash
php artisan test      # usa Pest por debajo
vendor/bin/pest       # binario directo
```

## Unit vs Feature Tests

- **Unit:** muy puntual y de bajo nivel (instancio una clase, llamo un método, espero un resultado).
- **Feature / browser:** amplio (abro el navegador, envío el formulario, espero quedar autenticado). Se recomienda empezar por estos.

## Feature test básico

```php
it('returns a successful response', function () {
    $this->get('/')->assertStatus(200);
});
```

Si la ruta no existe, falla con 404. En este proyecto `/` redirige a `/ideas`.

## Browser testing (referencia del video)

Pest puede abrir un navegador real usando `visit()` en lugar de `get()` (wrapper sobre Playwright):

```php
it('registers a user', function () {
    visit('/register')
        ->fill('name', 'Jane Doe')
        ->fill('email', 'jane@example.com')
        ->fill('password', 'password123')
        ->press('@register-button')   // data-test, no el texto
        ->assertPath('/ideas');

    $this->assertAuthenticated();
});
```

**Problema típico:** presionar por texto (`Register`) hace clic en el primer botón de la página (navbar). Solución: identificar el botón con `data-test` y accederlo con el prefijo `@`.

```blade
<button type="submit" data-test="register-button">Register</button>
```

> El browser testing con `visit()` requiere **Pest 4 / PHP 8.3**. Tu VM usa **PHP 8.2**, así que esta parte queda solo como referencia; para las evidencias se usan los feature tests de abajo.

## Código usado en la VM (Pest 3 / PHP 8.2)

Los feature tests reproducen las mismas aserciones sin abrir navegador. Se usa SQLite en memoria para no borrar la base `lfts` (en `phpunit.xml`):

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

`tests/Feature/IdeaTest.php`:

```php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a user', function () {
    $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/ideas');

    $this->assertAuthenticated();
});

it('shows all ideas', function () {
    $user = User::factory()->create();
    $user->ideas()->create(['description' => 'Build a thing', 'status' => 'pending']);

    $this->actingAs($user)->get('/ideas')->assertSee('Build a thing');
});
```

Ejecutar y capturar el resultado en verde:

```bash
vendor/bin/pest
```

**Equivalencias:** `visit()->fill()->press()` ↔ `post()`; `assertPath()` ↔ `assertRedirect()`; `assertSee()` igual en ambos.

---
---

# Final Project Setup (Configuración del proyecto final)

Se arranca el proyecto final: una app para **coleccionar ideas** (aquí, posibles cursos). Soporta autenticación, flash messaging, notificaciones, filtrado por estado (completadas / en progreso / pendientes), descripción con markdown, imágenes, tareas ("steps"), links, validación y perfil de usuario. Es simple de entender pero completa para cubrir todo el proceso de construir una app con Laravel.

## Crear la app y subirla a GitHub

```bash
laravel new idea      # crear proyecto (con Pest)
cd idea

git init
git add .
git commit -m "initial commit"

git remote add origin <url-del-repo>
git push -u origin main
```

## Despliegue con Laravel Forge (referencia)

Se muestra el deploy a producción con **Laravel Forge** (forge.laravel.com): crear servidor, conectar el repositorio de GitHub, configurar base de datos, instalar dependencias, correr migraciones y desplegar. En segundos el sitio queda en línea. *(En este curso el despliegue es en tu VM, así que esta parte es solo de referencia.)*

## Herramientas de desarrollo (tooling)

### Laravel Pint (formateo de código)

Pint viene como dependencia de desarrollo. Es un formateador de estilo con opinión (comillas simples, coma final, llaves en su lugar, etc.):

```bash
vendor/bin/pint
```

### Scripts de Composer

En `composer.json` se pueden crear scripts propios. Ejemplo de un alias `format`:

```json
"scripts": {
    "format": [
        "rector",
        "pint"
    ]
}
```

```bash
composer run format
```

### Rector (modernizar el código)

Rector escana y actualiza el código PHP (agrega tipos, `declare(strict_types=1)`, early returns, elimina código muerto):

```bash
composer require rector/rector --dev
composer require driftingly/rector-laravel --dev   # reglas específicas de Laravel
vendor/bin/rector                                   # aplica los cambios
```

Genera un `rector.php` en la raíz donde se configuran las rutas, reglas a omitir y los *sets* (por ejemplo el set de Laravel y `strict types`).

### CodeRabbit y Laravel Boost (opcionales)

- **CodeRabbit:** revisiones de código automáticas por commit (detecta vulnerabilidades y malas prácticas).
- **Laravel Boost:** paquete oficial que da contexto del proyecto a asistentes de IA vía servidores MCP.

```bash
composer require laravel/boost --dev
php artisan boost:install
```

## Resumen del flujo

Crear proyecto → inicializar Git → subir a GitHub → desplegar (Forge / VM) → configurar tooling (Pint, Rector, scripts de Composer) → opcionalmente CodeRabbit y Laravel Boost. No es obligatorio usar todo; es el flujo recomendado para un proyecto real.

![Tooling configurado](Images-entregable02/Final%20Project%20Setup%204.1%20tooling%20configuration.png)
