@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold">Game Details</h1>

    <div class="mt-4">
        <p><strong>Competition:</strong> {{ $game->competition->name ?? '—' }}</p>
        <p><strong>Home:</strong> {{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}</p>
        <p><strong>Away:</strong> {{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}</p>
        <p><strong>Score:</strong> {{ $game->home_score ?? '—' }} - {{ $game->away_score ?? '—' }}</p>
        <p><strong>Date:</strong> {{ optional($game->date)->toDateTimeString() ?? '—' }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('games.edit', $game) }}" class="btn">Edit</a>
        <form action="{{ route('games.destroy', $game) }}" method="POST" style="display:inline-block">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Delete this game?')">Delete</button>
        </form>
    </div>
</div>
@endsection
