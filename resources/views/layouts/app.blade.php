<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            (() => {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased transition-colors duration-300">
        <div class="min-h-screen relative overflow-hidden bg-gray-50 dark:bg-gray-950">
            <div class="pointer-events-none absolute inset-0 opacity-70 dark:opacity-40" aria-hidden="true">
                <div class="absolute -left-10 -top-10 h-72 w-72 rounded-full bg-indigo-200 blur-3xl dark:bg-indigo-900/60"></div>
                <div class="absolute right-[-4rem] top-20 h-80 w-80 rounded-full bg-sky-200 blur-3xl dark:bg-sky-900/50"></div>
                <div class="absolute left-1/3 bottom-[-8rem] h-72 w-72 rounded-full bg-emerald-200 blur-3xl dark:bg-emerald-900/50"></div>
            </div>

            <div class="relative">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow-sm dark:bg-gray-900 dark:shadow-none">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
