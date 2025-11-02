@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Welcome</h1>

    <p class="mb-4 text-gray-700 ">Welcome to the Viking Pool League.</p>
</div>

<div class="container mx-auto py-4">
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-3">Fixtures (grouped by date → competition)</h2>

        @if(isset($fixtures) && $fixtures->count())
            @php
                // Group fixtures first by date (Y-m-d) then by competition name
                $byDate = $fixtures->groupBy(function ($g) {
                    return $g->date?->format('Y-m-d') ?? 'TBA';
                });
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-600">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Competition</th>
                            <th class="px-3 py-2">Home</th>
                            <th class="px-3 py-2">Away</th>
                            <th class="px-3 py-2">Status / Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byDate as $date => $dateFixtures)
                            @php
                                // Further group by competition name (fallback to '-')
                                $byCompetition = $dateFixtures->groupBy(fn($g) => $g->competition?->name ?? '-');
                            @endphp
                            <tr class="bg-gray-100">
                                <td class="px-3 py-2 font-medium" colspan="5">{{ $date }}</td>
                            </tr>

                            @foreach($byCompetition as $competitionName => $compFixtures)
                                <tr class="bg-gray-50">
                                    <td></td>
                                    <td class="px-3 py-2 font-semibold" colspan="4">{{ $competitionName }}</td>
                                </tr>

                                @foreach($compFixtures as $game)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">{{ $game->date?->format('Y-m-d H:i') ?? 'TBA' }}</td>
                                        <td class="px-3 py-2">{{ $game->competition?->name ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            @if($game->homeTeam)
                                                {{ $game->homeTeam->name }}
                                            @else
                                                {{ $game->homePlayer?->name ?? '—' }}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($game->awayTeam)
                                                {{ $game->awayTeam->name }}
                                            @else
                                                {{ $game->awayPlayer?->name ?? '—' }}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($game->period === 'played')
                                                @if(!is_null($game->home_score) || !is_null($game->away_score))
                                                    {{ $game->home_score ?? 0 }} - {{ $game->away_score ?? 0 }}
                                                @else
                                                    Final
                                                @endif
                                            @else
                                                {{ (is_null($game->home_score) && is_null($game->away_score)) ? 'TBD' : ($game->home_score . ' - ' . $game->away_score) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No fixtures in the selected windows.</p>
        @endif
    </div>
</div>
</div>
@endsection
