<x-layout>
    <header>
        <h1 class="text-2xl font-bold">My Ideas</h1>
    </header>

    <div class="grid lg:grid-cols-2 gap-4 mt-6">
        @forelse ($ideas as $idea)
            <x-card>
                <a href="/ideas/{{ $idea->id }}">
                    <h3 class="text-lg text-white">{{ $idea->title }}</h3>
                    <x-idea.status-label :status="$idea->status" />
                    <div class="text-muted-foreground">{{ $idea->description }}</div>
                    <div class="mt-2">{{ $idea->created_at->diffForHumans() }}</div>
                </a>
            </x-card>
        @empty
            <p>No ideas at this time.</p>
        @endforelse
    </div>
</x-layout>