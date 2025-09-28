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
                                    @foreach($games as $game)
                                        <div class="border rounded mb-2 p-2 bg-gray-50">
                                            <!-- Include the date of the Game here -->
                                            <div class="text-xs text-gray-500">{{ $game->date?->toDateString() }} (Optional Round Text to be built)</div>
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">
                                                    {{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }} ({{ $game->home_score ?? '—' }})
                                                    <br>
                                                    {{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }} ({{ $game->away_score ?? '—' }}) 
                                                </span>

                                            </div>
                                          
                                        </div>
                                    @endforeach

                                    {{-- Pagination links for this competition --}}
                                    <div class="mt-2">
                                        @if($games->lastPage() > 1)
                                            <nav class="flex items-center justify-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                                                {{-- Previous Page Link --}}
                                                @if($games->onFirstPage())
                                                    <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 text-sm">&laquo;</span>
                                                @else
                                                    <a href="{{ $games->previousPageUrl() }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">&laquo;</a>
                                                @endif

                                                {{-- Pagination Elements --}}
                                                @php
                                                    $start = max(1, $games->currentPage() - 2);
                                                    $end = min($games->lastPage(), $games->currentPage() + 2);
                                                @endphp
                                                @for($i = $start; $i <= $end; $i++)
                                                    @if($i == $games->currentPage())
                                                        <span aria-current="page" class="px-3 py-1 rounded bg-green-600 text-white text-sm">{{ $i }}</span>
                                                    @else
                                                        <a href="{{ $games->url($i) }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">{{ $i }}</a>
                                                    @endif
                                                @endfor

                                                {{-- Next Page Link --}}
                                                @if($games->hasMorePages())
                                                    <a href="{{ $games->nextPageUrl() }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">&raquo;</a>
                                                @else
                                                    <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 text-sm">&raquo;</span>
                                                @endif
                                            </nav>
                                        @endif
                                    </div>
                                </div>
                            @endif
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
