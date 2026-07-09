@props([
    'title' => 'Laracasts'
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-primary px-10">
    <x-nav />

    <main class="">
        {{ $slot }}
    </main>

@if (session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition.opacity.duration.300ms
        class="fixed bottom-4 right-4 bg-primary text-white px-4 py-2 rounded-lg"
    >
        {{ session('success') }}
    </div>
@endif
</body>
</html>