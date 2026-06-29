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

## Usar Gates en Blade

```blade
@can('view-admin')
    <a href="/admin">Admin</a>
@endcan

@cannot('view-admin')
    <p>No tienes acceso</p>
@endcannot
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