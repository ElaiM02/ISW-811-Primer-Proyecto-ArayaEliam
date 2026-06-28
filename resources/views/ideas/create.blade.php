<x-layout>
    <form method="POST" action="/ideas">
        @csrf
        <div class="col-span-full">
            <label for="description" class="block text-sm/6 font-medium text-white">Create new idea</label>
            <div class="mt-2">
                <textarea id="description" 
                name="description" 
                rows="3" 
                class="textarea w-full"></textarea>
            </div>

            <x-forms.error name="description" />

            <p class="mt-3 text-sm/6 text-gray-400">Have an idea to share?</p>
        </div>

        <div class="mt-6 flex items-center gap-x-6">
            <button type="submit" class="btn">
                Save
            </button>
        </div>
    </form>
</x-layout>