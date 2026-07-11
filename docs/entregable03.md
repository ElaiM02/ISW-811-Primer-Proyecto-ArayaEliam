# Estudiante: Edgar Eliam Araya Alvarado

# Aplicaciones Web Utilizando Software Libre

# Proyeccto-1 Laravel From Scratch 2026

# Entregable #3

# Fecha 13/07/2026

# Misael Matamoros Soto

# Final Project: Build and Deploy an App, segunda parte

Este entregable finaliza el Proyecto 1: página de detalle de una idea, modales con AlpineJS,
formularios avanzados, pruebas, carga de archivos, clases de acción, autorización, edición de
perfil y documentación del despliegue (episodios 30 al 43).

---
---

# Show A Single Idea (Mostrar una idea individual)

Se construye la página de detalle de una idea: título, estado, fecha, descripción, enlaces y una barra de navegación con acciones (volver, editar, eliminar).

## Acción show en el controlador

```php
public function show(Idea $idea)
{
    return view('ideas.show', ['idea' => $idea]);
}
```

- Se usa **route model binding**: Laravel inyecta la `Idea` automáticamente a partir del `{idea}` de la URL.

## Vista de detalle

`resources/views/ideas/show.blade.php`:

```blade
<x-layout>
    <div class="p-4 max-w-3xl mx-auto">

        {{-- Barra de navegación con acciones --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('idea.index') }}" class="flex gap-1 text-sm font-bold">
                <x-icons.arrow-back /> Back to ideas
            </a>

            <div class="flex items-center gap-2">
                <a href="#" class="btn btn-outline">Edit idea</a>

                <form method="POST" action="{{ route('idea.destroy', $idea) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline text-red-500">Delete</button>
                </form>
            </div>
        </div>

        {{-- Título, estado y fecha --}}
        <h1 class="text-2xl font-bold mt-8">{{ $idea->title }}</h1>

        <div class="flex gap-2 items-center mt-2">
            <x-idea.status-label :status="$idea->status" />
            <span class="text-muted-foreground text-sm">
                {{ $idea->updated_at->diffForHumans() }}
            </span>
        </div>

        {{-- Descripción --}}
        <div class="card mt-6 max-w-none">
            {{ $idea->description }}
        </div>

        {{-- Enlaces relacionados --}}
        @if ($idea->links->count())
            <div class="mt-8">
                <h3 class="font-bold">Links</h3>
                <div class="space-y-2 mt-2">
                    @foreach ($idea->links as $link)
                        <x-card href="{{ $link }}" class="text-primary font-bold flex gap-1">
                            {{ $link }} <x-icons.external />
                        </x-card>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layout>
```

## Iconos como componentes SVG

En vez de imágenes, los iconos se guardan como componentes Blade en `resources/views/components/icons/` (ej. `arrow-back.blade.php`, `external.blade.php`). Al ser SVG se pueden estilizar (color, hover, tamaño) desde las clases:

```blade
{{-- components/icons/arrow-back.blade.php --}}
<svg {{ $attributes }} xmlns="http://www.w3.org/2000/svg" fill="none"
     viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
</svg>
```

## Eliminar una idea

La ruta de borrado (ya existente) responde a `DELETE`:

```php
Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('idea.destroy');
```

```php
public function destroy(Idea $idea)
{
    // Gate::authorize('delete', $idea); // se agrega en el capítulo de autorización
    $idea->delete();

    return redirect()->route('idea.index');
}
```

- El formulario usa `@csrf` y `@method('DELETE')` porque HTML solo soporta GET/POST.
- La autorización real se agrega en el episodio "Authorization Is A Requirement".

![Vista individual de una idea](Images-entregable03/Show%20Idea%204.1%20show%20idea.png)

---
---

# Create A Functional Modal With AlpineJS (Modal funcional con AlpineJS)

Se construye un modal reutilizable con AlpineJS que se abre al hacer clic en un botón, mediante **eventos del navegador** (dispatch/listen). Se abrirá al presionar un botón "What's the idea?" y se cerrará al hacer clic fuera o presionar Escape.

## Botón que abre el modal

En `index.blade.php`, se usa el componente `card` pero renderizado como `<button>` (no como enlace) mediante un prop `is`:

```blade
<x-card is="button" x-data
        @click="$dispatch('open-modal', 'create-idea')"
        class="w-full text-left mt-10 cursor-pointer h-24">
    <p>What's the idea?</p>
</x-card>
```

- `$dispatch('open-modal', 'create-idea')` → **emite** un evento de navegador llamado `open-modal` con el nombre del modal como detalle.

### Card polimórfico (prop `is`)

Para que el card pueda ser `<a>` o `<button>`:

```blade
@props(['is' => 'a'])

<{{ $is }} {{ $attributes->merge(['class' => 'block border rounded-lg bg-card p-4 text-sm']) }}>
    {{ $slot }}
</{{ $is }}>
```

## Componente modal reutilizable

`resources/views/components/modal.blade.php`:

```blade
@props(['name', 'title'])

<div
    x-data="{ show: false }"
    x-on:open-modal.window="$event.detail === @js($name) ? show = true : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-transition.opacity.duration.300ms
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-{{ $name }}-title"
    tabindex="-1"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
>
    <x-card x-show="show" @click.away="show = false" class="max-w-lg w-full">
        <header>
            <h2 id="modal-{{ $name }}-title" class="text-lg font-bold">{{ $title }}</h2>
        </header>

        <div class="mt-4">
            {{ $slot }}
        </div>
    </x-card>
</div>
```

**Claves de Alpine:**

| Directiva | Función |
|---|---|
| `x-data="{ show: false }"` | Estado del modal (oculto por defecto) |
| `x-on:open-modal.window` | Escucha el evento `open-modal` a nivel `window` |
| `$event.detail === @js($name)` | Solo abre si el nombre del evento coincide con este modal |
| `x-on:keydown.escape.window` | Cierra con la tecla Escape |
| `@click.away="show = false"` | Cierra al hacer clic fuera del card |
| `x-show` + `x-transition` | Muestra/oculta con animación |
| `style="display: none;"` | Evita el "parpadeo" del modal antes de que Alpine cargue |
| `@js($name)` | Convierte la variable PHP a string JS de forma segura |

## Usar el modal en la vista

```blade
<x-modal name="create-idea" title="New idea">
    {{-- aquí irá el formulario (siguiente episodio) --}}
    <p>Contenido del formulario...</p>
</x-modal>
```

## Accesibilidad

Se agregan atributos ARIA para lectores de pantalla: `role="dialog"`, `aria-modal="true"`, `aria-labelledby` (apuntando al id único del título) y `tabindex="-1"`.

![Modal de creación](Images-entregable03/Modal%204.1%20Se%20crea%20un%20modal%20con%20AlpineJS.png)

---
---