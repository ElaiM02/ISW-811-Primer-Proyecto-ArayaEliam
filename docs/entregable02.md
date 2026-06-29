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
# Bundling de Assets con Vite en Laravel

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