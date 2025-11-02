@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold"></h2>{{ $game->competition->name ?? '—' }}</h2>
                    <p class="text-sm text-gray-600">{{ optional($game->date)->format('F j, Y g:ia') ?? '—' }}</p>
                </div>

            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <div class="font-medium flex items-center justify-between">
                        <span>{{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}</span>
                        <span class="text-xl font-bold text-right">{{ $game->home_score ?? '—' }}</span>
                    </div>
                </div>
            
                <div class="bg-gray-50 p-3 rounded">
                    
                    <div class="font-medium flex items-center justify-between">
                        <span>{{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}</span>
                        <span class="text-xl font-bold text-right">{{ $game->away_score ?? '—' }}</span>
                    </div>
                </div>
            </div>

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

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Frames</h2>

        @php
            // If frames are preloaded, use them; otherwise fetch ordered by game_no
            if ($game->relationLoaded('frames')) {
                $frames = $game->frames->sortBy('game_no');
            } else {
                $frames = $game->frames()->orderBy('game_no')->get();
            }
        @endphp

        @if($frames->isEmpty())
            <div class="text-gray-600">No frames for this game.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($frames as $frame)
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Frame {{ $frame->game_no ?? '—' }}</div>
                                <div class="font-medium">{{ $frame->homePlayer->name ?? '—' }} ({{ $frame->home_score ?? '-' }})</div>
                                <div class="text-sm text-gray-600">{{ $frame->awayPlayer->name ?? '—' }} ({{ $frame->away_score ?? '-' }})</div>
                            </div>
                            <div class="text-right text-sm">
                                <div>{{ $frame->eight_ball_clear_home ? '8-ball clear (H)' : '' }}</div>
                                <div>{{ $frame->eight_ball_clear_away ? '8-ball clear (A)' : '' }}</div>
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
@endsection
