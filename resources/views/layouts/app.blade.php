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
            <h1 class="text-2xl font-bold mb-4">the <strong>Viking Pool League</strong></h1>

            @yield('content')
        </main>

        <footer class="container mx-auto px-4 py-6 text-sm text-gray-500">

                <a href="{{ url('/') }}">Home</a> | 
                <a href="{{ route('seasons.index') }}" >View Seasons</a> |
                <a href="{{ route('teams.index') }}" >View Teams</a> |
                <a href="{{ route('players.index') }}" >View Players</a> |
                <a href="{{ route('competitions.index') }}" >View Competitions</a> |
                <a href="{{ route('games.index') }}" >View Games</a> |
                <a href="{{ route('frames.index') }}" >View Frames</a> |

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
                    <a href="{{ route('seasons.show', $currentSeason) }}">Current Season</a>
                @endif

            
            <div class="flex justify-between items-center">
                <div>&copy; {{ date('Y') }} Chris Sparrow - {{ config('app.name', 'Laravel') }}</div>
            </div>
        </footer>
        {{-- Render any pushed scripts from child views (e.g. page-specific JS) --}}
        @stack('scripts')
    </body>
</html>
