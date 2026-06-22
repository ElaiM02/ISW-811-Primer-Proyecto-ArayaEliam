<x-layout>

    @forelse($tasks as $task)
    <li>{{ $task }}</li>
@empty
    <p>There are no active tasks.</p>
@endforelse
</x-layout>