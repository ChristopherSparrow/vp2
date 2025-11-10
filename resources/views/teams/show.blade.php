@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <div class="border rounded-lg p-4 shadow-sm bg-white">
                <div>
                    <h3 class="text-lg font-semibold">{{ $team->name }}</h3>
                    <p>{{ $team->location }}</p>
                    <p> {{ $team->season?->name }} Season</p>
                </div>
        </div>

        <div class="border rounded-lg p-4 shadow-sm bg-white">
                <div>
                    <h3 class="text-lg font-semibold">Players</h3>
                    @if(isset($playerTeams) && $playerTeams->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm  text-left">

                            <tbody>
                                @foreach($playerTeams as $pt)
                                    @php $p = $pt->player; @endphp
                                    <tr class="border-t">
                                        <td class="px-2 py-2">{{ $p?->name ?? '—' }}</td>
                                        <td class="px-2 py-2">{{ $p?->phone ?? '—' }}</td>
                                        <td class="px-2 py-2">{{ $pt->start_date?->format('d/m') ?? '—' }}</td>
                                        <td class="px-2 py-2">{{ $pt->end_date?->format('d/m') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-gray-500">No players recorded for this team in the selected season.</p>
                    @endif
                </div>
        </div>

        <div class="border rounded-lg p-4 shadow-sm bg-white"> 
                <div>
                    <h3 class="text-lg font-semibold">Fixtures & Results</h3>
                    @if(isset($fixtures) && $fixtures->count())
                        @php
                            $byDate = $fixtures->groupBy(function ($g) {
                                return $g->date?->format('Y-m-d') ?? 'TBA';
                            });
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="w-full  text-sm text-left">
                                <tbody>
                                @foreach($byDate as $date => $dateFixtures)
                                    @php
                                        $byCompetition = $dateFixtures->groupBy(fn($g) => $g->competition?->name ?? '-');
                                        $displayDate = $date === 'TBA'
                                            ? 'TBA'
                                            : \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $date)->format('l d F');
                                    @endphp

                                    <tr class="bg-gray-100">
                                        <td class="px-0 py-2" colspan="2"><strong>{{ $displayDate }}</strong></td>
                                    </tr>

                                    @foreach($byCompetition as $competitionName => $compFixtures)
                                        <tr ><td class="px-3 py-2" ><strong>{{ $competitionName }}</strong></td></tr>

                                        @foreach($compFixtures as $game)
                                            @php
                                                $homeName = $game->homeTeam?->name ?? $game->homePlayer?->name ?? '—';
                                                $awayName = $game->awayTeam?->name ?? $game->awayPlayer?->name ?? '—';
                                                $homeIsTeam = $game->homeTeam && $game->homeTeam->getKey() == $team->getKey();
                                                $awayIsTeam = $game->awayTeam && $game->awayTeam->getKey() == $team->getKey();
                                                $scoreExists = $game->home_score !== null || $game->away_score !== null;
                                            @endphp

                                            <tr class="border-t">
                                                <td class="px-3 py-2 break-words">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <div class="text-sm">
                                                                <span class="{{ $homeIsTeam ? 'font-semibold' : '' }}">{{ $homeName }}</span>
                                                                &nbsp;
                                                                @if($scoreExists)
                                                                    <span class="font-medium">{{ $game->home_score ?? 0 }} - {{ $game->away_score ?? 0 }}</span>
                                                                @else
                                                                    <span class="text-gray-500">-</span>
                                                                @endif
                                                                &nbsp;
                                                                <span class="{{ $awayIsTeam ? 'font-semibold' : '' }}">{{ $awayName }}</span>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach

                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else
                        <p class="text-gray-500">No fixtures or results for this team.</p>
                    @endif
                </div>
        </div>

    </div>
</div>
@endsection
