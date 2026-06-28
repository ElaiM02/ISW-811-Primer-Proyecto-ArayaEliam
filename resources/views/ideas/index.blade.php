<x-layout>

    <form method="POST" action="/ideas">
        @csrf
        <div class="col-span-full">
          <label for="description" class="block text-sm/6 font-medium text-white">Create new idea</label>
          <div class="mt-2">
            <textarea id="description" name="description" rows="3" class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"></textarea>
          </div>
          <p class="mt-3 text-sm/6 text-gray-400">Have an idea to share?</p>
        </div>

        <div class="mt-6 flex items-center gap-x-6">
            <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                Save
            </button>
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