@props([
    'title' => 'Laracasts'
])

<!DOCTYPE html>
<html lang="en" data-theme="dracula">
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
</body>
</html>