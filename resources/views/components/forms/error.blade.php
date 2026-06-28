@props(['name'])

@error($name)
    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
@enderror