@extends('layouts.app')

@section('content')
<div class="container mx-auto py-0">
    <h1 class="text-2xl font-bold mb-4">{{ $competition->name }}</h1>

    @php
        $currentSeason = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('seasons')) {
                $currentSeason = \App\Models\Season::where('current', true)->first();
            }
        } catch (\Exception $e) {
            $currentSeason = null;
        }
    @endphp

    <p>
        @if($currentSeason)
            <a href="{{ route('seasons.show', $currentSeason) }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">Current Season</a>
        @else
            <a href="{{ route('competitions.index') }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">Competitions</a>
        @endif
    </p>


    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Fixtures & Results</h2>

        @php
            // Show fixtures for this competition, paginated
            $perPage = 10;
            $games = $competition->games()->with(['homeTeam', 'awayTeam', 'competition'])->orderBy('date')->paginate($perPage);
        @endphp

        @if($games->total() == 0)
            <div class="text-gray-600">No fixtures for this competition.</div>
        @else
            @if(!empty($standings) && $competition->type === 'team_league')
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Standings</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-3 py-2">Team</th>
                                    <th class="px-3 py-2">P</th>
                                    <th class="px-3 py-2">W</th>
                                    <th class="px-3 py-2">D</th>
                                    <th class="px-3 py-2">L</th>
                                    <th class="px-3 py-2">Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standings as $i => $row)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">{{ $row['name'] }}</td>
                                        <td class="px-3 py-2">{{ $row['played'] }}</td>
                                        <td class="px-3 py-2">{{ $row['wins'] }}</td>
                                        <td class="px-3 py-2">{{ $row['draws'] }}</td>
                                        <td class="px-3 py-2">{{ $row['losses'] }}</td>
                                        <td class="px-3 py-2">{{ $row['for'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <div class="space-y-3">
                @foreach($games as $game)
                    <div class="border rounded p-3 bg-white">
                        <div class="text-xs text-gray-500">{{ $game->date?->toDateString() }}</div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }} ({{ $game->home_score ?? '—' }})</div>
                                <div class="text-sm text-gray-600">vs {{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }} ({{ $game->away_score ?? '—' }})</div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if(optional($game->competition)->type === 'team_league')
                                    <a href="{{ route('games.show', $game) }}" class="text-green-600 hover:text-green-800">View</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $games->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
