@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-semibold mb-4">Frame {{ $frame->id }}</h1>

        <div class="bg-white border rounded p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Game</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->game->competition->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Frame No</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->game_no }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Home Player</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->homePlayer->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Away Player</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->awayPlayer->name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Score</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->home_score ?? '-' }} — {{ $frame->away_score ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">8-ball Clear (Home)</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->eight_ball_clear_home ? 'Yes' : 'No' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">8-ball Clear (Away)</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->eight_ball_clear_away ? 'Yes' : 'No' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Home Game No</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->home_game_no }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Away Game No</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $frame->away_game_no }}</dd>
                </div>
            </dl>

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
