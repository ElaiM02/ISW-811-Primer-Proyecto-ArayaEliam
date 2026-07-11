<x-layout>
    <div class="p-4 max-w-3xl mx-auto">

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
</x-layout>