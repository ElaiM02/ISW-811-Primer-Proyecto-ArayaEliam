@props([
    'title' => 'Laracasts'
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
<body class="bg-gray-700 p-6 max-w-xl mx-auto">
    <nav>
        <a href="/">Home</a>
        <a href="about">About Us</a>
        <a href="contact">Contact Us</a>
    </nav>
    
    <main>
        {{ $slot }}
    </main>
</body>
</html>