# Estudiante: Edgar Eliam Araya Alvarado

# Aplicaciones Web Utilizando Software Libre

# Proyeccto-1 Laravel From Scratch 2026

# Entregable #1

# Fecha 22/06/2026

# Misael Matamoros Soto

# Routing 101

## ¿Qué es una ruta en Laravel?

En Laravel, una **ruta** define qué debe ocurrir cuando un usuario visita una URL específica de la aplicación.

La sintaxis de una ruta es fácil de interpretar porque se parece al lenguaje natural:

```php
Route::get('/', function () {
    return view('welcome');
});
```

Significa:

- **Route** → Define una ruta dentro de la aplicación.
- **get()** → Indica el método HTTP que escuchará la ruta.
- **/** → Representa la URL que visitará el usuario.
- **function()** → Código que se ejecuta cuando se accede a la ruta.
- **view()** → Carga una vista que será mostrada al usuario.

![web.php](Images-entregable01/Routing%20101%201.1%20web.png)

## Vistas en Laravel

Las **vistas** son las páginas que el usuario observa en el navegador.

![Pagina Laravel](Images-entregable01/Routing%20101%201.2%20PaginaWeb.png)

Normalmente se encuentran en:

```text
resources/views
```

Ejemplo:

```text
resources/views/welcome.blade.php
```

![welcome.blade.php](Images-entregable01/Routing%20101%201.3%20resources.png)

Laravel usa **Blade** como motor de plantillas para crear páginas HTML dinámicas.

Una vista puede contener:

- HTML
- CSS
- JavaScript
- Código Blade


## Editar una vista

Las vistas se pueden modificar directamente.

Eliminamos todo el contenido y agregamos de ejemplo:

```html
<h1>Hello World</h1>
```

![editar welcome.blade.php](Images-entregable01/Routing%20101%201.4%20editar%20pagina.png)

Al guardar los cambios y actualizar el navegador, Laravel muestra automáticamente la nueva versión.

![vista editada de welcome.blade.php](Images-entregable01/Routing%20101%201.5%20vista%20pagina%20editada.png)

## Crear enlaces entre páginas

Para navegar entre páginas se utiliza HTML:

```html
<a href="/about">About Us</a>
```

Si la ruta no existe, Laravel muestra un error:

![error 404](Images-entregable01/Routing%20101%201.6%20enlace%20de%20pagina.png)

Esto ocurre porque la URL no está registrada.


## Crear una nueva ruta

Para crear una página **About**:

```php
Route::get('/about', function () {
    return view('about');
});
```

Laravel buscará:

```text
resources/views/about.blade.php
```
![vista pagina About](Images-entregable01/Routing%20101%201.7%20vista%20about%20error.png)

## Retornar texto directamente

Una ruta también puede devolver texto:

```php
Route::get('/about', function () {
    return "About us";
});
```

Aunque funciona, normalmente se recomienda utilizar vistas para páginas completas.


## Crear una nueva vista

Si la vista no existe aparecerá:

```text
View [about] not found
```

Se debe crear el archivo:

```text
resources/views/about.blade.php
```

Ejemplo:

```html
<h1>About Us</h1>

<a href="/">Return Home</a>
```
![vista About](Images-entregable01/Routing%20101%201.8%20nueva%20vista%20about.png)

## Ejercicio: Crear ruta Contact

Se debe crear una ruta que responda a:

```text
GET /contact
```

Código:

```php
Route::get('/contact', function () {
    return view('contact');
});
```

Después crear:

```text
resources/views/contact.blade.php
```

Ejemplo:

```html
<h1>Contact</h1>

<p>Email: example@test.com</p>
```

![vnueava vista contact](Images-entregable01/Routing%20101%201.9%20Tarea%20contact.png)

---

---

# Layout Files

## Atajo `Route::view()`

Cuando una ruta solo carga una vista, Laravel permite usar:

```php
Route::view('/contact', 'contact');
```

En lugar de:

```php
Route::get('/contact', function () {
    return view('contact');
});
```

Es útil para páginas estáticas.

![atajo routes](Images-entregable01/Layouts%20File%201.1%20Atajo%20routes.png)

## Problema: Código repetido

Al crear varias páginas aparecen elementos repetidos:

- Menú
- HTML principal
- CSS
- JavaScript

Para evitarlo se utilizan layouts y componentes Blade.

## Crear un Layout

Se crea un componente reutilizable:
resources/views/components/layout.blade.php

Aquí se coloca el código común:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    {{ $slot }}
</body>
</html>
```

## Uso de Slot

`{{ $slot }}` representa el contenido específico de cada página.

Ejemplo:

```html
<x-layout>
    <h1>Welcome to Laravel</h1>

    <nav>
        <a href="about">About Us</a>
        <a href="contact">Contact Us</a>
    </nav>
</x-layout>
```

El contenido se inserta dentro del layout.

![Mustra de layout](Images-entregable01/Layouts%20File%201.2%20muestra%20de%20layout.png)

## Props en componentes(Titulos)

Los componentes pueden recibir datos.

Queremos que nuestro titulos sean diferente segun la vista que ingresmos.


En el archivo layout.blade.php combiamos el titulo por lo siguient:
```html
<title>{{ $title }}</title>
```

En cada vista se debe de colocar el titulo de la siguiente forma:
```html
<x-layout title="About Us">
```

En el componente se define:

```php
@props(['title'])
```

Esto permite crear valores dinámicos como títulos de página. También se pueden definir valores por defecto:

```php
@props([
    'title' => 'Laracasts'
])
```
![Props Tile](Images-entregable01/Layouts%20File%201.3%20Tittles.png)
## Attributes vs Props

**Props** — Son datos enviados al componente:
title="Home"

**Attributes** — Son atributos HTML:
class=""

id=""

Laravel los maneja de forma diferente.

## Componentes reutilizables

Se pueden crear componentes como tarjetas:
components/card.blade.php

Ejemplo:

```html
<div class="card" styles: >
{{ $slot }}
</div>
```

Uso:

```html
<x-card>
Contenido
</x-card>
```

![Cards](Images-entregable01/Layouts%20File%201.4%20cards.png)
---
---

# Pass Data to Views

## Métodos para pasar datos

En web.php
**Con `Route::view()`** — tercer parámetro como arreglo:
```php
Route::view('/welcome', 'welcome', ['greeting' => 'Hello, welcome to our website!', 'person' => 'Eliam!']);
```

En la vista de la pagina welcome:

```html
<x-layout>
    {{ $greeting }}, {{ $person }}!
</x-layout>
```

![Metodo pasar datos](Images-entregable01/Pass%20Data%201.1%20metodos%20para%20pasar%20datos.png)

**Con `Route::get()`** — segundo argumento de `view()`:
```php
Route::get('/', function () {
    return view('welcome', ['person' => request('person')]);
});
```

## Query String

Leer parámetros de la URL con `request()`:
```php
request('person') // lee ?person=Frank
```
![query string](Images-entregable01/Pass%20data%201.2%20query%20string.png)
## Valores por defecto

```php
request('person', 'World') // si no hay valor, usa 'World'
```

## Escapado y seguridad XSS

| Sintaxis | Escapa HTML | Uso recomendado |
|---|---|---|
| `{{ $var }}` | Sí | Datos del usuario |
| `{!! $var !!}` | No | Solo datos de confianza |

## Otro metodo 

```php
Route::get('/', function () {
    return view('welcome', [
        'greeting' => 'Hello, welcome to our website!',
        'person' => request('person')
    ]);
});
```

---
---

# Blade Directives

## Pasar arreglos a las vistas

Se puede enviar un arreglo de datos a una vista así:

```php
Route::get('/', function () {
    return view('welcome', [
        'tasks' => ['Go to the market', 'Walk the dog', 'Watch a video tutorial']
    ]);
});
```

>No se puede hacer `{{ $tasks }}` directamente si es un arreglo — causará un error.

![Error de arreglo](Images-entregable01/Blade%20Directives%201.1%20Error%20de%20arreglo.png)

## Depuración con `dump` y `dd`
Puedes utilizar `var_dump(tasks)` o `die(var_dump(tasks))`

![EDepuracion con dumb o dd](Images-entregable01/Blade%20Directives%201.2%20Utilizando%20dumb.png)

En lugar de `var_dump()` o `die(var_dump())`, Blade ofrece directivas más limpias:

```blade
@dump($tasks)   {{-- muestra el contenido sin detener la ejecución --}}
@dd($tasks)     {{-- muestra el contenido y detiene la ejecución --}}
```
![EDepuracion con @dumb o @dd](Images-entregable01/Blade%20Directives%201.3%20Using%20Dump.png)

## Condicionales

```php
    <?php if (count($tasks)): ?>
        <p> Yes we have some tasks. How many Eliam? <?= count($tasks) ?> tasks, in fact.</p>
    <?php endif; ?>
```

Si lo cambiamos por un formato blade deberia de verse el mismo resultado:

```blade
@if (count($tasks))
    <p> Yes we have some tasks. How many Eliam? <?= count($tasks) ?> tasks, in fact.</p>
@endif
```
![Count](Images-entregable01/Blade%20Directives%201.4%20Count.png)

También existe `@unless` como equivalente a `if not`:

```blade
@unless(count($tasks))
    No hay tareas activas.
@endunless
```

## Bucles con `@foreach`

```blade
@foreach($tasks as $task)
    <li>{{ $task }}</li>
@endforeach
```
![Blade foreach](Images-entregable01/Blade%20Directives%201.5%20Foreach.png)

## Manejo de arreglos vacíos con `@forelse`

Cuando el arreglo puede estar vacío, `@forelse` combina el bucle con un caso alternativo:

```blade
@forelse($tasks as $task)
    <li>{{ $task }}</li>
@empty
    <p>There are no active tasks.</p>
@endforelse
```
![Forelse and empty](Images-entregable01/Blade%20Directives%201.6%20empty,%20forelse.png)

## Directivas de autenticación y autorización

Blade también incluye directivas para controlar acceso según el estado del usuario:

```blade
@auth
    {{-- usuario autenticado --}}
@endauth

@guest
    {{-- visitante sin sesión iniciada --}}
@endguest

@can('editar', $post)
    <a href="/posts/edit">Editar</a>
@endcan
```

---
---

# Forms

## Crear la vista y el formulario

Se crea una vista `ideas.blade.php` con un formulario básico usando Tailwind CSS (https://tailwindcss.com/). El formulario debe tener `method="POST"` y un `action` que apunte al endpoint donde se enviará la data:

utilizaremos este codigo de ejemplo que esta en la pagina:
![tailwindcss](Images-entregable01/Forms%201.1%20pagina%20tailwindcss.png)

![tailwindcss](Images-entregable01/forms%201.2%20vista%20del%20codigo.png)
```html
<form action="">
        <div class="col-span-full">
          <label for="idea" class="block text-sm/6 font-medium text-white">New idea</label>
          <div class="mt-2">
            <textarea id="idea" name="idea" rows="3" class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"></textarea>
          </div>
          <p class="mt-3 text-sm/6 text-gray-400">Have an idea to share?</p>
        </div>

        <div class="mt-6 flex items-center gap-x-6">
            <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
        </div>
    </form>
```

Agregamos esta liena en layout.blade.php:

```html
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
```
> Para estilos rápidos en demos se puede usar Tailwind desde un CDN, aunque en producción se recomienda instalarlo formalmente.
![Formulario con tailwind](Images-entregable01/forms%201.3%20utilizando%20tailwind.png)

## Registrar las rutas

Se necesitan **dos rutas** separadas: una para mostrar el formulario y otra para recibir los datos:

```php
// Muestra el formulario
Route::get('/', function () {
    return view('ideas');
});

// Recibe y procesa el formulario
Route::post('/ideas', function () {
    dd('Hello');
});
```

## Protección CSRF

Al enviar un formulario POST, Laravel lanza un error **419** si no se incluye el token CSRF. La solución es agregar la directiva `@csrf` dentro del formulario:

```blade
<form method="POST" action="/ideas">
    @csrf
    ...
</form>
```

> El token se genera en el servidor y se compara al recibir el formulario. Esto protege contra ataques de **Cross-Site Request Forgery**, donde un sitio externo podría enviar solicitudes maliciosas en nombre del usuario.

![Token](Images-entregable01/forms%201.4%20token.png)

## Leer datos del Request

Hay varias formas de acceder a los datos enviados:

```php
// Función helper (la más simple)
request('idea')

// Facade de Laravel
Request::get('idea')

// Inyección de dependencia
function (Illuminate\Http\Request $request) {
    $request->get('idea');
}
```
![Request](Images-entregable01/forms%201.5%20request.png)


## Guardar datos en Session

Como alternativa temporal a una base de datos, se puede usar la sesión del usuario:

```php
// Agregar un item al arreglo 'ideas' en sesión
session()->push('ideas', $idea);

// Leer las ideas guardadas (con arreglo vacío como valor por defecto)
session('ideas', []);

// Eliminar las ideas de la sesión
session()->forget('ideas');
```

![Datos de Session](Images-entregable01/forms%201.6%20datos%20de%20session.png)

> La sesión funciona como un bloc de notas único por usuario. Es temporal y no persiste entre navegadores distintos. Para persistencia real se debe usar una base de datos.

## Mostrar las ideas guardadas

En la vista se recorre el arreglo con `@forelse` para manejar el caso vacío:

```blade
@if(count($ideas))
    <ul>
        @foreach($ideas as $idea)
            <li>{{ $idea }}</li>
        @endforeach
    </ul>
@endif
```

![Ideas guardadas](Images-entregable01/forms%201.7%20ideas%20guardadas.png)

---
---

# Databases, Migrations, and Eloquent

## Configuración de la base de datos

Laravel soporta múltiples drivers de base de datos (MySQL, PostgreSQL, SQLite, entre otros). Por defecto usa **SQLite**, que es una base de datos basada en archivos. La configuración se define en el archivo `.env`:

```
DB_CONNECTION=sqlite
```

> El archivo `.env` almacena la configuración del entorno local. En producción se usa un archivo `.env` distinto con configuraciones diferentes.

## Migraciones

Las migraciones son **control de versiones para la base de datos**. Permiten definir tablas y columnas de forma programática y compartirlas con el equipo.

Crear una migración:

```bash
php artisan make:migration create_ideas_table
```
![Tabla_ideas](Images-entregable01/database%201.1%20crear%20tabla%20ideas.png)
Estructura básica de una migración:

```php
public function up(): void
{
    Schema::create('ideas', function (Blueprint $table) {
        $table->id();
        $table->text('description');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('ideas');
}
```

Ejecutar las migraciones:

```bash
php artisan migrate
```

### Opciones para modificar una tabla existente

**Opción 1** — Durante desarrollo, modificar la migración original y refrescar:

```bash
php artisan migrate:refresh  # borra todos los datos y recrea las tablas
```

**Opción 2** — En producción o trabajo en equipo, crear una nueva migración:

```bash
php artisan make:migration add_state_to_ideas_table
```

## Consultas con el Facade DB

Forma genérica de consultar cualquier tabla:

```php
use Illuminate\Support\Facades\DB;

// Obtener todos los registros
$ideas = DB::table('ideas')->get();

// Con condición
$ideas = DB::table('ideas')->where('state', 'pending')->get();
```

Los resultados se retornan como una **colección de objetos**, no como arreglos:

```php
$idea->description  // acceder a una propiedad
```

![muestra de datos](Images-entregable01/database%201.2%20datos%20desde%20base%20de%20datos.png)
## Eloquent ORM

Eloquent es el **ORM de Laravel**. Permite crear una clase que representa una tabla de la base de datos.

Crear un modelo:

```bash
php artisan make:model Idea
```

Esto genera `app/Models/Idea.php`:

![crear model Idea](Images-entregable01/database%201.3%20crear%20model.png)

```php
class Idea extends Model
{
    protected $guarded = [];  // deshabilitar protección para mayor control manual
}
```

### Operaciones básicas con Eloquent

```php
use App\Models\Idea;

// Obtener todos
$ideas = Idea::all();

// Buscar por ID
$idea = Idea::find(1);

// Con condición
$ideas = Idea::where('state', 'pending')->get();

// Crear un nuevo registro
Idea::create([
    'description' => request('idea'),
    'state' => 'pending',
]);
```

> Eloquent asigna automáticamente los campos `created_at` y `updated_at` al crear o actualizar registros.

## Filtrado dinámico con Query String

Se puede filtrar resultados según parámetros de la URL (`?state=pending`):

```php
$ideas = Idea::when(request('state'), function ($query, $state) {
    $query->where('state', $state);
})->get();
```

Ejemplos de uso en la URL:

```
/?state=pending    → solo ideas pendientes
/?state=completed  → solo ideas completadas
```

## Flujo completo con Eloquent

```php
// Mostrar ideas
Route::get('/', function () {
    $ideas = Idea::when(request('state'), function ($query, $state) {
        $query->where('state', $state);
    })->get();

    return view('ideas', ['ideas' => $ideas]);
});

// Guardar nueva idea
Route::post('/ideas', function () {
    Idea::create([
        'description' => request('idea'),
        'state' => 'pending',
    ]);

    return redirect('/');
});
```

---
---

# HTTP Requests y REST en Laravel

## Métodos HTTP → CRUD

| Método | Acción | Uso |
|---|---|---|
| `GET` | index / show | Leer recursos |
| `POST` | store | Crear recurso |
| `PATCH` | update | Actualizar recurso |
| `DELETE` | destroy | Eliminar recurso |

## Rutas del recurso

```php
Route::get('/ideas', fn() => view('ideas.index', ['ideas' => Idea::all()]));
Route::get('/ideas/{idea}', fn(Idea $idea) => view('ideas.show', compact('idea')));
Route::get('/ideas/{idea}/edit', fn(Idea $idea) => view('ideas.edit', compact('idea')));
Route::post('/ideas', fn() => [Idea::create(['description' => request('description')]), redirect('/ideas')]);
Route::patch('/ideas/{idea}', fn(Idea $idea) => [$idea->update(['description' => request('description')]), redirect("/ideas/$idea->id")]);
Route::delete('/ideas/{idea}', fn(Idea $idea) => [$idea->delete(), redirect('/ideas')]);
```

![Ruta ejemplo 1](Images-entregable01/REST%201.1%20Ejemplo%20de%20ruta%201.png)

![Ruta show](Images-entregable01/REST%201.2%20ejemplo%20show.png)
## Route Model Binding

Laravel resuelve el modelo automáticamente, el nombre del parámetro debe coincidir con el wildcard:

```php
// Laravel busca el registro y retorna 404 si no existe
//Show
Route::get('/ideas/{id}', function ($id) {
    $idea = Idea::find($id);

    if (is_null($idea)) {
        abort(404);
    }

    return view('ideas.show', [
        'idea' => $idea
    ]);
});
```

![Model binding](Images-entregable01/rest%201.3%20route%20model%20binding.png)

## Method Spoofing

Los navegadores solo soportan `GET` y `POST`, por eso se usa `@method`:

```blade
<form method="POST" action="/ideas/{{ $idea->id }}">
    @csrf
    @method('PATCH')  {{-- o DELETE --}}
    ...
</form>
```

El botón de eliminar debe estar en un **formulario separado** vinculado con el atributo `form="id-del-form"`.

---
---

# Controllers

## Las 7 acciones RESTful

Todo recurso en Laravel tiene 7 acciones estándar:

| Acción | Método | URI | Descripción |
|---|---|---|---|
| `index` | GET | `/ideas` | Lista todos |
| `create` | GET | `/ideas/create` | Muestra formulario de creación |
| `store` | POST | `/ideas` | Guarda nuevo registro |
| `show` | GET | `/ideas/{idea}` | Muestra uno específico |
| `edit` | GET | `/ideas/{idea}/edit` | Muestra formulario de edición |
| `update` | PATCH | `/ideas/{idea}` | Actualiza un registro |
| `destroy` | DELETE | `/ideas/{idea}` | Elimina un registro |

## Crear un controlador de recurso

```bash
php artisan make:controller IdeaController --resource --model=Idea
```

Esto genera automáticamente las 7 acciones con Route Model Binding incluido en `app/Http/Controllers/IdeaController.php`.

![Crear contreoller](Images-entregable01/controllers%201.1%20crear%20IdeaController.png)

## Conectar rutas al controlador

```php
use App\Http\Controllers\IdeaController;

Route::get('/ideas', [IdeaController::class, 'index']);
Route::get('/ideas/create', [IdeaController::class, 'create']);
Route::post('/ideas', [IdeaController::class, 'store']);
Route::get('/ideas/{idea}', [IdeaController::class, 'show']);
Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit']);
Route::patch('/ideas/{idea}', [IdeaController::class, 'update']);
Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy']);
```

## Regla de oro

Intenta siempre usar solo estas 7 acciones. Si necesitas algo diferente, crea un **nuevo controlador** en lugar de inventar nombres de acciones nuevas.

---
---

# Request Validation

## Por qué validar

Sin validación, datos inválidos llegan hasta la base de datos y generan errores SQL. La validación debe ocurrir **antes** de persistir cualquier dato.

## Validación en el controlador

```php
public function store(Request $request)
{
    $request->validate([
        'description' => ['required', 'min:10'],
    ]);

    Idea::create(['description' => $request->description]);
    return redirect('/ideas');
}
```
Si la validación falla, Laravel redirige automáticamente de vuelta al formulario.

## Reglas más comunes

| Regla | Descripción |
|---|---|
| `required` | Campo obligatorio |
| `min:10` | Mínimo de caracteres |
| `max:255` | Máximo de caracteres |
| `email` | Debe ser un email válido |
| `confirmed` | Debe coincidir con otro campo |

Lista completa en `laravel.com/docs/validation`

## Mostrar errores en la vista

**Forma larga:**
```blade
@if($errors->has('description'))
    <p>{{ $errors->first('description') }}</p>
@endif
```

**Forma corta con directiva Blade:**
```blade
@error('description')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
```

## Componente reutilizable de error

Crear `components/form/error.blade.php`:

```blade
@props(['name'])

@error($name)
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
```

Uso en la vista:

```blade
<x-form.error name="description" />
```

Los errores de validación se guardan en sesión **flash** — solo están disponibles para la siguiente request y luego se borran automáticamente.

---
---

# Clases Form Request en Laravel

## ¿Qué es un Form Request?

Es una clase dedicada para manejar la validación **fuera del controlador**. No es mejor ni peor que validar en el controlador, es solo una preferencia personal.

## Crear un Form Request

```bash
php artisan make:request StoreIdeaRequest
```

Esto genera `app/Http/Requests/StoreIdeaRequest.php`:

![Crear form request](Images-entregable01/Class%20request%201.1%20crear%20form%20request.png)


```php
class StoreIdeaRequest extends FormRequest
{
    // ¿Está autorizado el usuario?
    public function authorize(): bool
    {
        return true; // false = error 403
    }

    // Reglas de validación
    public function rules(): array
    {
        return [
            'description' => ['required', 'min:10'],
        ];
    }

    // Mensajes personalizados (opcional)
    public function messages(): array
    {
        return [
            'description.required' => '¡Escribe algo!',
            'description.min' => 'Dame algo para :attribute',
        ];
    }
}
```

## Form Request Classes

Solo se sustituye `Request` por la clase creada — la validación ocurre **automáticamente**:

```php
// Antes
public function store(Request $request) {
    $request->validate([...]);
}

// Después
public function store(StoreIdeaRequest $request) {
    // La validación ya ocurrió automáticamente
    Idea::create(['description' => $request->input('description')]);
    return redirect('/ideas');
}
```

## ¿Un Form Request o dos?

| Situación | Solución |
|---|---|
| Las reglas de store y update son **iguales** | Compartir un solo `IdeaRequest` |
| Las reglas son **diferentes** | Crear `StoreIdeaRequest` y `UpdateIdeaRequest` por separado |

```php
// Compartiendo el mismo Form Request
public function store(IdeaRequest $request) { ... }
public function update(IdeaRequest $request, Idea $idea) { ... }
```

---
---

# A Brief DaisyUI Detour

## ¿Qué es DaisyUI?

Es una librería de componentes para Tailwind CSS, similar a Bootstrap pero para Tailwind. Permite construir rápidamente navbars, cards, botones, formularios y más.

![Pagina DaisyUI](Images-entregable01/Daisyui%201.1%20pagina%20daisy%20ui.png)

## Instalación vía CDN

En el archivo `layout.blade.php` agregar:

```html
<head>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
</head>
```

## Temas

Se define en la etiqueta `<html>`:

```html
<html data-theme="dark">
<html data-theme="dracula">
<html data-theme="coffee">
<html data-theme="business"> 
```
![Navbard DaisyUI](Images-entregable01/daisyui%201.2%20temas%20y%20navbard.png)

## Componentes más usados

**Botón:**
```html
<button class="btn btn-primary">Guardar</button>
<button class="btn btn-warning">Editar</button>
<button class="btn btn-error">Eliminar</button>
```

**Textarea:**
```html
<textarea class="textarea w-full"></textarea>
```

**Card:**
```html
<div class="card bg-neutral">
    <div class="card-body">
        Contenido
    </div>
</div>
```

## Componente reutilizable de card

Crear `components/idea-card.blade.php`:

```blade
<a href="{{ $attributes->get('href') }}" class="card bg-neutral">
    <div class="card-body">
        {{ $slot }}
    </div>
</a>
```

Uso en la vista:

```blade
@foreach($ideas as $idea)
    <x-idea-card href="/ideas/{{ $idea->id }}">
        {{ $idea->description }}
    </x-idea-card>
@endforeach
```

![Diseño de pagina](Images-entregable01/daisyui%201.3%20diseño%20de%20pagina.png)
---
---

# II. Authentication and Authorization, primera parte

# Authentication Explained

## Flujo completo de autenticación

```
Registro  → Validar → Crear usuario → Login automático → Redirigir
Login     → Validar → Intentar login → Redirigir (éxito/fallo)
Logout    → Cerrar sesión → Redirigir
```

## Rutas necesarias

```php
// Registro
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);

// Login / Logout
Route::get('/login', [SessionsController::class, 'create']);
Route::post('/login', [SessionsController::class, 'store']);
Route::delete('/logout', [SessionsController::class, 'destroy']);
```

## Controlador de Registro

```bash
php artisan make:controller Auth/RegisteredUserController
```
![Crear register controller](Images-entregable01/Authentication%202.1%20Crear%20Register%20Controller.png)


```php
public function create()
{
    return view('auth.register');
}

public function store(Request $request)
{
    // 1. Validar
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'unique:users'],
        'password' => ['required', 'min:8'],
    ]);

    // 2. Crear usuario (contraseña hasheada)
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // 3. Login automático
    Auth::login($user);

    // 4. Redirigir
    return redirect('/ideas');
}
```

![Nuevo Usuario](Images-entregable01/Authentication%202.2%20New%20User.png)

## Controlador de Sesiones (Login/Logout)

```bash
php artisan make:controller Auth/SessionsController --resource
```

![Session Controller](Images-entregable01/Authentication%202.3%20Craer%20SessionController.png)
```php
// Mostrar formulario de login
public function create()
{
    return view('auth.login');
}

// Intentar login
public function store(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'min:8'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect('/ideas');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

// Cerrar sesión
public function destroy()
{
    Auth::logout();
    return redirect('/ideas');
}
```

![Login](Images-entregable01/Authentication%202.4%20Login.png)


## Formulario de logout

```blade
<form method="POST" action="/logout">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost">Logout</button>
</form>
```

## Directivas Blade para autenticación

```blade
@auth
    {{-- Solo usuarios autenticados --}}
    <form method="POST" action="/logout">...</form>
@else
    {{-- Solo visitantes --}}
    <a href="/register">Register</a>
    <a href="/login">Login</a>
@endauth

@guest
    {{-- Alternativa para visitantes --}}
@endguest
```

**Nunca** almacenar contraseñas en texto plano. Siempre usar `Hash::make()`.

---
---

# Require Authentication With Middleware

## Agregar relación user_id a ideas

En la migración de ideas agregar la relación con cascada:

```php
$table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
```

`cascadeOnDelete()` elimina automáticamente las ideas del usuario cuando este es eliminado.

Luego refrescar las migraciones:

```bash
php artisan migrate:fresh
```

## Proteger rutas con Middleware

```php
// Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index']);
    Route::get('/ideas/create', [IdeaController::class, 'create']);
    Route::get('/ideas/{idea}', [IdeaController::class, 'show']);
    Route::post('/ideas', [IdeaController::class, 'store']);
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit']);
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update']);
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy']);
    Route::delete('/logout', [SessionsController::class, 'destroy']);
});

// Rutas solo para visitantes (no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/login', [SessionsController::class, 'store']);
});
```

## Configurar redirecciones en bootstrap/app.php

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectGuestTo('/login');
    $middleware->redirectUsersTo('/ideas');
})
```

## Asignar idea al usuario autenticado

En `IdeaController` al crear una idea:

```php
public function store(Request $request)
{
    $request->validate([
        'description' => ['required', 'min:10'],
    ]);

    Idea::create([
        'description' => $request->input('description'),
        'status'      => 'pending',
        'user_id'     => Auth::id(), // usuario autenticado
    ]);

    return redirect('/ideas');
}
```

## Filtrar ideas por usuario autenticado

```php
public function index()
{
    $ideas = Idea::where('user_id', Auth::id())->get();
    return view('ideas.index', ['ideas' => $ideas]);
}
```

## Crear usuario de prueba con Tinker

```bash
php artisan tinker
```

![Tinker User](Images-entregable01/Auth%20Middleware%202.1%20Usuario%20con%20tnker.png)

```php
User::factory()->create(); // crea un usuario con datos falsos
```

---
---