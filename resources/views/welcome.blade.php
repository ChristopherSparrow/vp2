@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Welcome</h1>
    <p class="mb-4 text-gray-700 ">Welcome to the Viking Pool League.</p>
</div>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">News</h3>
                    <p>Placeholder text</p>
                </div>
            </div>
     </div>       

        <div class="border rounded-lg p-4 shadow-sm bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Fixtures / Results</h3><br>
                        @if(isset($fixtures) && $fixtures->count())
                        @php
                            // Group fixtures first by date (Y-m-d) then by competition name
                            $byDate = $fixtures->groupBy(function ($g) {
                                return $g->date?->format('Y-m-d') ?? 'TBA';
                            });
                        @endphp

                        
                            <div class="overflow-x-auto">
                                <table class="w-full table-full text-sm text-left">
                                    <colgroup>
                                        <col class="w-full" />
                                        <col class="w-60" />
                                    </colgroup>
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
                                                            {{ $game->homeTeam->name }}
                                                        @else
                                                            {{ $game->homePlayer?->name ?? '—' }}
                                                        @endif
                                                    <br>
                                                        @if($game->awayTeam)
                                                            {{ $game->awayTeam->name }}
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
                    <h3 class="text-lg font-semibold">News</h3>
                    <p>Placeholder text</p>
                </div>
            </div>
     </div>       
    </div>



</div>
@endsection
