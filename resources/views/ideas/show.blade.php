<x-layout>
    <div class="card bg-neutral p-6">
        <div>
            {{ $idea->description }}
        </div>

        <div class="mt-6 flex gap-4">
            <a href="/ideas/{{ $idea->id }}/edit"
            class="btn btn-primary">
                Edit
            </a>
        </div>
    </div>
</x-layout>