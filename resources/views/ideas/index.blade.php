<x-layout>
@if ($ideas->count())
    <div class="mt-6" text-white>
        <h2>Your ideas</h2>
        <ul>
            @foreach ($ideas as $idea)
                <li class="text-sm">{{ $idea->description }}</li>
            @endforeach
        </ul>
    </div>
@else
    <p>No ideas yet. <a href="/ideas/create" class="text-indigo-500">Create one</a></p>
@endif
</x-layout>