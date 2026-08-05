@props([
    'title' => 'Dashboard - PURE Research Hub',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark Mode FOUC Prevention -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-200">
    <!-- Authenticated Navbar -->
    <x-layout.navbar />

    <div class="flex-1 flex w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 gap-6">
        <!-- Sidebar -->
        <div class="hidden lg:block">
            <x-layout.sidebar type="user" />
        </div>

        <!-- Dashboard Content -->
        <main class="flex-1 min-w-0">
            <!-- Flash Messages -->
            @if (session('success'))
                <x-ui.alert type="success">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert type="error">
                    {{ session('error') }}
                </x-ui.alert>
            @endif

            @if (session('status'))
                <x-ui.alert type="info">
                    {{ session('status') }}
                </x-ui.alert>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Footer -->
    <x-layout.footer />

    <!-- Real-Time Echo User Notification Listener -->
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Echo) {
                    window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                        .notification((notification) => {
                            console.log('Realtime Notification Received:', notification);
                            window.dispatchEvent(new CustomEvent('notification-received', { detail: notification }));
                        });
                }
            });
        </script>
    @endauth
</body>
</html>
