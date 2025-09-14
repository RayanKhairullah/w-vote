@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? ($title . ' — W-Vote') : 'W-Vote' }}</title>
    <link rel="icon" href="{{ asset('images/logo-w_vote.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-w_vote.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-zinc-950 text-gray-900 dark:text-zinc-100">
    <main class="py-6">
        {{ $slot }}
    </main>
</body>
<!-- Livewire scripts are loaded via resources/js/app.js -->
</html>
