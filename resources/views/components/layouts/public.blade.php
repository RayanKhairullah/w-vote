@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? ($title . ' — W-Vote') : 'W-Vote' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-zinc-950 text-gray-900 dark:text-zinc-100">
    <div class="min-h-screen flex flex-col">
        <main class="flex-1 py-6">
            <div class="max-w-6xl mx-auto px-4">
                {{ $slot }}
            </div>
        </main>
        <footer class="py-4 text-center text-xs text-gray-500 dark:text-zinc-500">
            &copy; {{ date('Y') }} W-Vote
        </footer>
    </div>
</body>
<!-- Livewire scripts are loaded via resources/js/app.js -->
</html>
