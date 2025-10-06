@php
    $isEdit = isset($game) && $game;
    $values = old();
    if ($isEdit) {
        $values = array_merge($game->toArray(), $values ?? []);
    }
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4 max-w-2xl">
    @csrf
    @if(in_array(strtoupper($method), ['PUT', 'PATCH']))
        @method($method)
    @endif

    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">{{ $isEdit ? 'Edit Game' : 'New Game' }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Competition</label>
                <select id="competition-select" name="competition_id" required class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Select</option>
                    @foreach($competitions as $c)
                        <option value="{{ $c->id }}" {{ (string)($values['competition_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('competition_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Season</label>
                <select name="season_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Select</option>
                    @if(isset($seasons))
                        @foreach($seasons as $s)
                            <option value="{{ $s->id }}" {{ (string)($values['season_id'] ?? ($currentSeason->id ?? '')) === (string)$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('season_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Team</label>
                <select id="home_team_id" name="home_team_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @php $teamList = $seasonTeams ?? $teams ?? collect(); @endphp
                    @foreach($teamList as $t)
                        <option value="{{ $t->id }}" {{ (string)($values['home_team_id'] ?? '') === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                @error('home_team_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Team</label>
                <select id="away_team_id" name="away_team_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @foreach($teamList as $t)
                        <option value="{{ $t->id }}" {{ (string)($values['away_team_id'] ?? '') === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                @error('away_team_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Player</label>
                <select id="home_indiv_id" name="home_indiv_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @foreach($players as $p)
                        <option value="{{ $p->id }}" {{ (string)($values['home_indiv_id'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('home_indiv_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Player</label>
                <select id="away_indiv_id" name="away_indiv_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @foreach($players as $p)
                        <option value="{{ $p->id }}" {{ (string)($values['away_indiv_id'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('away_indiv_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Score</label>
                <input type="number" name="home_score" value="{{ $values['home_score'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="0" />
                @error('home_score')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Score</label>
                <input type="number" name="away_score" value="{{ $values['away_score'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="0" />
                @error('away_score')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <input type="datetime-local" name="date" value="{{ isset($values['date']) ? 
                    \Carbon\Carbon::parse($values['date'])->format('Y-m-d\TH:i') : '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" />
                @error('date')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save
            </button>
        </div>
    </div>

    {{-- Preserve a return URL so controller can redirect back to the index or caller --}}
    @php
        $returnTo = old('return_to') ?? request()->query('return_to') ?? null;
    @endphp
    @if($returnTo)
        <input type="hidden" name="return_to" value="{{ $returnTo }}" />
    @endif

    @push('scripts')
        <script>
            // Map competition id -> type for client-side toggling
            const competitionTypes = {
                @foreach($competitions as $c)
                    "{{ $c->id }}": "{{ $c->type }}",
                @endforeach
            };

            function setDisabled(el, disabled) {
                if (!el) return;
                el.disabled = disabled;
                if (disabled) {
                    el.classList.add('bg-gray-100', 'text-gray-500');
                } else {
                    el.classList.remove('bg-gray-100', 'text-gray-500');
                }
            }

            function onCompetitionChange() {
                const select = document.getElementById('competition-select');
                const compId = select?.value;
                const type = competitionTypes[compId] ?? null;

                const homeTeam = document.getElementById('home_team_id');
                const awayTeam = document.getElementById('away_team_id');
                const homePlayer = document.getElementById('home_indiv_id');
                const awayPlayer = document.getElementById('away_indiv_id');

                // If type starts with 'team' then teams are active, players disabled
                if (type && type.startsWith('team')) {
                    setDisabled(homePlayer, true);
                    setDisabled(awayPlayer, true);
                    setDisabled(homeTeam, false);
                    setDisabled(awayTeam, false);
                } else if (type && (type.startsWith('individ') || type.startsWith('pairs'))) {
                    // Individual or pairs competitions use players
                    setDisabled(homePlayer, false);
                    setDisabled(awayPlayer, false);
                    setDisabled(homeTeam, true);
                    setDisabled(awayTeam, true);
                } else {
                    // Unknown or empty: enable all
                    setDisabled(homePlayer, false);
                    setDisabled(awayPlayer, false);
                    setDisabled(homeTeam, false);
                    setDisabled(awayTeam, false);
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const compSelect = document.getElementById('competition-select');
                if (compSelect) {
                    compSelect.addEventListener('change', onCompetitionChange);
                }
                // Run once in case of edit / preselected values
                onCompetitionChange();
            });
        </script>
    @endpush
</form>
