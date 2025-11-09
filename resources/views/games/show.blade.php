@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold mb-3">{{ $game->competition->name ?? '—' }}</h2>
                    <p class="text-gray-600">{{ optional($game->date)->format('F j, Y') ?? '—' }}</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <div class="font-bold flex items-center justify-between">
                        <span>{{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}</span>
                        <span class="text-xl font-bold text-right">{{ $game->home_score ?? '—' }}</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded">  
                    <div class="font-bold flex items-center justify-between">
                        <span>{{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}</span>
                        <span class="text-xl font-bold text-right">{{ $game->away_score ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div>
                <h2 class="text-xl font-bold mb-3">Frames</h2>

                @if(empty($frames) || $frames->isEmpty())
                    <p class="text-gray-600">No frames for this game.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($frames as $frame)
                            <div class="border rounded p-3 bg-gray-50 text-sm">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="text-xs text-gray-500">Frame {{ $frame->game_no ?? '—' }}</div>
                                            <div class="flex items-center space-x-2">


                                            <span class="text-xs text-gray-500">#{{ $frame->home_appearance_number ?? 0 }}</span>
                                            <span class="{{ (isset($frame->home_score) && isset($frame->away_score) && $frame->home_score > $frame->away_score) ? 'font-bold' : '' }}">{{ $frame->homePlayer->name ?? '—' }} ({{ $frame->home_score ?? '-' }})</span>
                                            @if($frame->eight_ball_clear_home)
                                                <span title="8-ball clear (A)" aria-label="8-ball clear" class="inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 inline-block" role="img" aria-hidden="true">
                                                        <title>8-ball</title>
                                                        <circle cx="12" cy="12" r="10" fill="#000" />
                                                        <text x="12" y="16" text-anchor="middle" font-size="12" fill="#fff" font-family="Arial, Helvetica, sans-serif">8</text>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">#{{ $frame->away_appearance_number ?? 0 }}</span>
                                            <span class="{{ (isset($frame->home_score) && isset($frame->away_score) && $frame->away_score > $frame->home_score) ? 'font-bold' : '' }}">{{ $frame->awayPlayer->name ?? '—' }}  ({{ $frame->away_score ?? '-' }})</span>
                                            @if($frame->eight_ball_clear_away)
                                                <span title="8-ball clear (A)" aria-label="8-ball clear" class="inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 inline-block" role="img" aria-hidden="true">
                                                        <title>8-ball</title>
                                                        <circle cx="12" cy="12" r="10" fill="#000" />
                                                        <text x="12" y="16" text-anchor="middle" font-size="12" fill="#fff" font-family="Arial, Helvetica, sans-serif">8</text>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right font-bold">

                                        <div class="mt-2">
                                            <a href="{{ route('frames.show', $frame) }}" class="text-green-600">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('frames.create') }}?game_id={{ $game->id }}" class="px-3 py-2 bg-green-600 text-white rounded">Add Frame</a>
                </div>
            </div>
        </div>


        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold mb-3">Actions</h2>
                <div class="mt-6 flex space-x-2">
                    <a href="{{ route('games.edit', $game) }}" class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>

                <form action="{{ route('games.destroy', $game) }}" method="POST" onsubmit="return confirm('Delete this game?');">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-2 bg-red-600 text-white rounded">Delete</button>
                </form>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
