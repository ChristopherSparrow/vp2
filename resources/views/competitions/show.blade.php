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
            // Show fixtures for this competition (no pagination) and we'll group them by date in the view
            $games = $competition->games()->with(['homeTeam', 'awayTeam', 'competition'])->orderBy('date')->get();
        @endphp

        @if($games->isEmpty())
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
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($games->groupBy(fn($g) => $g->date?->format('Y-m-d') ?? 'TBA') as $date => $dayGames)
                    <div class="p-2 border rounded bg-gray-50">
                        <h4 class="font-semibold mb-2">
                            @if($date === 'TBA')
                                To be announced
                            @else
                                {{-- Use the grouped Y-m-d key to format the header consistently --}}
                                {{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $date)->format('l, j M Y') }}
                            @endif
                        </h4>

                        <div class="space-y-3">
                            @foreach($dayGames as $game)
                                <div class="border rounded p-3 bg-white">
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium">{{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }} ({{ $game->home_score ?? '—' }})</div>
                                            <div class="font-medium">{{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }} ({{ $game->away_score ?? '—' }})</div>
                                            @if(isset($game->homeTeam) && !empty($game->homeTeam->location))
                                                <div class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                    <!-- small location pin -->
                                                    <svg class="w-3 h-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 1 1 18 0z"></path>
                                                        <circle cx="12" cy="10" r="3"></circle>
                                                    </svg>
                                                    <span>{{ $game->homeTeam->location }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if(optional($game->competition)->type === 'team_league')
                                                @if(!empty($game->getKey()))
                                                    <a href="{{ route('games.show', $game->getKey()) }}" class="text-green-600 hover:text-green-800">View</a>
                                                @else
                                                    <span class="text-gray-500">View</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
