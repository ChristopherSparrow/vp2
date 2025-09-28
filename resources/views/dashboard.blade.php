@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
        <h2 class="text-2xl font-semibold mb-4">Dashboard</h2>

        <div class="mb-4">
            <p class="text-sm text-gray-600">Logged in as</p>
            <div class="mt-1 text-lg font-medium">{{ $user->name }} &middot; <span class="text-sm text-gray-500">{{ $user->email }}</span></div>
        </div>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Logout</button>
            </form>
        </div>
        
        <br>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('games.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Manage Games</a>
        </div>    

        <br>

        <div class="flex items-center gap-3">
            
            <a href="{{ route('players.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Manage Players</a>
            <a href="{{ route('teams.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Manage Teams</a>
        </div>

        <br>
        <div class="flex items-center gap-3">
            <a href="{{ route('seasons.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Manage Seasons</a>
            <a href="{{ route('competitions.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Manage Competitions</a>

        </div>
    </div>
@endsection
