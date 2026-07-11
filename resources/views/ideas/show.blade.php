<x-layout>
    <div class="p-4 max-w-3xl mx-auto">

        <div class="flex justify-between items-center">
            <a href="{{ route('idea.index') }}" class="flex gap-1 text-sm font-bold">
                <x-icons.arrow-back /> Back to ideas
            </a>

            <div class="flex items-center gap-2">
                <button data-test="edit-idea-button" x-data
                        @click="$dispatch('open-modal', 'edit-idea-{{ $idea->id }}')"
                        class="btn btn-outline">
                    Edit idea
                </button>

                <form method="POST" action="{{ route('idea.destroy', $idea) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline text-red-500">Delete</button>
                </form>
            </div>
        </div>

        <div class="mt-8 space-y-6">
            @if ($idea->image_path)
                <div class="rounded-lg overflow-hidden mb-6">
                    <img src="{{ asset('storage/' . $idea->image_path) }}"
                        alt="{{ $idea->title }}" class="w-full h-auto object-cover">
                </div>
            @endif

            <h1 class="text-2xl font-bold mt-8">{{ $idea->title }}</h1>

            <div class="flex gap-2 items-center mt-2">
                <x-idea.status-label :status="$idea->status" />
                <span class="text-muted-foreground text-sm">
                    {{ $idea->updated_at->diffForHumans() }}
                </span>
            </div>

            <div class="card mt-6 max-w-none">
                {{ $idea->description }}
            </div>

            @if ($idea->steps->count())
                <div class="mt-8">
                    <h3 class="font-bold">Actionable steps</h3>
                    <div class="space-y-2 mt-2">
                        @foreach ($idea->steps as $step)
                            <x-card class="flex items-center gap-3">
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
    </div>

    {{-- Modal de edición --}}
    <x-modal name="edit-idea-{{ $idea->id }}" title="Edit idea">
        <form method="POST" action="{{ route('idea.update', $idea) }}"
              enctype="multipart/form-data"
              x-data="{
                  status: '{{ $idea->status->value }}',
                  newLink: '', links: {{ Js::from(collect($idea->links)->toArray()) }},
                  newStep: '', steps: {{ Js::from($idea->steps->map->only('id', 'description', 'completed')) }}

              }"
              class="space-y-6">
            @csrf
            @method('PATCH')

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
            <x-form.field label="Title" name="title" :value="$idea->title" required />

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
                          :value="$idea->description" placeholder="Describe your idea" />

            {{-- Featured image --}}
            <div class="space-y-2">
                <label for="image-edit" class="label">Featured image</label>
                <input type="file" name="image" id="image-edit" accept="image/*" class="input">
                <x-form.error name="image" />
            </div>

            {{-- Links --}}
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>

                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-2">
                        <input type="text" name="links[]" x-model="links[index]" class="input flex-1">
                        <button type="button" @click="links.splice(index, 1)" aria-label="Remove link">
                            <x-icon.close class="text-muted-foreground" />
                        </button>
                    </div>
                </template>

                <div class="flex gap-2">
                    <input type="url" x-model="newLink" placeholder="https://..."
                           class="input flex-1" spellcheck="false">
                    <button type="button" :disabled="newLink.length === 0"
                            @click="links.push(newLink.trim()); newLink = ''"
                            aria-label="Add link" class="btn rotate-45">
                        <x-icon.close />
                    </button>
                </div>
            </fieldset>

            {{-- Actionable steps --}}
            <fieldset class="space-y-3">
                <legend class="label">Actionable steps</legend>

                <template x-for="(step, index) in steps" :key="step.id ?? index">
                    <div class="flex gap-2">
                        <input type="text" :name="`steps[${index}][description]`" x-model="step.description" class="input flex-1">
                        <input type="hidden" :name="`steps[${index}][completed]`" :value="step.completed ? 1 : 0">
                        <button type="button" @click="steps.splice(index, 1)" aria-label="Remove step">
                            <x-icon.close class="text-muted-foreground" />
                        </button>
                    </div>
                </template>

                <div class="flex gap-2">
                    <input type="text" x-model="newStep" placeholder="What needs to be done?"
                           class="input flex-1" spellcheck="false">
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
                <button type="submit" class="btn">Update</button>
            </div>
        </form>
    </x-modal>

    {{-- Si la validación falló, reabrir el modal de edición --}}
    @if ($errors->any())
        <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'edit-idea-{{ $idea->id }}'))"></div>
    @endif
</x-layout>
