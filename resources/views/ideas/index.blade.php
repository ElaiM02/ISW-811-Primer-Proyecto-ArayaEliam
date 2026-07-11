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

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($ideas as $idea)
            <x-card href="/ideas/{{ $idea->id }}">
                <h3 class="text-lg text-white">{{ $idea->title }}</h3>
                <x-idea.status-label :status="$idea->status" />
                <div class="text-muted-foreground mt-3 line-clamp-3">{{ $idea->description }}</div>
                <div class="text-muted-foreground text-xs mt-4">{{ $idea->created_at->diffForHumans() }}</div>
            </x-card>
        @empty
            <p class="text-muted-foreground">No ideas at this time.</p>
        @endforelse
    </div>


    <x-modal name="create-idea" title="New idea">
        <form method="POST" action="{{ route('idea.store') }}"
            x-data="{ status: 'pending', newLink: '', links: [], newStep: '', steps: [] }" class="space-y-6">
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

            <x-form.field label="Title" name="title" placeholder="Enter a title for your idea" required />

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

            <x-form.field label="Description" name="description" type="textarea"
                        placeholder="Describe your idea" />

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
                            aria-label="Add link"
                            class="btn rotate-45">
                        <x-icon.close />
                    </button>
                </div>
            </fieldset>

            <div>
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
            </div>
                
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn btn-ghost"
                        @click="$dispatch('close-modal')">Cancel</button>
                <button type="submit" class="btn">Create</button>
            </div>
        </form>
    </x-modal>

    @if ($errors->any())
        <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'create-idea'))"></div>
    @endif
</x-layout>
