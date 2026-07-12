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

# Construct The Idea Form (Construir el formulario de idea)

Dentro del modal se construye el formulario para crear una idea: título, descripción (textarea), selector de estado con botones (AlpineJS) y validación en el servidor.

## Endpoint y estructura del formulario

Ruta de guardado:

```php
Route::post('/ideas', [IdeaController::class, 'store'])->name('idea.store');
```

Formulario dentro del modal (`x-data` para el estado del selector):

```blade
<form method="POST" action="{{ route('idea.store') }}"
      x-data="{ status: 'pending' }" class="space-y-6">
    @csrf

    <x-form.field label="Title" name="title" placeholder="Enter a title for your idea" required />

    <x-form.field label="Description" name="description" type="textarea"
                  placeholder="Describe your idea" />

    {{-- Selector de estado --}}
    <div class="space-y-2">
        <label class="label">Status</label>
        <div class="flex gap-2">
            @foreach (\App\IdeaStatus::cases() as $status)
                <button type="button" @click="status = @js($status->value)"
                        :class="{ 'btn-outline': status !== @js($status->value) }"
                        class="btn flex-1 h-10">
                    {{ $status->label() }}
                </button>
            @endforeach
        </div>
        {{-- input oculto con el estado seleccionado --}}
        <input type="hidden" name="status" :value="status">
    </div>

    {{-- Botones --}}
    <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost"
                @click="$dispatch('close-modal')">Cancel</button>
        <button type="submit" class="btn">Create</button>
    </div>
</form>
```

**Claves del selector de estado con Alpine:**
- `x-data="{ status: 'pending' }"` → una sola fuente de verdad para el estado elegido.
- `@click="status = @js($status->value)"` → al hacer clic, guarda el valor.
- `:class="{ 'btn-outline': status !== ... }"` → el botón activo se ve "encendido" (sin outline).
- `<input type="hidden" name="status" :value="status">` → lo que realmente se envía al servidor.

## Soporte de textarea en el componente field

Se hace el componente `field` más configurable (label opcional, tipo textarea):

```blade
@props(['label' => null, 'name', 'type' => 'text'])

<div>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif

    @if ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}"
                  {{ $attributes->merge(['class' => 'textarea']) }}>{{ old($name) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
               value="{{ old($name) }}" {{ $attributes->merge(['class' => 'input']) }} />
    @endif

    <x-form.error :name="$name" />
</div>
```

Y el componente de error extraído (`components/form/error.blade.php`):

```blade
@props(['name'])

@error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
```

## Cerrar el modal al cancelar

El botón Cancel emite `close-modal`; el modal lo escucha:

```blade
{{-- en modal.blade.php --}}
@close-modal.window="show = false"
```

## Autorizar, validar y persistir

En el Form Request (`StoreIdeaRequest`), autorizar (por defecto viene en `false`):

```php
public function authorize(): bool
{
    return true; // la autorización real se ve en un episodio posterior
}

public function rules(): array
{
    return [
        'title' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'status' => ['required', Rule::enum(App\IdeaStatus::class)],
    ];
}
```

En el controlador:

```php
public function store(StoreIdeaRequest $request)
{
    auth()->user()->ideas()->create($request->validated());

    return redirect()->route('idea.index')->with('success', 'Idea created');
}
```

## Mostrar las más recientes primero

```php
// index()
$ideas = auth()->user()->ideas()->latest()->get();
```

![Formulario de creación en el modal](Images-entregable03/Form%20Modal%204.1%20Creacion%20de%20formulario%20modal.png)

---
---

# Test The Create Idea Form (Probar el formulario de creación)

Se automatiza la prueba del modal de creación: visitar la página, abrir el modal, llenar el formulario, enviarlo y verificar que la idea se guardó en la base de datos.

## Atributos data-test para seleccionar elementos

Para no depender de textos (frágiles), se marcan los elementos con `data-test`:

```blade
{{-- botón que abre el modal --}}
<x-card is="button" data-test="create-idea-button" ...>

{{-- botones de estado --}}
<button type="button" data-test="button-status-{{ $status->value }}" ...>
```

## Test de navegador (referencia del video)

`tests/Browser/CreateIdeaTest.php`:

```php
it('creates a new idea', function () {
    $user = User::factory()->create();

    $page = visit('/ideas')->actingAs($user);

    $page->click('@create-idea-button')      // abre el modal
        ->fill('title', 'Some example title')
        ->click('@button-status-completed')  // elige el estado
        ->fill('description', 'An example description')
        ->click('Create');                   // envía

    $page->assertPath('/ideas');

    expect($user->ideas()->first())->toMatchArray([
        'title' => 'Some example title',
        'status' => 'completed',
        'description' => 'An example description',
    ]);
});
```

- `actingAs($user)` → la página requiere autenticación.
- `@create-idea-button` → el prefijo `@` apunta a `data-test`.
- `toMatchArray([...])` → verifica que la idea guardada tiene esos valores.

> `visit()` requiere **Pest 4 / PHP 8.3**; en tu VM (PHP 8.2) se usa el feature test de abajo.

## Código usado en la VM (Pest 3 / PHP 8.2)

El mismo comportamiento sin abrir navegador, enviando el formulario con `post()`:

```php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a new idea', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/ideas', [
        'title' => 'Some example title',
        'description' => 'An example description',
        'status' => 'completed',
    ])->assertRedirect('/ideas');

    expect($user->ideas()->count())->toBe(1);

    expect($user->ideas()->first())->toMatchArray([
        'title' => 'Some example title',
        'status' => 'completed',
        'description' => 'An example description',
    ]);
});

it('requires a title', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/ideas', [
        'description' => 'An example description',
        'status' => 'pending',
    ])->assertSessionHasErrors('title');

    expect($user->ideas()->count())->toBe(0);
});
```

```bash
vendor/bin/pest tests/Feature/CreateIdeaTest.php
```

**Equivalencias:** `visit()->click()->fill()->click('Create')` ↔ `post('/ideas', [...])`; `assertPath('/ideas')` ↔ `assertRedirect('/ideas')`; `toMatchArray(...)` funciona igual en ambos.

---
---
# Allow For One or Many Links (Uno o varios enlaces)

Se permite asociar **uno o varios enlaces** a una idea mediante inputs dinámicos con AlpineJS. El usuario escribe un link, presiona `+` y se agrega a la lista; también puede eliminarlos. Todos se envían como un array `links[]`.

## Estado en Alpine

En el `<form>` se agregan dos datos al `x-data`:

```blade
<form ... x-data="{ status: 'pending', links: [], newLink: '' }">
```

- `links` -> array de enlaces ya agregados.
- `newLink` -> lo que el usuario esta escribiendo.

## Fieldset de enlaces

```blade
<fieldset class="space-y-3">
    <legend class="label">Links</legend>

    {{-- Enlaces ya agregados (editables + boton eliminar) --}}
    <template x-for="(link, index) in links" :key="link">
        <div class="flex gap-2">
            <input type="text" name="links[]" x-model="links[index]" class="input flex-1">
            <button type="button" @click="links.splice(index, 1)" aria-label="Remove link">
                <x-icon.close class="text-muted-foreground" />
            </button>
        </div>
    </template>

    {{-- Input para agregar un nuevo enlace --}}
    <div class="flex gap-2">
        <input type="url" id="new-link" x-model="newLink" data-test="new-link"
               placeholder="https://..." class="input flex-1" spellcheck="false">

        <button type="button" data-test="submit-new-link-button"
                :disabled="newLink.length === 0"
                @click="links.push(newLink.trim()); newLink = ''"
                aria-label="Add link"
                class="btn rotate-45">   {{-- rota la X 45 grados para simular un + --}}
            <x-icon.close />
        </button>
    </div>
</fieldset>
```

**Claves:**
- `x-for` con `:key="link"` recorre los enlaces ya agregados.
- `name="links[]"` -> agrupa todos los inputs como **array** al enviar el formulario tradicional.
- `@click="links.push(newLink.trim()); newLink = ''"` -> agrega el enlace (recortado) y limpia el input.
- `:disabled="newLink.length === 0"` -> no deja agregar vacios.
- `links.splice(index, 1)` -> elimina ese enlace.
- El boton `+` es en realidad el icono `close` (una X) **rotado 45 grados** (`rotate-45`).

## Validar los enlaces en el servidor

En `StoreIdeaRequest`:

```php
'links' => ['nullable', 'array'],
'links.*' => ['url', 'max:255'], // valida CADA elemento del array
```

- `links.*` valida cada URL individual del array.

## Guardar en la base de datos

Como el modelo `Idea` tiene el cast `links => AsArrayObject`, el array se guarda directo (ya viene incluido en `$request->validated()`):

```php
auth()->user()->ideas()->create($request->validated());
```

## Test actualizado (agregar links)

```php
// dentro del test de creacion (browser)
->fill('@new-link', 'https://laracasts.com')
->click('@submit-new-link-button')
->fill('@new-link', 'https://laravel.com')
->click('@submit-new-link-button')
// ...al enviar, se espera que links contenga ambos
```

En la VM (Pest 3) se prueba enviando `links` como array directamente:

```php
$this->actingAs($user)->post('/ideas', [
    'title' => 'Con links',
    'status' => 'pending',
    'links' => ['https://laracasts.com', 'https://laravel.com'],
])->assertRedirect('/ideas');
```

![Enlaces asociados a una idea](Images-entregable03/links%204.1%20models%20con%20links.png)
![Enlaces asociados a una idea](Images-entregable03/links%204.2%20Idea%20con%20links.png)
---
---

# Actionable Steps (Pasos accionables)

Una idea puede tener uno o varios **pasos** (tareas), cada uno con una descripcion y un estado `completed` que se puede marcar/desmarcar. Los pasos se guardan en su propia tabla `steps` (relacion `hasMany`).

## Agregar pasos en el modal (Alpine)

Igual que los links, se usa un array en Alpine:

```blade
<form ... x-data="{ status: 'pending', newLink: '', links: [], newStep: '', steps: [] }">
```

Fieldset de pasos (encima de Links):

```blade
<fieldset class="space-y-3">
    <legend class="label">Actionable steps</legend>

    <template x-for="(step, index) in steps" :key="index">
        <div class="flex gap-2">
            <input type="text" name="steps[]" x-model="steps[index]" class="input flex-1">
            <button type="button" @click="steps.splice(index, 1)" aria-label="Remove step">
                <x-icon.close class="text-muted-foreground" />
            </button>
        </div>
    </template>

    <div class="flex gap-2">
        <input type="text" id="new-step" x-model="newStep"
               placeholder="What needs to be done?" class="input flex-1" spellcheck="false">
        <button type="button" :disabled="newStep.length === 0"
                @click="steps.push(newStep.trim()); newStep = ''"
                aria-label="Add step" class="btn rotate-45">
            <x-icon.close />
        </button>
    </div>
</fieldset>
```

## Validar los pasos

En `StoreIdeaRequest`:

```php
'steps' => ['nullable', 'array'],
'steps.*' => ['string', 'max:255'],
```

## Persistir los pasos (tabla aparte)

Los pasos NO estan en la tabla `ideas`, van en `steps`. Se crea la idea sin los steps y luego se asocian:

```php
public function store(StoreIdeaRequest $request)
{
    // crear la idea con todo EXCEPTO steps
    $idea = auth()->user()->ideas()->create($request->safe()->except('steps'));

    // crear los steps relacionados (array de strings -> array de arrays)
    $idea->steps()->createMany(
        collect($request->steps ?? [])->map(fn ($step) => ['description' => $step])
    );

    return redirect()->route('idea.index')->with('success', 'Idea created');
}
```

- `$request->safe()->except('steps')` -> atributos validados **sin** `steps`.
- `createMany([...])` -> inserta varios steps de una vez.
- Se mapea cada string a `['description' => $step]` porque `createMany` espera arrays con las columnas.

## Mostrar y marcar pasos en la vista de detalle

En `show.blade.php`, encima de los links:

```blade
@if ($idea->steps->count())
    <div class="mt-8">
        <h3 class="font-bold">Actionable steps</h3>
        <div class="space-y-2 mt-2">
            @foreach ($idea->steps as $step)
                <x-card class="flex items-center gap-3">
                    {{-- boton para marcar/desmarcar (form PATCH) --}}
                    <form method="POST" action="{{ route('step.update', $step) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" role="checkbox"
                                @class([
                                    'size-5 flex items-center justify-center rounded border border-primary',
                                    'bg-primary text-primary-foreground' => $step->completed,
                                ])>
                            @if ($step->completed) &check; @endif
                        </button>
                    </form>

                    <span @class(['line-through text-muted-foreground' => $step->completed])>
                        {{ $step->description }}
                    </span>
                </x-card>
            @endforeach
        </div>
    </div>
@endif
```

## Ruta y controlador para alternar el estado

Ruta PATCH:

```php
Route::patch('/steps/{step}', [StepController::class, 'update'])
    ->name('step.update')->middleware('auth');
```

Controlador (`php artisan make:controller StepController`):

```php
public function update(Step $step)
{
    // Gate::authorize('update', $step->idea); // se agrega en el episodio de autorizacion

    $step->update(['completed' => ! $step->completed]);

    return back();
}
```

- `! $step->completed` -> invierte el valor actual (toggle).
- Si el paso esta completo, en la vista se le aplica `line-through` y color atenuado.

![Pasos accionables asociados a una idea](Images-entregable03/Steps%204.1%20Model%20con%20Steps.png)
![Pasos accionables asociados a una idea](Images-entregable03/Steps%204.2%20prueba%20pests.png)


---
---

# Upload Featured Images To Storage (Subir imagen destacada)

Se agrega la posibilidad de subir una **imagen destacada** por idea, que se guarda en el almacenamiento de Laravel y se muestra en la vista de detalle y en las tarjetas.

## Campo de archivo en el formulario

Debajo de la descripcion, en el modal:

```blade
<div class="space-y-2">
    <label for="image" class="label">Featured image</label>
    <input type="file" name="image" id="image" accept="image/*" class="input">
    <x-form.error name="image" />
</div>
```

## Formulario multipart

Para poder enviar archivos, el `<form>` necesita `enctype`:

```blade
<form method="POST" action="{{ route('idea.store') }}" enctype="multipart/form-data" ...>
```

> Sin `enctype="multipart/form-data"` el archivo **no se envia** al servidor.

## Validar la imagen

En `StoreIdeaRequest`:

```php
'image' => ['nullable', 'image', 'max:5120'], // max 5 MB (5120 KB)
```

## Guardar el archivo y asociar la ruta

En el `store()`, despues de crear la idea:

```php
if ($request->hasFile('image')) {
    $path = $request->file('image')->store('ideas', 'public'); // guarda en storage/app/public/ideas
    $idea->update(['image_path' => $path]);
}
```

- `store('ideas', 'public')` -> guarda el archivo en `storage/app/public/ideas` con un nombre unico y devuelve la ruta.
- Se guarda esa ruta en la columna `image_path` de la idea.

> La columna `image_path` ya se definio en la migracion de `ideas`.

## Enlace simbolico de storage (storage:link)

Los archivos en `storage/app/public` **no son accesibles** desde el navegador hasta crear el symlink:

```bash
php artisan storage:link
```

Esto crea `public/storage` -> `storage/app/public`, de modo que las imagenes quedan accesibles via `/storage/...`.

## Mostrar la imagen

En `show.blade.php`, arriba del titulo:

```blade
@if ($idea->image_path)
    <div class="rounded-lg overflow-hidden mb-6">
        <img src="{{ asset('storage/' . $idea->image_path) }}"
             alt="{{ $idea->title }}" class="w-full h-auto object-cover">
    </div>
@endif
```

- `asset('storage/' . $idea->image_path)` -> genera la URL publica usando el symlink.
- `object-cover` -> la imagen llena el area sin deformarse.

## Miniatura en la tarjeta (index)

En `index.blade.php`, dentro de cada `<x-card>`, arriba del titulo:

```blade
@if ($idea->image_path)
    <div class="-mx-4 -mt-4 mb-4 rounded-t-lg overflow-hidden">
        <img src="{{ asset('storage/' . $idea->image_path) }}"
             alt="{{ $idea->title }}" class="w-full h-auto object-cover">
    </div>
@endif
```

- `-mx-4 -mt-4` -> margenes negativos para que la imagen llegue a los bordes del card.
- `rounded-t-lg` -> esquinas redondeadas solo arriba.

![Carga de imagen destacada](Images-entregable03/Imagenes%204.1%20Cargar%20imagenes.png)

---
---

# Action Classes (Clases de accion)

Se refactoriza la logica de crear una idea (que estaba toda en el controlador) hacia una **clase de accion** dedicada. Asi se puede reutilizar desde cualquier parte (controlador, comando de consola, IA, pruebas) y el controlador queda simple.

## Que es una clase de accion

Es simplemente una clase con un metodo (por convencion `handle`) que hace **una** cosa con un nombre descriptivo. Se guardan en `app/Actions/`.

## Crear la clase CreateIdea

`app/Actions/CreateIdea.php`:

```php
<?php

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\DB;

class CreateIdea
{
    // Laravel inyecta el usuario autenticado gracias al atributo #[CurrentUser]
    public function __construct(
        #[CurrentUser] protected User $user
    ) {}

    public function handle(array $attributes): Idea
    {
        return DB::transaction(function () use ($attributes) {
            $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

            // si viene imagen, guardarla y agregar la ruta
            if ($attributes['image'] ?? false) {
                $data['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            $idea = $this->user->ideas()->create($data);

            // crear los steps relacionados
            $idea->steps()->createMany(
                collect($attributes['steps'] ?? [])->map(fn ($step) => ['description' => $step])
            );

            return $idea;
        });
    }
}
```

**Claves:**
- `#[CurrentUser]` -> atributo de PHP que le dice a Laravel que inyecte el usuario autenticado en el constructor.
- `collect($attributes)->only([...])` -> selecciona solo las columnas que van en la tabla `ideas`.
- `DB::transaction(...)` -> agrupa todas las operaciones; si algo falla, se **revierte todo** (rollback) para no dejar la BD inconsistente.

## Controlador simplificado

`app/Http/Controllers/IdeaController.php`:

```php
public function store(StoreIdeaRequest $request, CreateIdea $action)
{
    $action->handle($request->validated());

    return redirect()->route('idea.index')->with('success', 'Idea created');
}
```

- Laravel resuelve `CreateIdea` automaticamente desde el **contenedor de servicios** (inyeccion de dependencias), incluyendo el `#[CurrentUser]`.
- El controlador ahora solo: valida (form request), delega a la accion y redirige.

## Beneficios

- **Reutilizable:** se puede crear una idea desde un comando, un job, un test o IA, no solo desde el controlador.
- **Testeable:** el test de creacion (episodio 33) sigue pasando sin cambios, porque el comportamiento es el mismo.
- **Transaccional:** si falla la creacion de steps, no queda una idea a medias.

---
---

# Authorization Is A Requirement (La autorizacion es obligatoria)

Estar autenticado no basta: un usuario logueado no deberia poder ver, editar ni borrar ideas de **otro** usuario. Se agrega **autorizacion** con una Policy y se aplica en las acciones principales (`show`, `update`, `destroy`).

## Bug detectado por el test (contexto)

En el refactor anterior, el closure de la transaccion olvido incluir `$attributes` en el `use (...)`, asi que los steps nunca se insertaban. El test no lo detecto porque no habia una asercion sobre los steps. Leccion: escribir un test que verifique los steps:

```php
expect($idea->steps)->toHaveCount(2);
```

## Crear la Policy

Al generar el modelo con `--policy` ya se creo `app/Policies/IdeaPolicy.php`. Si no:

```bash
php artisan make:policy IdeaPolicy --model=Idea
```

Regla simple: solo el creador de la idea puede trabajar con ella:

```php
<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    // el usuario puede modificar/ver esta idea?
    public function workWith(User $user, Idea $idea): bool
    {
        return $user->is($idea->user); // solo el creador
    }
}
```

## Aplicar la autorizacion en el controlador

```php
use Illuminate\Support\Facades\Gate;

public function show(Idea $idea)
{
    Gate::authorize('workWith', $idea); // lanza 403 si no es el creador

    return view('ideas.show', ['idea' => $idea]);
}

public function destroy(Idea $idea)
{
    Gate::authorize('workWith', $idea);
    $idea->delete();

    return redirect()->route('idea.index');
}
```

- `Gate::authorize('workWith', $idea)` -> si la policy devuelve `false`, corta con **403 Forbidden**.

## Alternativa: autorizacion en la ruta (middleware)

En lugar del controlador, se puede autorizar en la ruta:

```php
Route::get('/ideas/{idea}', [IdeaController::class, 'show'])
    ->name('idea.show')
    ->can('workWith', 'idea'); // 'idea' = parametro de ruta resuelto
```

> No hay un "correcto": autorizar en el controlador o en la ruta es cuestion de preferencia. El video lo hace en el controlador.

## Pruebas de autorizacion

`tests/Feature/ShowIdeaTest.php` (version VM, Pest 3):

```php
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires authentication', function () {
    $idea = Idea::factory()->create();

    $this->get(route('idea.show', $idea))
        ->assertRedirect(route('login')); // sin login -> redirige a login
});

it('disallows accessing an idea you did not create', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(); // creada por OTRO usuario

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertForbidden(); // 403
});

it('allows accessing your own idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertOk();
});
```

```bash
vendor/bin/pest tests/Feature/ShowIdeaTest.php
```

> En el video se usan browser tests (`visit()`), que requieren Pest 4 / PHP 8.3; aqui se usan feature tests equivalentes con `get()`.

![Pruebas de autorizacion en verde](Images-entregable03/Auth%2010.1%20autorizacion.png)

---
---

# The Edit Idea Modal (Modal de edicion)

Se reutiliza el **mismo modal** para editar una idea existente. Al presionar "Edit idea" en la pagina de detalle se abre el modal con los datos ya cargados.

## Problema: el modal solo existia en index

El modal estaba escrito directamente en `index.blade.php`, asi que en `show` no existia. Por eso, al despachar `open-modal` desde "Edit idea", no pasaba nada.

## Solucion: extraer el modal a un componente reutilizable

Se mueve todo el modal + formulario a un componente que acepta una idea **opcional**:

`resources/views/components/idea/modal.blade.php`

```blade
@props(['idea' => null])

@php
    $editing = (bool) $idea; // hay idea -> modo edicion; null -> modo creacion
@endphp

<x-modal :name="$editing ? 'edit-idea-'.$idea->id : 'create-idea'"
         :title="$editing ? 'Edit idea' : 'New idea'">
    <form method="POST"
          action="{{ $editing ? route('idea.update', $idea) : route('idea.store') }}"
          enctype="multipart/form-data"
          x-data="{
              status: '{{ $editing ? $idea->status->value : 'pending' }}',
              newLink: '', links: {{ $editing ? Js::from($idea->links) : '[]' }},
              newStep: '', steps: {{ $editing ? Js::from($idea->steps->pluck('description')) : '[]' }}
          }"
          class="space-y-6">
        @csrf
        @if ($editing) @method('PATCH') @endif

        {{-- ...los mismos campos (title, status, description, image, links, steps)... --}}
        {{-- pre-cargando valores: value="{{ old('title', $idea?->title) }}" --}}

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="btn btn-ghost" @click="$dispatch('close-modal')">Cancel</button>
            <button type="submit" class="btn">{{ $editing ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</x-modal>
```

**Claves de la reutilizacion:**
- El prop `:idea` decide si el modal esta en **modo creacion** (`null`) o **modo edicion** (una idea).
- Si edita: la accion es `PATCH` a `route('idea.update', $idea)`; si crea: `POST` a `route('idea.store')`.
- El `x-data` se **precarga** con el status, links y steps de la idea (usando `Js::from(...)` para pasar arrays PHP a JavaScript de forma segura).
- Los campos usan `old('campo', $idea?->campo)` para mostrar los valores actuales.
- El titulo y el boton cambian entre "New idea"/"Create" y "Edit idea"/"Update".

## Incluir el modal en ambas paginas

```blade
{{-- index.blade.php: modo creacion --}}
<x-idea.modal />

{{-- show.blade.php: modo edicion, se le pasa la idea --}}
<x-idea.modal :idea="$idea" />
```

## Boton "Edit idea" en la vista de detalle

```blade
<button data-test="edit-idea-button" x-data
        @click="$dispatch('open-modal', 'edit-idea-{{ $idea->id }}')"
        class="btn btn-outline">
    Edit idea
</button>
```

- Despacha `open-modal` con el nombre unico del modal de edicion (`edit-idea-{id}`), que coincide con el `name` del componente.

## Test (inicio)

```php
it('edits an existing idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $page = visit(route('idea.show', $idea))->actingAs($user);
    $page->click('@edit-idea-button'); // abre el modal de edicion
    // ...enviar cambios y verificar (se completa con Update Idea Action)
});
```

![Modal de edicion](Images-entregable03/Edit%20modal%204.1%20edicion%20de%20Idea.png)

---
---

# Update Idea Action (Accion para actualizar ideas)

Se implementa la actualizacion de una idea con una **segunda clase de accion** (`UpdateIdea`), separada de `CreateIdea`. Al editar y enviar el modal, la idea se actualiza y se redirige a su pagina de detalle.

## Dos acciones separadas (no una)

Se evaluo reutilizar `CreateIdea`, pero requeriria muchos `if/else` (hay idea vs no hay). Por claridad se decide **mantenerlas separadas**: `CreateIdea` y `UpdateIdea`.

## Los steps ahora son objetos (no strings)

Para poder actualizar/eliminar/agregar steps al editar, el formulario pasa a ser la **fuente de verdad**: cada step lleva `description` **y** `completed`.

En el modal:

```blade
<template x-for="(step, index) in steps" :key="step.id ?? index">
    <div class="flex gap-2">
        <input type="text" :name="`steps[${index}][description]`" x-model="step.description" class="input flex-1">
        <input type="hidden" :name="`steps[${index}][completed]`" :value="step.completed ? 1 : 0">
        <button type="button" @click="steps.splice(index, 1)" aria-label="Remove step">
            <x-icon.close class="text-muted-foreground" />
        </button>
    </div>
</template>
```

Al agregar un step nuevo se empuja un **objeto**:

```js
steps.push({ description: newStep.trim(), completed: false }); newStep = ''
```

Y en modo edicion, el `x-data` se precarga con objetos:

```blade
steps: {{ Js::from($idea->steps->map->only('id', 'description', 'completed')) }}
```

## Validacion actualizada

En el Form Request, `steps` pasa de array de strings a array de objetos:

```php
'steps' => ['nullable', 'array'],
'steps.*.description' => ['required', 'string', 'max:255'],
'steps.*.completed' => ['boolean'],
```

## La clase UpdateIdea

`app/Actions/UpdateIdea.php`:

```php
<?php

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateIdea
{
    // no necesita el usuario: se infiere de la idea que se actualiza
    public function handle(array $attributes, Idea $idea): Idea
    {
        return DB::transaction(function () use ($attributes, $idea) {
            $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

            if (($attributes['image'] ?? null) instanceof UploadedFile) {
                $data['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            $idea->update($data);

            // "wipe & rebuild": borrar los steps y recrearlos desde el form
            $idea->steps()->delete();
            $idea->steps()->createMany($attributes['steps'] ?? []);

            return $idea;
        });
    }
}
```

**Claves:**
- Recibe `(array $attributes, Idea $idea)` — sin usuario, se infiere de la idea.
- `instanceof UploadedFile` — solo procesa la imagen si de verdad se subio una nueva.
- **Wipe & rebuild de steps**: `steps()->delete()` + `createMany(...)` — es mas simple que un `upsert` y sincroniza altas, bajas y cambios de una vez.

## Controlador

```php
public function update(StoreIdeaRequest $request, Idea $idea, UpdateIdea $action)
{
    Gate::authorize('workWith', $idea); // solo el creador

    $action->handle($request->validated(), $idea);

    return redirect()->route('idea.show', $idea)->with('success', 'Idea updated');
}
```

## Pruebas (organizacion)

Se separan en carpeta `tests/Browser/Idea/` (o `Feature/Idea/`): `CreateIdeaTest` y `UpdateIdeaTest`. Ejemplos de aserciones utiles al editar:

- `assertValue('title', $idea->title)` — el input se precarga con el valor actual.
- Verificar que tras editar hay la cantidad correcta de steps/links.
- Verificar el redirect a `idea.show`.

## Detalle: id para steps nuevos

Al usar `:key` en el `x-for`, los steps existentes tienen `id`, pero los nuevos no. Se usa `step.id ?? index` como key para evitar que se sobrescriban entre si.

---
---
# Edit Your Profile (Editar el perfil)

Se agrega la posibilidad de que el usuario edite su perfil (nombre, email, contrasena) y, como buena practica de seguridad, se **notifica al email anterior** cuando cambia el correo.

## Enlace y ruta

En el nav, si el usuario esta autenticado:

```blade
@auth
    <a href="{{ route('profile.edit') }}">Edit profile</a>
@endauth
```

Rutas:

```php
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
```

> Usar **rutas con nombre** evita tener que actualizar todos los enlaces si cambias la URL.

## Controlador de perfil

```bash
php artisan make:controller ProfileController
```

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

public function edit()
{
    return view('profile.edit', ['user' => Auth::user()]);
}

public function update(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        'password' => ['nullable', Password::defaults()],
    ]);

    $originalEmail = $user->email; // guardar el email anterior

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        // solo actualizar la contrasena si se envio una
        ...($validated['password'] ? ['password' => $validated['password']] : []),
    ]);

    // si el email cambio, avisar al correo anterior
    if ($originalEmail !== $user->email) {
        Notification::route('mail', $originalEmail)
            ->notify(new EmailChanged($user, $originalEmail));
    }

    return redirect()->route('profile.edit')->with('success', 'Profile updated');
}
```

**Claves:**
- `Rule::unique('users','email')->ignore($user->id)` -> el email debe ser unico, pero **ignorando** al propio usuario (para que no choque consigo mismo).
- El `password` es opcional: solo se actualiza si se escribio uno (se hashea solo por el cast `hashed`).
- `Notification::route('mail', $originalEmail)->notify(...)` -> envia una notificacion **on-demand** a un email que no es de un usuario del sistema (el correo anterior).

## Vista del formulario

`resources/views/profile/edit.blade.php` (similar al registro, precargada con los valores actuales):

```blade
<x-layout>
    <x-form title="Edit your account" description="Update your profile details">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <x-form.field label="Name" name="name" :value="$user->name" />
            <x-form.field label="Email" name="email" type="email" :value="$user->email" />
            <x-form.field label="New password" name="password" type="password" />

            <button type="submit" class="btn w-full">Update account</button>
        </form>
    </x-form>
</x-layout>
```

## Notificacion EmailChanged

```bash
php artisan make:notification EmailChanged
```

```php
class EmailChanged extends Notification
{
    public function __construct(public User $user, public string $originalEmail) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your email was changed')
            ->line('Heads up: the email on your account was changed.')
            ->line('If you did not do this, please contact support.');
    }
}
```

## Pruebas

- `it('requires authentication')` -> sin login, `get(route('profile.edit'))->assertRedirect(route('login'))`.
- `it('edits a profile')` -> tras enviar, `assertRedirect` + `$user->fresh()->name` actualizado.
- `it('notifies the original email if changed')` -> usar `Notification::fake()` y `Notification::assertSentOnDemand(EmailChanged::class, fn ($n, $channels, $notifiable) => $notifiable->routes['mail'] === $originalEmail)`.

> `Notification::fake()` evita enviar correos reales y permite hacer aserciones sobre lo que se "envio".

![Perfil de usuario editable](Images-entregable03/edit%20profile%204.1%20Ediatar%20mi%20perfil.png)


---
---

# Deploy And Then Implement A Feature Request (Desplegar e implementar una mejora)

Se cierra el proyecto: correr formato/pruebas, desplegar, y luego implementar una **solicitud de mejora** (descripcion con Markdown) para demostrar el ciclo completo cambio -> deploy -> produccion.

## Formato y pruebas antes de desplegar

```bash
composer run format   # Rector + Pint
vendor/bin/pest       # o php artisan test
```

Se corrigen tests que quedaron desactualizados: register y login ahora redirigen a `idea.index` (no a `/`), asi que las aserciones cambian a `assertRedirect(route('idea.index'))`.

## Despliegue

En el video se usa **Laravel Forge** (push a GitHub -> hook -> deploy automatico). En este curso el despliegue es en **tu VM** (Apache), asi que el flujo equivalente es:

```bash
# en la VM, dentro del proyecto
git pull
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
```

## Feature request: descripcion con Markdown

Se agrega un **accessor** (propiedad calculada) en el modelo `Idea` para formatear la descripcion como Markdown.

`app/Models/Idea.php`:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

protected function formattedDescription(): Attribute
{
    return Attribute::get(
        fn ($value, $attributes) => Str::of($attributes['description'] ?? '')->markdown()
    );
}
```

- Un **accessor** define una propiedad calculada: `$idea->formatted_description`.
- `Str::of($texto)->markdown()` convierte el Markdown a HTML.

## Mostrar el HTML en la vista

En `show.blade.php`, la descripcion usa `{!! !!}` (sin escapar) para renderizar el HTML, y clases de tipografia:

```blade
<div class="card mt-6 prose prose-invert max-w-none">
    {!! $idea->formatted_description !!}
</div>
```

- `{!! ... !!}` -> imprime HTML sin escapar (necesario para el Markdown ya convertido).
- `prose prose-invert` -> plugin de tipografia de Tailwind (estiliza el HTML: titulos, listas, enlaces, negritas); `invert` para tema oscuro.

## Plugin de tipografia

```bash
npm install @tailwindcss/typography
```

En `resources/css/app.css`:

```css
@plugin "@tailwindcss/typography";
```

## Test del accessor

```php
it('formats a description using markdown', function () {
    $idea = new Idea();
    $idea->description = "hello *world*";

    expect((string) $idea->formatted_description)
        ->toContain('<em>world</em>');
});
```

---
---

# Where To Go From Here (Hacia dónde seguir)

Episodio de cierre del curso. Jeffrey Way agradece y da recomendaciones sobre cómo seguir aprendiendo y mejorando el proyecto.

## Ideas de mejora para el proyecto

El proyecto de ideas es open source y se puede seguir ampliando. Sugerencias de features "de la vida real":

- **Soporte de equipos (teams):** que varios usuarios (ej. John y Jane) compartan y colaboren en las mismas ideas.
- **Autorización mas fina:** por ejemplo, John puede **ver** las ideas de Jane pero no **editarlas ni borrarlas**. Excelente práctica de policies.

## Herramientas recomendadas para seguir

| Herramienta | Para qué sirve |
|---|---|
| **Laravel Livewire** | Construir interfaces interactivas usando solo PHP, sin escribir JavaScript. |
| **Vue.js / React** | Si te gusta JavaScript, frameworks front-end que se integran con Laravel. |
| **Inertia.js** | Combina lo mejor de ambos: rutas y autorización tradicionales del lado del servidor, pero con la experiencia de una SPA. Es lo que usa el propio Laracasts. |

## Mensaje final

Hay muchísimos caminos posibles y **ninguno es incorrecto** — lo importante es elegir uno y seguir aprendiendo, porque el aprendizaje nunca termina.

> Con este episodio se completa la serie **Laravel From Scratch (2026 Edition)** y el Proyecto 1 del curso ISW811.

---
---
