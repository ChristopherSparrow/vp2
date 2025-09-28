<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))@vite(['resources/css/app.css', 'resources/js/app.js'])
        @elseif (file_exists(public_path('css/app.css')))<link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endif

    </head>

    
    <body class="min-h-screen bg-gray-50 text-gray-900 ">
        <header class="bg-white  border-b">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="font-semibold text-lg">{{ config('app.name', 'Laravel') }}</a>

                </div>

                <div>
                    @if (Route::has('login'))
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm px-3 py-1 rounded bg-gray-100">Dashboard</a>
                                <span class="text-sm">{{ Auth::user()->name }}</span>
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
                <a href="{{ route('teams.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">View Teams</a>
                <a href="{{ route('players.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">View Players</a>

                @php
                    $currentSeason = null;
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('seasons')) {
                            $currentSeason = \App\Models\Season::where('current', true)->first();
                        }
                    } catch (\Exception $e) {
                        // If Schema isn't available or DB not migrated (tests), just ignore
                        $currentSeason = null;
                    }
                @endphp
                @if($currentSeason)
                    <a href="{{ route('seasons.show', $currentSeason) }}" class="px-4 py-2 bg-blue-600 text-white rounded"">Current Season</a>
                @endif

            </div>
            <div class="flex justify-between items-center">
                <div>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</div>
            </div>
        </footer>
    </body>
</html>
