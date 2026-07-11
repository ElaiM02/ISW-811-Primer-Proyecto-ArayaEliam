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
        {{-- Botón "All" --}}
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
            x-data="{ status: 'pending' }" class="space-y-6">
            @csrf

            <x-form.field label="Title" name="title" placeholder="Enter a title for your idea" required />

            <x-form.field label="Description" name="description" type="textarea"
                        placeholder="Describe your idea" />


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
            
            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost"
                        @click="$dispatch('close-modal')">Cancel</button>
                <button type="submit" class="btn">Create</button>
            </div>
        </form>
    </x-modal>
</x-layout>
