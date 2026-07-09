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

---
---

# Design Your Model Layer (Diseñar la capa de modelos)

Antes de la UI se ordena el **dominio y la estructura de base de datos**: modelos, migraciones, relaciones, casts y factories, con tests que prueban que todo funciona.

## Generar el modelo Idea con todo lo necesario

```bash
php artisan make:model Idea -mfrc --policy
# -m migración, -f factory, -r form request, -c controller, --policy policy
```

![Modelo Idea](Images-entregable02/Model's%20Layer%204.1%20Crear%20lo%20necesario%20para%20modelo%20Idea.png)


## Diseñar la migración de ideas

```php
Schema::create('ideas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // cada idea es de un usuario
    $table->string('title');                     // requerido
    $table->text('description')->nullable();      // opcional
    $table->string('status')->default('pending'); // pending / in_progress / completed
    $table->json('links')->default('[]');         // lista de links como JSON
    $table->string('image')->nullable();          // ruta de imagen destacada
    $table->timestamps();
});
```

## Configurar defaults de Eloquent

En `app/Providers/AppServiceProvider.php`, dentro de `boot()`:

```php
use Illuminate\Database\Eloquent\Model;

Model::unguard();                              // sin protección de mass assignment
Model::shouldBeStrict();                       // previene lazy loading (N+1) y atributos inexistentes
Model::automaticallyEagerLoadRelationships();  // carga relaciones automáticamente
```

## Enum para el status

En lugar de repetir strings ("pending", etc.) por todo el código, se crea un enum:

```bash
php artisan make:enum IdeaStatus
```

![Crear Enum](Images-entregable02/Model's%20Layer%204.2%20Craar%20un%20Enum.png)


```php
<?php

namespace App\Enums;

enum IdeaStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    // método para mostrar en la UI
    public function label(): string
    {
        return match ($this) {
            IdeaStatus::Pending => 'Pending',
            IdeaStatus::InProgress => 'In Progress',
            IdeaStatus::Completed => 'Completed',
        };
    }
}
```

## Casts en el modelo Idea

```php
protected function casts(): array
{
    return [
        'links' => AsArrayObject::class,  // interactuar con el JSON como array
        'status' => IdeaStatus::class,    // castear a enum
    ];
}

// valor por defecto a nivel de modelo (no solo en la BD)
protected $attributes = [
    'status' => IdeaStatus::Pending->value,
];
```

## Relaciones

```php
// Idea.php
public function user(): BelongsTo { return $this->belongsTo(User::class); }
public function steps(): HasMany  { return $this->hasMany(Step::class); }

// Step.php
public function idea(): BelongsTo { return $this->belongsTo(Idea::class); }

// User.php
public function ideas(): HasMany  { return $this->hasMany(Idea::class); }
```

## Modelo Step (tareas de una idea)

```bash
php artisan make:model Step -mf
```

Migración de steps:

```php
$table->foreignId('idea_id')->constrained()->cascadeOnDelete();
$table->string('description');
$table->boolean('completed')->default(false);
```

## Factories

```php
// IdeaFactory
'user_id' => User::factory(),
'title' => fake()->sentence(),
'description' => fake()->paragraph(),
'links' => [fake()->url()],

// StepFactory
'idea_id' => Idea::factory(),
'description' => fake()->sentence(),
'completed' => false,
```

## Tests de relaciones (Unit)

En `tests/Pest.php`, hacer que los tests Unit también arranquen el framework y refresquen la BD:

```php
pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');
```

```php
it('belongs to a user', function () {
    $idea = Idea::factory()->create();
    expect($idea->user)->toBeInstanceOf(User::class);
});

it('can have steps', function () {
    $idea = Idea::factory()->create();
    expect($idea->steps)->toBeEmpty();

    $idea->steps()->create(['description' => 'Do the thing']);
    expect($idea->refresh()->steps)->toHaveCount(1);
});
```

Los tests van guiando el diseño: si falla porque `completed` no puede ser null, se agrega el default en la migración y en `$attributes`.

---
---

# Tailwind Theme Setup And Initial UI (Tema y UI inicial)

Se arma la base visual: tema de Tailwind, componentes CSS y Blade reutilizables, layout con navbar y las páginas de registro / login con su lógica de autenticación.

## Tema de Tailwind (variables CSS)

En Tailwind v4 las variables de `@theme` se convierten automáticamente en clases (`text-primary`, `bg-primary`, `border-primary`, etc.). En `resources/css/app.css`:

```css
@import "tailwindcss";

@theme {
    --color-background: #0d1117;
    --color-foreground: #e6edf3;
    --color-card: #161b22;
    --color-border: #30363d;
    --color-primary: #3fb950; /* verde */
}
```

## Componentes CSS (button y form)

En `resources/css/components/` se crean archivos con clases reutilizables usando las variables del tema:

```css
/* components/button.css */
.btn {
    background-color: var(--color-primary);
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    /* variantes: .btn-outline, .btn-ghost ... */
}
```

Se importan indicando la capa (`layer`) para que las utilidades (`mt-4`, etc.) puedan sobrescribirlos:

```css
@import "./components/button.css" layer(components);
@import "./components/form.css" layer(components);
```

Compilar en modo watch:

```bash
npm run dev
```

## Layout principal

`resources/views/components/layouts/idea.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Idea</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground">
    <x-layouts.nav />
    <main class="max-w-5xl mx-auto p-4">
        {{ $slot }}
    </main>
</body>
</html>
```

> Sin `@vite(...)` no se aplican los estilos (era el error de "no se ve el color de fondo").

## Componente de navegación

`resources/views/components/layouts/nav.blade.php` con logo + links, usando `flex items-center justify-between` para separar logo (izquierda) y links (derecha):

```blade
<nav class="border-b h-16 max-w-5xl mx-auto flex items-center justify-between">
    <a href="/"><img src="/images/logo.png" width="100" alt="Idea logo"></a>

    <div class="flex items-center gap-5">
        @auth
            <form method="POST" action="/logout">@csrf<button>Log out</button></form>
        @else
            <a href="/login">Sign in</a>
            <a href="/register" class="btn">Register</a>
        @endauth
    </div>
</nav>
```

## Componentes Blade reutilizables para formularios

**Formulario centrado** (`components/form/form.blade.php`) con slots `title` y `description`:

```blade
@props(['title', 'description'])
<div class="min-h-[80vh] flex flex-col justify-center max-w-md mx-auto">
    <h1 class="text-2xl font-bold">{{ $title }}</h1>
    <p class="text-sm">{{ $description }}</p>
    {{ $slot }}
</div>
```

**Campo de formulario** (`components/form/field.blade.php`) con label, name, type (default text) y merge de atributos:

```blade
@props(['label', 'name', 'type' => 'text'])
<div>
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
           value="{{ old($name) }}" {{ $attributes }} />
    @error($name) <p class="error">{{ $message }}</p> @enderror
</div>
```

> `old($name)` recuerda lo que el usuario escribió si la validación falla.

## Página de registro

```blade
<x-layouts.idea>
    <x-form title="Register an account" description="Start tracking your ideas today">
        <form method="POST" action="/register" class="space-y-4">
            @csrf
            <x-form.field label="Name" name="name" />
            <x-form.field label="Email" name="email" type="email" />
            <x-form.field label="Password" name="password" type="password" />
            <button type="submit" class="btn w-full">Create account</button>
        </form>
    </x-form>
</x-layouts.idea>
```

## Rutas y controladores de auth

```php
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::get('/login', [SessionController::class, 'create'])->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth');
```

**Registro** (`RegisteredUserController@store`):

```php
$attributes = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
    'password' => ['required', 'min:6'],
]);

$user = User::create($attributes); // el password se hashea solo (cast 'hashed' en Laravel 12)
Auth::login($user);

return redirect('/')->with('success', 'Registration complete');
```

> En Laravel 12 el modelo `User` ya castea `password` a `hashed`, así que **no** hay que llamar `bcrypt()` manualmente.

**Login** (`SessionController@store`):

```php
$attributes = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
]);

if (! Auth::attempt($attributes)) {
    return back()
        ->withInput()
        ->withErrors(['password' => 'No pudimos autenticar con esas credenciales.']);
}

request()->session()->regenerate(); // buena práctica de seguridad
return redirect()->intended('/')->with('success', 'You are now logged in');
```

![UI de registro/login](Images-entregable02/Tailwind%204.1%20Vista.png)

---
---