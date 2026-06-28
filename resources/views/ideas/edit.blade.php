<x-layout>
    <form method="POST" action="/ideas/{{ $idea->id }}">
        @csrf
        @method('PATCH')
        <div class="col-span-full">
          <label for="description" class="block text-sm/6 font-medium text-white">Edit your idea</label>
          <div class="mt-2">
            <textarea id="description" name="description" rows="3" 
            class="textarea w-full">{{$idea->description}}</textarea>
          </div>
        </div>

        <div class="mt-6 flex items-center gap-x-6">
            <button type="submit" 
                class="btn btn-primary">
                Update
            </button>

            <button type="submit" 
            form="delete-form"
            class="btn btn-danger">
                Delete
            </button>
        </div>
    </form>

    <form id="delete-form" method="POST" action="/ideas/{{ $idea->id }}">
        @csrf
        @method('DELETE')
    <form>
</x-layout>