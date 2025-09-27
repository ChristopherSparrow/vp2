<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Load Vite-built assets when available, otherwise fall back to a prebuilt CSS file if present. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @elseif (file_exists(public_path('css/app.css')))
            {{-- Fallback to a prebuilt stylesheet in public/css/app.css when present (created by `npm run build`). --}}
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endif
    </head>
    <body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <header class="bg-white dark:bg-gray-800 border-b">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="font-semibold text-lg">{{ config('app.name', 'Laravel') }}</a>
                    <nav class="hidden sm:flex items-center gap-4">
                        <a href="{{ url('/seasons') }}" class="text-sm hover:underline">Seasons</a>
                    </nav>
                </div>

                <div>
                    @if (Route::has('login'))
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm px-3 py-1 rounded bg-gray-100 dark:bg-gray-700">Dashboard</a>
                                <!-- include user name here -->
                                <span class="text-sm">{{ Auth::user()->name }}</span>
                                <!-- include log out link here -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm px-3 py-1 border rounded">Log out</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm px-3 py-1">Log in</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm px-3 py-1 border rounded">Register</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-6">
            @yield('content')
        </main>

        <footer class="container mx-auto px-4 py-6 text-sm text-gray-500">
            <div class="flex gap-3">
                <a href="{{ url('/') }}" class="px-4 py-2 border rounded">Home</a> | 
                <a href="{{ route('seasons.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">View Seasons</a>
            </div>
            <div class="flex justify-between items-center">
                <div>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</div>
            </div>
        </footer>
    </body>
</html>
