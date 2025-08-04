<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <!-- CSS propio dark mode -->
    <link rel="stylesheet" href="{{ asset('public/css/darkmode.css') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const darkToggle = document.getElementById('darkModeToggle');
            if (!darkToggle) return;

            const darkIcon = darkToggle.querySelector('i');

            // Carga estado guardado
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-theme');
                darkIcon.classList.replace('fa-moon', 'fa-sun');
            }

            // Toggle dark mode
            darkToggle.addEventListener('click', (e) => {
                e.preventDefault();
                body.classList.toggle('dark-theme');
                if (body.classList.contains('dark-theme')) {
                    localStorage.setItem('darkMode', 'enabled');
                    darkIcon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    darkIcon.classList.replace('fa-sun', 'fa-moon');
                }
            });
        });
    </script>
</body>
</html>
