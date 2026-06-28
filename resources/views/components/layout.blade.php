@props([
    'title' => 'Laracasts'
])

<!DOCTYPE html>
<html lang="en" data-theme="night">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <style>
        nav {
            background-color: #333;
            padding: 1em;
        }
        nav a {
            color: white;
            margin-right: 1em;
            text-decoration: none;
        }
        main {
            padding: 2em;
        }
    </style>
</head>
<body class="">
    <x-nav />

    <main class="max-w-3xl mx-auto mt-6">
        {{ $slot }}
    </main>
</body>
</html>