@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="mb-4">
        <a href="{{ route('players.index') }}">&larr; Back to Players</a>
    </div>

    <h1 class="text-2xl font-bold mb-2">{{ $player->name }}</h1>

    <div class="border p-4">
        <p><strong>Number:</strong> {{ $player->number ?? '-' }}</p>
        <p><strong>Created:</strong> {{ $player->created_at->toDayDateTimeString() }}</p>
    </div>

    {{-- League frames win percentage box --}}
    <div class="mt-4">
        @if(isset($leagueWinPct) && $leagueWinPct !== null)
            <div class="inline-block bg-white border border-gray-200 p-3 rounded">
                <div class="text-xs text-gray-500">League frames won</div>
                <div class="text-2xl font-bold">{{ $leagueWinPct }}%</div>
                <div class="text-sm text-gray-600">({{ $leagueFramesWon }} / {{ $leagueFramesTotal }})</div>
            </div>
        @else
            <div class="inline-block bg-white border border-gray-200 p-2 rounded text-sm text-gray-600">
                No league frame results available
            </div>
        @endif
    </div>

    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Games & Frames</h2>

        @if(isset($groupedFrames) && $groupedFrames->isNotEmpty())
            @foreach($groupedFrames as $gameId => $frames)
                @php $game = $frames->first()->game ?? null; @endphp

                <div class="mb-4 border border-gray-200 bg-white">
                    <div class="p-2 border-b bg-gray-50">
                        <strong>
                            {{ optional($game)->date ? optional($game->date)->format('j/m/Y') : '—' }}
                        </strong>
                        <div class="text-sm text-gray-600">
                            {{ $game->homeTeam->name ?? $game->homePlayer->name ?? '—' }}
                            vs
                            {{ $game->awayTeam->name ?? $game->awayPlayer->name ?? '—' }}
                            @if($game && ($game->home_score !== null || $game->away_score !== null))
                                — Score: {{ $game->home_score ?? '–' }} - {{ $game->away_score ?? '–' }}
                            @endif
                        </div>
                    </div>

                    <table class="min-w-full w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left text-xs text-gray-600">
                                <th class="px-2 py-1">Opponent</th>
                                <th class="px-2 py-1">Frame Score</th>
                                <th class="px-2 py-1">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($frames as $frame)
                                @php
                                    $isHome = $frame->home_player === $player->id;
                                    $opponentName = $isHome ? optional($frame->awayPlayer)->name ?? ($game->awayTeam->name ?? '-') : optional($frame->homePlayer)->name ?? ($game->homeTeam->name ?? '-');
                                    $frameScore = ($frame->home_score !== null || $frame->away_score !== null)
                                        ? ($frame->home_score ?? '–') . ' - ' . ($frame->away_score ?? '–')
                                        : '—';

                                    // Determine result for this player in the frame
                                    if ($frame->home_score === null && $frame->away_score === null) {
                                        $result = null;
                                    } else {
                                        $playerScore = $isHome ? $frame->home_score : $frame->away_score;
                                        $oppScore = $isHome ? $frame->away_score : $frame->home_score;

                                        if ($playerScore === $oppScore) {
                                            $result = 'Draw';
                                        } elseif ($playerScore > $oppScore) {
                                            $result = 'Won';
                                        } else {
                                            $result = 'Lost';
                                        }
                                    }
                                @endphp
                                <tr class="border-t">
                                    <td class="px-2 py-1">{{ $opponentName }}</td>
                                    <td class="px-2 py-1">{{ $frameScore }}</td>
                                    <td class="px-2 py-1">
                                        @if($result === 'Won')
                                            <span class="text-green-600 font-semibold">Won</span>
                                            @if( ($isHome && !empty($frame->eight_ball_clear_home)) || (! $isHome && !empty($frame->eight_ball_clear_away)) )
                                                <span class="ml-2 inline-block align-middle text-yellow-600" title="8-ball clearance">🎱</span>
                                            @endif
                                        @elseif($result === 'Lost')
                                            <span class="text-red-600 font-semibold">Lost</span>
                                        @elseif($result === 'Draw')
                                            <span class="text-gray-600">Draw</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <p class="mt-2 text-sm text-gray-600">No games or frames found for this player.</p>
        @endif
    </div>

    {{-- Cup games the player is involved in --}}
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Cup Games</h2>

        @if(isset($cupGames) && $cupGames->isNotEmpty())
            <table class="min-w-full w-full text-sm bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-xs text-gray-600">
                        <th class="px-2 py-1">Date</th>
                        <th class="px-2 py-1">Opponent</th>
                        <th class="px-2 py-1">Score</th>
                        <th class="px-2 py-1">Result</th>
                        <th class="px-2 py-1">Competition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cupGames as $game)
                        @php
                            // Determine if this player is on the home or away side (team or individual)
                            $isHomeSide = ($game->home_indiv_id === $player->id) || (in_array($game->home_team_id, $player->teams->pluck('id')->all()));

                            // Opponent label
                            if ($game->home_team_id && $game->away_team_id) {
                                // team vs team
                                $opponent = $isHomeSide ? ($game->awayTeam->name ?? '-') : ($game->homeTeam->name ?? '-');
                            } else {
                                // individual or pairs
                                if ($game->home_indiv_id === $player->id) {
                                    $opponent = optional($game->awayPlayer)->name ?? '-';
                                } elseif ($game->away_indiv_id === $player->id) {
                                    $opponent = optional($game->homePlayer)->name ?? '-';
                                } else {
                                    // fallback: if player's team matched earlier
                                    $opponent = $game->homeTeam->name ?? $game->awayTeam->name ?? '-';
                                }
                            }

                            $score = ($game->home_score !== null || $game->away_score !== null)
                                ? ($game->home_score ?? '–') . ' - ' . ($game->away_score ?? '–')
                                : '—';

                            // Determine result from player's perspective
                            if ($game->home_score === null && $game->away_score === null) {
                                $result = null;
                            } else {
                                if ($game->home_team_id || $game->away_team_id) {
                                    // team game
                                    $playerIsHome = in_array($game->home_team_id, $player->teams->pluck('id')->all());
                                    if ($playerIsHome) {
                                        $result = $game->home_score === $game->away_score ? 'Draw' : ($game->home_score > $game->away_score ? 'Won' : 'Lost');
                                    } else {
                                        $result = $game->home_score === $game->away_score ? 'Draw' : ($game->away_score > $game->home_score ? 'Won' : 'Lost');
                                    }
                                } else {
                                    // individual game
                                    if ($game->home_indiv_id === $player->id) {
                                        $result = $game->home_score === $game->away_score ? 'Draw' : ($game->home_score > $game->away_score ? 'Won' : 'Lost');
                                    } elseif ($game->away_indiv_id === $player->id) {
                                        $result = $game->home_score === $game->away_score ? 'Draw' : ($game->away_score > $game->home_score ? 'Won' : 'Lost');
                                    } else {
                                        $result = null;
                                    }
                                }
                            }
                        @endphp
                        <tr class="border-t">
                            <td class="px-2 py-1">{{ optional($game->date)->format('j/m/Y') ?? '—' }}</td>
                            <td class="px-2 py-1">{{ $opponent }}</td>
                            <td class="px-2 py-1">{{ $score }}</td>
                            <td class="px-2 py-1">
                                @if($result === 'Won')
                                    <span class="text-green-600 font-semibold">Won</span>
                                @elseif($result === 'Lost')
                                    <span class="text-red-600 font-semibold">Lost</span>
                                @elseif($result === 'Draw')
                                    <span class="text-gray-600">Draw</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-2 py-1">{{ optional($game->competition)->name ?? optional($game->competition)->type ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="mt-2 text-sm text-gray-600">No cup games found for this player.</p>
        @endif
    </div>
</div>
@endsection
