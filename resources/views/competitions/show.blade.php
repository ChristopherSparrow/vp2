@extends('layouts.app')

@section('content')
<div class="container mx-auto py-0">
    {{-- Styles moved to `resources/css/app.css` (details marker hiding, chevron rotation, responsive behavior) --}}
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


        @php
            // Show fixtures for this competition (no pagination) and we'll group them by date in the view
            $games = $competition->games()->with(['homeTeam', 'awayTeam', 'competition'])->orderBy('date')->get();
        @endphp

        @if($games->isEmpty())
            <div class="text-gray-600">No fixtures for this competition.</div>
        @else
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @if(!empty($standings) && $competition->type === 'team_league')
                <div class="mb-6">
                    <details class="section-panel details-no-marker rounded border bg-white" open>
                        <summary class="px-4 py-2 font-semibold cursor-pointer flex items-center justify-between">
                            <span class="text-xl font-bold">League Standings</span>
                            <svg class="chev w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="p-4 overflow-x-auto">
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
                    </details>
                </div>
            @endif
            <details class="section-panel details-no-marker mb-4 rounded border bg-white" open>
                <summary class="px-4 py-2 font-semibold cursor-pointer flex items-center justify-between">
                    <span class="text-xl font-bold">Fixtures & Results</span>
                    <svg class="chev w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="p-4 mt-4 space-y-3">
                @foreach($games->groupBy(fn($g) => $g->date?->format('Y-m-d') ?? 'TBA') as $date => $dayGames)
                    <details class="details-no-marker border rounded bg-gray-50">
                        <summary class="px-4 py-2 font-semibold cursor-pointer flex items-center justify-between">
                            <span>
                                @if($date === 'TBA')
                                    To be announced
                                @else
                                    {{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $date)->format('l, j M Y') }}
                                @endif
                            </span>
                            <span class="text-sm text-gray-500">{{ $dayGames->count() }} fixture{{ $dayGames->count() === 1 ? '' : 's' }}</span>
                        </summary>

                        <div class="p-4 space-y-3">
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
                    </details>
                @endforeach
                </div>
            </details>
            <details class="section-panel details-no-marker mb-6 rounded border bg-white" open>
                <summary class="px-4 py-2 font-semibold cursor-pointer flex items-center justify-between">
                    <span class="text-xl font-bold">Statistics</span>
                    <svg class="chev w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

            @php
                // Determine the season to use: prefer the current season if available,
                // otherwise fall back to the competition's season (if present).
                $season = $currentSeason ?? $competition->season;

                // Build a DB-level query to include frames that are the first game for a player
                // and where that player actually won the frame. Do this at DB level to avoid
                // PHP-side type/coercion issues and to include both home and away winners.
                $firstFrameWinners = collect();
                try {
                    if ($season && $competition->getKey()) {
                        $firstFrameWinners = \App\Models\Frame::with(['game.homeTeam', 'game.awayTeam', 'game.homePlayer', 'game.awayPlayer', 'homePlayer', 'awayPlayer'])
                            ->whereHas('game', function ($q) use ($competition) {
                                $q->where('competition_id', $competition->getKey());
                            })
                            ->where(function ($q) {
                                // home_game_no == 1 AND home_score > away_score
                                $q->where(function ($q2) {
                                    $q2->where('home_game_no', 1)
                                        ->whereColumn('home_score', '>', 'away_score');
                                })
                                // OR away_game_no == 1 AND away_score > home_score
                                ->orWhere(function ($q2) {
                                    $q2->where('away_game_no', 1)
                                        ->whereColumn('away_score', '>', 'home_score');
                                });
                            })
                            ->orderByDesc('created_at')
                            ->get();
                    }
                } catch (\Exception $e) {
                    $firstFrameWinners = collect();
                }
            @endphp

            <div class="p-4 mt-4">


                
                @php
                    // Aggregate wins per player from the $firstFrameWinners collection.
                    $playerWins = [];
                    foreach ($firstFrameWinners as $frame) {
                        // Determine the winning player for this frame (we fetched only winning frames)
                        $winner = null;
                        if ($frame->home_game_no === 1 && $frame->home_score > $frame->away_score) {
                            $winner = $frame->homePlayer;
                        } elseif ($frame->away_game_no === 1 && $frame->away_score > $frame->home_score) {
                            $winner = $frame->awayPlayer;
                        }

                        if (!$winner) {
                            continue;
                        }

                        $pid = $winner->getKey();
                        if (!isset($playerWins[$pid])) {
                            $playerWins[$pid] = ['id' => $pid, 'name' => $winner->name ?? 'Unknown', 'wins' => 0];
                        }
                        $playerWins[$pid]['wins']++;
                    }

                    // Convert to collection and sort by wins desc
                    $playerWins = collect($playerWins)->sortByDesc('wins')->values();
                @endphp

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">First-game winners summary</h3>
                    @if($playerWins->isEmpty())
                        <div class="text-gray-600">No players found for the first-game winner criteria.</div>
                    @else
                        <div class="overflow-x-auto bg-white border rounded">
                            <table class="min-w-full text-left">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2">Player</th>
                                        <th class="px-3 py-2">Wins</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($playerWins as $p)
                                        <tr class="border-t">
                                            <td class="px-3 py-2">{{ $p['name'] }}</td>
                                            <td class="px-3 py-2">{{ $p['wins'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </details>
            </div>
        @endif
    </div>
</div>
@endsection
