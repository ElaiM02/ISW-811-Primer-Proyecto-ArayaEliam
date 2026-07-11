<x-layout>
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-foreground">My Ideas</h1>
        <p class="text-muted-foreground text-sm mt-1">Capture your thoughts. Make a plan.</p>

        <x-card is="button" x-data
                @click="$dispatch('open-modal', 'create-idea')"
                class="w-full text-left mt-10 cursor-pointer h-24">
            <p>What's the idea?</p>
        </x-card>
    </header>

    {{-- Filtros por estado --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="/ideas" @class(['btn', 'btn-outline' => request()->has('status')])>
            All
            <span class="text-xs pl-1">{{ $statusCounts['all'] ?? 0 }}</span>
        </a>

        @foreach (\App\IdeaStatus::cases() as $status)
            <a href="/ideas?status={{ $status->value }}"
               @class(['btn', 'btn-outline' => request('status') !== $status->value])>
                {{ $status->label() }}
                <span class="text-xs pl-1">{{ $statusCounts[$status->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    {{-- Grilla de ideas --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($ideas as $idea)
            <x-card href="/ideas/{{ $idea->id }}">
                
                @if ($idea->image_path)
                    <div class="-mx-4 -mt-4 mb-4 rounded-t-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $idea->image_path) }}"
                             alt="{{ $idea->title }}" class="w-full h-auto object-cover">
                    </div>
                @endif

                <h3 class="text-lg text-white">{{ $idea->title }}</h3>
                <x-idea.status-label :status="$idea->status" />
                <div class="text-muted-foreground mt-3 line-clamp-3">{{ $idea->description }}</div>
                <div class="text-muted-foreground text-xs mt-4">{{ $idea->created_at->diffForHumans() }}</div>
            </x-card>
        @empty
            <p class="text-muted-foreground">No ideas at this time.</p>
        @endforelse
    </div>

    {{-- Modal de creación --}}
    <x-modal name="create-idea" title="New idea">
        <form method="POST" action="{{ route('idea.store') }}"
              enctype="multipart/form-data"
              x-data="{ status: 'pending', newLink: '', links: [], newStep: '', steps: [] }"
              class="space-y-6">
            @csrf

            {{-- Resumen de errores de validación --}}
            @if ($errors->any())
                <div class="text-red-500 text-sm space-y-1">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Title --}}
            <x-form.field label="Title" name="title" placeholder="Enter a title for your idea" required />

            {{-- Status --}}
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
                <input type="hidden" name="status" :value="status">
            </div>

            {{-- Description --}}
            <x-form.field label="Description" name="description" type="textarea"
                          placeholder="Describe your idea" />

            {{-- Featured image --}}
            <div class="space-y-2">
                <label for="image" class="label">Featured image</label>
                <input type="file" name="image" id="image" accept="image/*" class="input">
                <x-form.error name="image" />
            </div>

            {{-- Links --}}
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>

                <template x-for="(link, index) in links" :key="link">
                    <div class="flex gap-2">
                        <input type="text" name="links[]" x-model="links[index]" class="input flex-1">
                        <button type="button" @click="links.splice(index, 1)" aria-label="Remove link">
                            <x-icon.close class="text-muted-foreground" />
                        </button>
                    </div>
                </template>

                <div class="flex gap-2">
                    <input type="url" id="new-link" x-model="newLink" data-test="new-link"
                           placeholder="https://..." class="input flex-1" spellcheck="false">
                    <button type="button" data-test="submit-new-link-button"
                            :disabled="newLink.length === 0"
                            @click="links.push(newLink.trim()); newLink = ''"
                            aria-label="Add link" class="btn rotate-45">
                        <x-icon.close />
                    </button>
                </div>
            </fieldset>

            {{-- Actionable steps --}}
            <fieldset class="space-y-3">
                <legend class="label">Actionable steps</legend>

                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex gap-2">
                        <input type="text" :name="`steps[${index}][description]`" x-model="step.description" class="input flex-1">
                        <input type="hidden" :name="`steps[${index}][completed]`" :value="step.completed ? 1 : 0">
                        <button type="button" @click="steps.splice(index, 1)" aria-label="Remove step">
                            <x-icon.close class="text-muted-foreground" />
                        </button>
                    </div>
                </template>

                <div class="flex gap-2">
                    <input type="text" id="new-step" x-model="newStep"
                           placeholder="What needs to be done?" class="input flex-1" spellcheck="false">
                    <button type="button" :disabled="newStep.length === 0"
                            @click="steps.push({ description: newStep.trim(), completed: false }); newStep = ''"
                            aria-label="Add step" class="btn rotate-45">
                        <x-icon.close />
                    </button>
                </div>
            </fieldset>

            {{-- Botones --}}
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn btn-ghost"
                        @click="$dispatch('close-modal')">Cancel</button>
                <button type="submit" class="btn">Create</button>
            </div>
        </form>
    </x-modal>

    {{-- Si la validación falló, reabrir el modal --}}
    @if ($errors->any())
        <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'create-idea'))"></div>
    @endif
</x-layout>
