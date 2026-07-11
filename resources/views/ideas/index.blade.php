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

    {{-- Modal de creación --}}
    <x-modal name="create-idea" title="New idea">
        <p>Aquí irá el formulario para crear una idea (siguiente episodio).</p>
    </x-modal>
</x-layout>
