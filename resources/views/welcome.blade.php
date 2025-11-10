@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <p class="mb-4 text-gray-700 ">Welcome to the Viking Pool League the blackball pool league for Market Weighton, Pocklington and surrounding areas.</p>
</div>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">News</h3>
                </div>
            </div>

            <!-- News mockup: featured article + small list -->
            <div class="mt-3">
                <article class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-40 h-28 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                        <!-- Image placeholder -->
                        <span class="text-xs">Image</span>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-md font-semibold text-gray-800">Season Opener: New Teams Join the League</h4>
                        <div class="text-xs text-gray-500 mt-1">22 September 2025</div>
                        <p class="mt-2 text-gray-700 text-sm">We welcomed two new teams to the Viking Pool League this season. Matches kicked off with competitive fixtures across the region — see the fixtures section for details.</p>
                        <div class="mt-3">
                            <a href="#" class="text-sm text-indigo-600 hover:underline">Read more</a>
                        </div>
                    </div>
                </article>

                <hr class="my-3">

                <ul class="space-y-3">
                    <li>
                        <a href="#" class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-sm flex items-center justify-center text-gray-400 text-xs">Img</div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Cup Draw Announced</div>
                                <div class="text-xs text-gray-500">10 October 2025</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-sm flex items-center justify-center text-gray-400 text-xs">Img</div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Player of the Month</div>
                                <div class="text-xs text-gray-500">01 October 2025</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
     </div>       

        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Fixtures / Results</h3>
                        @if(isset($fixtures) && $fixtures->count())
                            @php
                                // Group fixtures first by date (Y-m-d) then by competition name
                                $byDate = $fixtures->groupBy(function ($g) {
                                    return $g->date?->format('Y-m-d') ?? 'TBA';
                                });
                            @endphp
                            <div>
                                <table class="w-full table-fixed h-full text-sm text-left">

                                    <tbody>
                                    @foreach($byDate as $date => $dateFixtures)
                                        @php
                                            $byCompetition = $dateFixtures->groupBy(fn($g) => $g->competition?->name ?? '-');
                                            $displayDate = $date === 'TBA'
                                                ? 'TBA'
                                                : \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $date)->format('l d F');
                                        @endphp
                                        <tr>
                                            <td class="px-0 py-2" colspan="2"><strong>{{ $displayDate }}</strong></td>
                                        </tr>

                                        @foreach($byCompetition as $competitionName => $compFixtures)
                                            <tr class="bg-gray-100"><td class="px-3 py-2" colspan="2"><strong>{{ $competitionName }}</strong></td></tr>

                                            @foreach($compFixtures as $game)
                                                <tr class="border-t">

                                                    <td class="px-3 py-2 break-words">
                                                        @if($game->homeTeam)
                                                            <a href="{{ route('teams.show', $game->homeTeam->getKey()) }}" class="text-indigo-600 hover:underline">{{ $game->homeTeam->name }}</a>
                                                        @else
                                                            {{ $game->homePlayer?->name ?? '—' }}
                                                        @endif
                                                    <br>
                                                        @if($game->awayTeam)
                                                            <a href="{{ route('teams.show', $game->awayTeam->getKey()) }}" class="text-indigo-600 hover:underline">{{ $game->awayTeam->name }}</a>
                                                        @else
                                                            {{ $game->awayPlayer?->name ?? '—' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                                        {{ $game->home_score ?? 0 }}<br>{{ $game->away_score ?? 0 }}
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

        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">League Table</h3>
                </div>
            </div>

            <div class="mt-3">
                @if(isset($standings) && count($standings))
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table-fixed text-left">
                            <thead>
                                <tr class="text-gray-600">
                                    <th class="px-2"></th>
                                    <th class="w-12 px-2 text-right">P</th>

                                    <th class="w-16 px-2 text-right">Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standings as $i => $row)
                                    <tr class="border-t">
                                        <td class="px-2 py-2">
                                            <a href="{{ route('teams.show', $row['team_id']) }}" class="text-indigo-600 hover:underline">{{ $row['name'] }}</a>
                                        </td>
                                        <td class="px-2 py-2 text-right">{{ $row['played'] }}</td>
                                        <td class="px-2 py-2 text-right">{{ $row['for'] }}</td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No league table available for the current season.</p>
                @endif
            </div>
     </div>       
    </div>



</div>
@endsection
