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
