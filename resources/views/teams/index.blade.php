@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Teams</h1>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">New Team</a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="space-y-8">
        @if(isset($seasons) && $seasons->isNotEmpty())
            @foreach($seasons as $season)
                <div class="overflow-x-auto border border-white-300 p-4 rounded bg-white">
                    <h2 class="text-lg font-semibold mb-3">{{ $season->name }} <span class="text-sm text-gray-500">({{ $season->start_date->format('F j, Y') }} - {{ $season->end_date->format('F j, Y') }})</span></h2>
                    @php
                        $seasonTeams = $season->relationLoaded('teams') ? $season->teams : $season->teams()->orderBy('name')->get();
                    @endphp

                    @if($seasonTeams->isEmpty())
                        <div class="text-gray-600">No teams for this season.</div>
                    @else
                        <table class="min-w-full w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2"></th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seasonTeams as $team)
                                    <tr class="border-t">
                                        <td class="px-4 py-2"><a href="{{ route('teams.show', $team) }}" class="text-blue-600">{{ $team->name }}</a></td>
                                            <td class="px-4 py-2">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('teams.edit', $team) }}" class="text-yellow-600">Edit</a>
                                                <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline-flex">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach

            {{-- Teams without a season --}}
            @php
                $noSeasonTeams = collect($teams)->filter(fn($t) => !$t->season_id);
            @endphp
            @if($noSeasonTeams->isNotEmpty())
                <div class="overflow-x-auto border border-white-300 p-4 rounded bg-white">
                    <h2 class="text-lg font-semibold mb-3">No Season</h2>
                    <table class="min-w-full w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2"></th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($noSeasonTeams as $team)
                                <tr class="border-t">
                                    <td class="px-4 py-2"><a href="{{ route('teams.show', $team) }}" class="text-blue-600">{{ $team->name }}</a></td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('teams.edit', $team) }}" class="text-yellow-600">Edit</a>
                                            <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            {{-- Fallback: group teams by season name --}}
            @php
                $grouped = $teams->groupBy(fn($t) => $t->season?->name ?? 'No Season');
            @endphp

            @foreach($grouped as $seasonName => $group)
                <div class="overflow-x-auto border border-white-300 p-4 rounded bg-white">
                    <h2 class="text-lg font-semibold mb-3">{{ $seasonName }}</h2>
                    <table class="min-w-full w-full">

                        <tbody>
                            @foreach($group as $team)
                                <tr class="border-t">
                                    <td class="px-4 py-2"><a href="{{ route('teams.show', $team) }}" class="text-blue-600">{{ $team->name }}</a></td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('teams.edit', $team) }}" class="text-yellow-600">Edit</a>
                                            <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    <div class="mt-4">
        {{ $teams->links() }}
    </div>
</div>
@endsection
