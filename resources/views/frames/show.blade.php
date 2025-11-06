@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-1">

        @if($frame->game)
            <div class="mb-4 p-4 bg-gray-50 border rounded">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-sm text-gray-600">
                            {{ optional($frame->game->date)->format('j M Y') ?? 'TBA' }} · {{ $frame->game->competition->name ?? '—' }}
                        </div>
                         <div class="text-lg font-semibold">
                            <a href="{{ route('games.show', $frame->game->getKey()) }}" class="text-blue-600 hover:underline">
                                {{ $frame->game->homeTeam->name ?? $frame->game->homePlayer->name ?? '—' }} ({{ $frame->game->home_score ?? '-' }})
                                <br>
                                {{ $frame->game->awayTeam->name ?? $frame->game->awayPlayer->name ?? '—' }} ({{ $frame->game->away_score ?? '-' }})
                            </a>
                        </div>
                                                @if(!empty($frame->game->homeTeam?->location))
                            <div class="text-xs text-gray-500 mt-1">{{ $frame->game->homeTeam->location }}</div>
                        @endif
                    </div>

                </div>
            </div>
        @endif

        <div class="bg-white border rounded p-4">
            <p>Frame: {{ $frame->game_no }}</p>
            <p>#{{ $frame->away_game_no }} {{ $frame->homePlayer->name ?? '—' }} ({{ $frame->home_score ?? '-' }}) @if($frame->eight_ball_clear_home)
                                                <span title="8-ball clear (A)" aria-label="8-ball clear" class="inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 inline-block" role="img" aria-hidden="true">
                                                        <title>8-ball</title>
                                                        <circle cx="12" cy="12" r="10" fill="#000" />
                                                        <text x="12" y="16" text-anchor="middle" font-size="12" fill="#fff" font-family="Arial, Helvetica, sans-serif">8</text>
                                                    </svg>
                                                </span>
                                            @endif<br>
                #{{ $frame->away_game_no }} {{ $frame->awayPlayer->name ?? '—' }} ({{ $frame->away_score ?? '-' }})  @if($frame->eight_ball_clear_away)
                                                <span title="8-ball clear (B)" aria-label="8-ball clear" class="inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 inline-block" role="img" aria-hidden="true">
                                                        <title>8-ball</title>
                                                        <circle cx="12" cy="12" r="10" fill="#000" />
                                                        <text x="12" y="16" text-anchor="middle" font-size="12" fill="#fff" font-family="Arial, Helvetica, sans-serif">8</text>
                                                    </svg>
                                                </span>
                                            @endif</p>



            <div class="mt-6 flex space-x-2">
                <a href="{{ route('frames.edit', $frame) }}" class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>

                <form action="{{ route('frames.destroy', $frame) }}" method="POST" onsubmit="return confirm('Delete frame?');">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-2 bg-red-600 text-white rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
