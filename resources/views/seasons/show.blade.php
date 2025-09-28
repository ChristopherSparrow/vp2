@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $season->name }} Season</h1>
    <p>
        {{ $season->start_date->format('F j, Y') }} - {{ $season->end_date->format('F j, Y') }}.
        @if($season->current)
            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
                Current
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                <svg class="w-3 h-3 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
                Not current
            </span>
        @endif
    </p>


    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Competitions in this Season</h2>

        @if($season->relationLoaded('competitions'))
            @php $competitions = $season->competitions; @endphp
        @else
            @php $competitions = $season->competitions()->orderBy('name')->get(); @endphp
        @endif

        @if($competitions->isEmpty())
            <div class="text-gray-600">No competitions for this season.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                @foreach($competitions as $competition)
                    <div class="border rounded-lg p-4 shadow-sm bg-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $competition->name }}</h3>
                                <p class="text-sm text-gray-600">Type: {{ ucfirst(str_replace('_', ' ', $competition->type)) }}</p>
                            </div>
                            <div class="text-sm text-gray-500">{{ $competition->created_at?->toDateString() }}</div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('competitions.show', $competition) }}" class="text-blue-600">View</a>
                            <a href="{{ route('competitions.edit', $competition) }}" class="ml-3 text-yellow-600">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Teams in this Season</h2>

        @if($season->teams->isEmpty())
            <div class="text-gray-600">No teams for this season.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
            @foreach($season->teams as $team)
                <div class="border rounded-lg p-4 shadow-sm bg-white">
                <div class="flex justify-between items-start">
                    <div>
                    <h3 class="text-lg font-semibold">{{ $team->name }}</h3>
                    <p class="text-sm text-gray-600">Location: {{ $team->location }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    <h4 class="font-semibold text-sm mb-2">Players</h4>
                    @php
                    if ($team->relationLoaded('playerTeams')) {
                        $activeAssignments = $team->playerTeams->whereNull('deleted_at');
                    } else {
                        $activeAssignments = $team->playerTeams()->whereNull('deleted_at')->with('player')->get();
                    }
                    @endphp

                    @if($activeAssignments->isEmpty())
                    <div class="text-gray-600">No players for this team.</div>
                    @else
                    <div>
                        @foreach($activeAssignments as $assign)
                        @php $player = $assign->player ?? $assign; @endphp
                        <div class="border rounded mb-2 p-2 bg-gray-50">
                            <div class="flex items-center justify-between">
                            <span class="font-medium">{{ $player->name }}</span>
                            <span class="text-xs text-gray-500">#{{ $player->number ?? '-' }}</span>
                            </div>
                            <div class="text-xs text-gray-600">Position: {{ $player->position ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                </div>
            @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
