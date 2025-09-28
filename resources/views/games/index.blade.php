@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Games</h1>
        <a href="{{ route('games.create') }}" class="btn">New Game</a>
    </div>

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th>Competition</th>
                <th>Home</th>
                <th>Away</th>
                <th>Score</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($games as $game)
            <tr>
                <td>{{ $game->competition->name ?? '—' }}</td>
                <td>{{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}</td>
                <td>{{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}</td>
                <td>{{ $game->home_score ?? '—' }} - {{ $game->away_score ?? '—' }}</td>
                <td>{{ optional($game->date)->toDateString() ?? '—' }}</td>
                <td>
                    <a href="{{ route('games.show', $game) }}">View</a>
                    <a href="{{ route('games.edit', $game) }}" class="ml-2">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $games->links() }}</div>
</div>
@endsection
