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

