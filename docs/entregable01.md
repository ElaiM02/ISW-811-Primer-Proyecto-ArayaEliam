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