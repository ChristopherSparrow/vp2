@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $season->name }}</h1>

    <dl>
        <dt class="font-semibold">Start Date</dt>
        <dd class="mb-2">{{ $season->start_date->toDateString() }}</dd>

        <dt class="font-semibold">End Date</dt>
        <dd class="mb-2">{{ $season->end_date->toDateString() }}</dd>

        <dt class="font-semibold">Current</dt>
        <dd class="mb-2">{{ $season->current ? 'Yes' : 'No' }}</dd>
    </dl>

    <div class="mt-4">
        <a href="{{ route('seasons.edit', $season) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('seasons.index') }}" class="ml-2">Back</a>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Teams in this Season</h2>

        @if($season->teams->isEmpty())
            <div class="text-gray-600">No teams for this season.</div>
        @else
            <div class="overflow-x-auto border border-white-300 mt-2">
                <table class="min-w-full w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Location</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($season->teams as $team)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $team->name }}</td>
                            <td class="px-4 py-2">{{ $team->location }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="px-4 py-3">

                                @php
                                    // Prefer playerTeams so we can inspect soft deletes on the pivot records.
                                    if ($team->relationLoaded('playerTeams')) {
                                        $activeAssignments = $team->playerTeams->whereNull('deleted_at');
                                    } else {
                                        // if not loaded, query only non-deleted playerTeams
                                        $activeAssignments = $team->playerTeams()->whereNull('deleted_at')->with('player')->get();
                                    }
                                @endphp

                                @if($activeAssignments->isEmpty())
                                    <div class="text-gray-600">No players for this team.</div>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full w-full text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="px-3 py-2 text-left">Name</th>
                                                    <th class="px-3 py-2 text-left">Position</th>
                                                    <th class="px-3 py-2 text-left">Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeAssignments as $assign)
                                                    @php $player = $assign->player ?? $assign; @endphp
                                                    <tr class="border-t">
                                                        <td class="px-3 py-2">{{ $player->name }}</td>
                                                        <td class="px-3 py-2">{{ $player->position ?? '' }}</td>
                                                        <td class="px-3 py-2">{{ $player->number ?? '' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
