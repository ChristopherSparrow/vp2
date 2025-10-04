@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, <span class="font-medium">{{ $user->name }}</span> — <span class="text-gray-500">{{ $user->email }}</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="text-lg font-semibold">Account</h2>
                <p class="text-sm text-gray-600 mt-2">Manage your account and session.</p>
                <div class="mt-4 flex items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded text-sm">Logout</button>
                    </form>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="text-lg font-semibold">Quick Links</h2>
                <p class="text-sm text-gray-600 mt-2">Jump to common management pages.</p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a href="{{ route('games.index') }}" class="block px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700">Games</a>
                    <a href="{{ route('players.index') }}" class="block px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700">Players</a>
                    <a href="{{ route('teams.index') }}" class="block px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700">Teams</a>
                    <a href="{{ route('seasons.index') }}" class="block px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700">Seasons</a>
                    <a href="{{ route('competitions.index') }}" class="block px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700 col-span-1 sm:col-span-2">Competitions</a>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white border rounded-lg p-4 shadow-sm">
            <h2 class="text-lg font-semibold">Overview</h2>
            <p class="text-sm text-gray-600 mt-2">Use the links above to manage games, players, teams, seasons and competitions.</p>
        </div>
    </div>
</div>
@endsection
