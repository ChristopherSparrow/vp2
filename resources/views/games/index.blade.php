@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Games</h1>
        <a href="{{ route('games.create') }}" class="btn">New Game</a>
    </div>

    @if(isset($groupedGames) && $groupedGames->isNotEmpty())
        @foreach($groupedGames as $seasonName => $competitions)
            <h2 class="text-xl font-semibold mt-6">{{ $seasonName }}</h2>

            @foreach($competitions as $competitionName => $compGames)
                <h3 class="text-lg font-medium mt-4">{{ $competitionName }}</h3>

                <table class="min-w-full bg-white border border-gray-300">

                    <tbody>
                        @foreach($compGames as $game)
                        <tr>
                            <td colspan="3" class="border border-gray-300 px-2 py-1">{{ optional($game->date)->format('j/m/Y') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-2 py-1">
                            {{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}<br>
                            {{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                            {{ $game->home_score ?? '' }}<br>
                            {{ $game->away_score ?? '' }}
                            </td>



                            <td class="border border-gray-300 px-2 py-1">
                                @if(!empty($game->getKey()))
                                    <a href="{{ route('games.edit', $game->getKey()) }}" class="ml-2">Edit</a>
                                @else
                                    <span class="text-gray-500 ml-2">Edit</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endforeach
    @else
        <p class="mt-4">No games found.</p>
    @endif

    <div class="mt-4">{{ $games->links() }}</div>
</div>
@endsection
