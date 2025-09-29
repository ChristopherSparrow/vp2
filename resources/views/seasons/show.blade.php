@extends('layouts.app')

@section('content')

@push('scripts')
    <script>
        
        (function() {
            var btn = document.getElementById('toggle-teams');
            var list = document.getElementById('teams-list');
            if (!btn || !list) return;

            function setState(open) {
                if (open) {
                    list.classList.remove('hidden');
                    btn.textContent = 'Hide Teams';
                    btn.setAttribute('aria-expanded', 'true');
                } else {
                    list.classList.add('hidden');
                    btn.textContent = 'Show Teams';
                    btn.setAttribute('aria-expanded', 'false');
                }
            }

            // Initialize state: default hidden
            setState(false);

            btn.addEventListener('click', function(e) {
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                setState(!expanded);
            });
        })();
    </script>
@endpush
<div class="container mx-auto py-0">
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
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold mb-3">Competitions</h2>
  
        
        </div>
        @if($season->relationLoaded('competitions'))
            @php $competitions = $season->competitions; @endphp
        @else
            @php $competitions = $season->competitions()->orderBy('name')->get(); @endphp
        @endif

        @if($competitions->isEmpty())
            <div class="text-gray-600">No competitions for this season.</div>
        @else
            <div id="competitions-list" >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                @foreach($competitions as $competition)
                    <div class="border rounded-lg p-4 shadow-sm bg-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $competition->name }}</h3>
                            </div>

                        </div>
                        

                        <div class="mt-2">
                   
                            @php
                                // Per-competition pagination: show 3 games per page.
                                $perPage = 3;
                                $pageName = 'page_' . $competition->id;

                                if ($competition->relationLoaded('games')) {
                                    // If games are preloaded as a collection, sort and manually paginate.
                                    $collection = $competition->games->sortBy('date')->values();
                                    $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);
                                    $items = $collection->forPage($currentPage, $perPage);
                                    $games = new \Illuminate\Pagination\LengthAwarePaginator(
                                        $items,
                                        $collection->count(),
                                        $perPage,
                                        $currentPage,
                                        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => $pageName]
                                    );
                                } else {
                                    // When not preloaded, use query pagination with a unique page name per competition.
                                    $games = $competition->games()->orderBy('date')->paginate($perPage, ['*'], $pageName);
                                }
                            @endphp
                            @if($games->total() == 0)
                                <div class="text-gray-600">No games for this competition in this season.</div>
                            @else
                            
                            <div>
                                @if(optional($competition)->type === 'team_league')
                                    @php
                                        // Build standings from completed team-vs-team games
                                        $s_games = $competition->games()->with(['homeTeam', 'awayTeam'])->get();
                                        $table = [];

                                        $ensureTeam = function ($team) use (&$table) {
                                            if (!$team) return;
                                            $id = $team->getKey();
                                            if (!isset($table[$id])) {
                                                $table[$id] = [
                                                    'team_id' => $id,
                                                    'name' => $team->name,
                                                    'played' => 0,
                                                    'wins' => 0,
                                                    'draws' => 0,
                                                    'losses' => 0,
                                                    'for' => 0,
                                                ];
                                            }
                                        };

                                        foreach ($s_games as $g) {
                                            if (!$g->homeTeam || !$g->awayTeam) continue;
                                            if ($g->home_score === null || $g->away_score === null) continue;

                                            $ensureTeam($g->homeTeam);
                                            $ensureTeam($g->awayTeam);

                                            $hid = $g->homeTeam->getKey();
                                            $aid = $g->awayTeam->getKey();

                                            $table[$hid]['played']++;
                                            $table[$aid]['played']++;

                                            $table[$hid]['for'] += $g->home_score;
                                            $table[$aid]['for'] += $g->away_score;

                                            if ($g->home_score > $g->away_score) {
                                                $table[$hid]['wins']++;
                                                $table[$aid]['losses']++;
                                            } elseif ($g->home_score < $g->away_score) {
                                                $table[$aid]['wins']++;
                                                $table[$hid]['losses']++;
                                            } else {
                                                $table[$hid]['draws']++;
                                                $table[$aid]['draws']++;
                                            }
                                        }

                                        $standings = array_values($table);
                                        usort($standings, function ($a, $b) {
                                            if ($a['for'] !== $b['for']) return $b['for'] <=> $a['for'];
                                            if ($a['wins'] !== $b['wins']) return $b['wins'] <=> $a['wins'];
                                            return $a['played'] <=> $b['played'];
                                        });
                                    @endphp

                                    @if(!empty($standings))
                                        <div class="mb-3">

                                            <div class="overflow-x-auto">
                                                <table class="min-w-full bg-white border">
                                                    <thead>
                                                        <tr class="bg-gray-100 text-left">

                                                            <th class="px-2 py-1"> </th>
                                                            <th class="px-2 py-1">P</th>
                                                            <th class="px-2 py-1">W</th>
                                                            <th class="px-2 py-1">D</th>
                                                            <th class="px-2 py-1">L</th>
                                                            <th class="px-2 py-1">Pts</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($standings as $idx => $row)
                                                            <tr class="border-t">

                                                                <td class="px-2 py-1">{{ $row['name'] }}</td>
                                                                <td class="px-2 py-1">{{ $row['played'] }}</td>
                                                                <td class="px-2 py-1">{{ $row['wins'] }}</td>
                                                                <td class="px-2 py-1">{{ $row['draws'] }}</td>
                                                                <td class="px-2 py-1">{{ $row['losses'] }}</td>
                                                                <td class="px-2 py-1">{{ $row['for'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    <p>
                                        <a href="{{ route('competitions.stats', $competition) }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">Stats</a>
                                    </p>
                                @endif
                                <p>
                                    <a href="{{ route('competitions.show', $competition) }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">Fixtures & Results</a>
                                </p>

                                
                            </div>
                            @endif
                            
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @endif
    </div>



    <div class="mt-8">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold mb-3">Teams & Players</h2>
            <button id="toggle-teams" aria-expanded="false" class="px-3 py-1 rounded bg-white-500  border text-sm hover:bg-gray-300">Show Teams</button>
        </div>

        <div id="teams-list" class="hidden">
        @if($season->teams->isEmpty())
            <div class="text-gray-600">No teams for this season.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
            @foreach($season->teams as $team)
                <div class="border rounded-lg p-4 shadow-sm bg-white">
                <div class="flex justify-between items-start">
                    <div>
                    <h3 class="text-lg font-semibold">{{ $team->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $team->location }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    
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

                            </div>

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
</div>
@endsection
