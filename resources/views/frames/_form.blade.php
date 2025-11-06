@php
    $isEdit = isset($frame) && $frame;
    $values = old();
    if ($isEdit) {
        $values = array_merge($frame->toArray(), $values ?? []);
    }
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4 max-w-2xl">
    @csrf
    @if(in_array(strtoupper($method ?? 'POST'), ['PUT', 'PATCH']))
        @method($method)
    @endif

    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold mb-4">{{ $isEdit ? 'Edit Frame' : 'New Frame' }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Game</label>
                <select name="game_id" required class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Select</option>
                    @foreach($games as $g)
                        <option value="{{ $g->id }}" {{ (string)($values['game_id'] ?? '') === (string)$g->id ? 'selected' : '' }}>
                            {{ optional($g->date)->format('Y-m-d') ?? '' }} — {{ optional($g->homeTeam)->name ?? optional($g->homePlayer)->name ?? 'Home' }} vs {{ optional($g->awayTeam)->name ?? optional($g->awayPlayer)->name ?? 'Away' }}
                        </option>
                    @endforeach
                </select>
                @error('game_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Frame No</label>
                <input id="game_no_input" type="number" name="game_no" value="{{ $values['game_no'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="1" max="12" {{ $isEdit ? '' : 'readonly' }} />
                @error('game_no')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Player</label>
                <select id="home_player_select" name="home_player" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @if(isset($homePlayersByGame) && !empty($homePlayersByGame))
                        @php $hList = $homePlayersByGame[$values['game_id'] ?? ''] ?? [] @endphp
                        @foreach($hList as $p)
                            <option value="{{ $p['id'] }}" {{ (string)($values['home_player'] ?? '') === (string)$p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                        @endforeach
                    @else
                        @foreach($players as $p)
                            <option value="{{ $p->id }}" {{ (string)($values['home_player'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('home_player')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Player</label>
                <select id="away_player_select" name="away_player" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @if(isset($awayPlayersByGame) && !empty($awayPlayersByGame))
                        @php $aList = $awayPlayersByGame[$values['game_id'] ?? ''] ?? [] @endphp
                        @foreach($aList as $p)
                            <option value="{{ $p['id'] }}" {{ (string)($values['away_player'] ?? '') === (string)$p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                        @endforeach
                    @else
                        @foreach($players as $p)
                            <option value="{{ $p->id }}" {{ (string)($values['away_player'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('away_player')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
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
                <label class="block text-sm font-medium text-gray-700">Quick result</label>
                <div class="mt-2 flex items-center space-x-4">
                    <label class="inline-flex items-center space-x-2">
                        <input id="win_home" type="radio" name="winner_toggle" value="home" class="form-radio" />
                        <span class="text-sm">Home Win</span>
                    </label>
                    <label class="inline-flex items-center space-x-2">
                        <input id="win_away" type="radio" name="winner_toggle" value="away" class="form-radio" />
                        <span class="text-sm">Away Win</span>
                    </label>
                    <span class="text-sm text-gray-500">(Selecting will set Home/Away scores to 1/0 or 0/1)</span>
                </div>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="eight_ball_clear_home" value="1" {{ !empty($values['eight_ball_clear_home']) ? 'checked' : '' }} />
                    <span class="text-sm">8-ball clear (home)</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="eight_ball_clear_away" value="1" {{ !empty($values['eight_ball_clear_away']) ? 'checked' : '' }} />
                    <span class="text-sm">8-ball clear (away)</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Game No</label>
                <input type="number" name="home_game_no" value="{{ $values['home_game_no'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="0" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Game No</label>
                <input type="number" name="away_game_no" value="{{ $values['away_game_no'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="0" />
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save
            </button>
        </div>
    </div>
</form>

@if(isset($gameNextNumbers))
    <script>
        (function(){
            const gameNextNumbers = {!! json_encode($gameNextNumbers ?? []) !!};
            const homePlayersByGame = {!! json_encode($homePlayersByGame ?? []) !!};
            const awayPlayersByGame = {!! json_encode($awayPlayersByGame ?? []) !!};
            const playerFrameCounts = {!! json_encode($playerFrameCountsByGame ?? []) !!};

            const select = document.querySelector('select[name="game_id"]');
            const input = document.getElementById('game_no_input');
            const homeSelect = document.getElementById('home_player_select');
            const awaySelect = document.getElementById('away_player_select');

            function setNextForSelected() {
                if (!select || !input) return;
                const gid = select.value;
                if (!gid) return;
                const next = (gameNextNumbers[gid] !== undefined) ? gameNextNumbers[gid] : null;
                if (next !== null && (input.value === '' || input.value === '0')) {
                    input.value = next;
                }
            }

            function populatePlayersForGame(gid) {
                // populate home
                if (homeSelect) {
                    homeSelect.innerHTML = '<option value="">--</option>';
                    const list = (homePlayersByGame[gid] || []);
                    list.forEach(function(p){
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        homeSelect.appendChild(opt);
                    });
                }
                // populate away
                if (awaySelect) {
                    awaySelect.innerHTML = '<option value="">--</option>';
                    const list = (awayPlayersByGame[gid] || []);
                    list.forEach(function(p){
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        awaySelect.appendChild(opt);
                    });
                }
            }

            function setPlayerGameNoForInput(gid, playerId, inputEl) {
                if (!gid || !playerId || !inputEl) return;
                const counts = (playerFrameCounts[gid] || {});
                const count = counts[playerId] || 0;
                const next = count + 1;
                // Only auto-set if field is empty or zero (preserve explicit values when editing)
                if (inputEl.value === '' || inputEl.value === '0') {
                    inputEl.value = next;
                }
            }

            document.addEventListener('DOMContentLoaded', function(){
                setNextForSelected();
                // populate for initial selection
                if (select && select.value) populatePlayersForGame(select.value);
                // also set home/away player game numbers for any initial selection
                const gidInit = select ? select.value : null;
                if (gidInit) {
                    const hIn = document.querySelector('input[name="home_game_no"]');
                    const aIn = document.querySelector('input[name="away_game_no"]');
                    const hPid = homeSelect ? homeSelect.value : null;
                    const aPid = awaySelect ? awaySelect.value : null;
                    if (hPid) setPlayerGameNoForInput(gidInit, hPid, hIn);
                    if (aPid) setPlayerGameNoForInput(gidInit, aPid, aIn);
                }
            });

            if (select) select.addEventListener('change', function(){
                const gid = this.value;
                const next = (gameNextNumbers[gid] !== undefined) ? gameNextNumbers[gid] : null;
                if (next !== null) {
                    input.value = next;
                } else {
                    input.value = 1;
                }
                populatePlayersForGame(gid);
                // update per-player game numbers after changing game
                const hIn = document.querySelector('input[name="home_game_no"]');
                const aIn = document.querySelector('input[name="away_game_no"]');
                const hPid = homeSelect ? homeSelect.value : null;
                const aPid = awaySelect ? awaySelect.value : null;
                if (hPid) setPlayerGameNoForInput(gid, hPid, hIn);
                if (aPid) setPlayerGameNoForInput(gid, aPid, aIn);
            });

            if (homeSelect) {
                homeSelect.addEventListener('change', function(){
                    const gid = select ? select.value : null;
                    const pid = this.value;
                    const hIn = document.querySelector('input[name="home_game_no"]');
                    setPlayerGameNoForInput(gid, pid, hIn);
                });
            }

            if (awaySelect) {
                awaySelect.addEventListener('change', function(){
                    const gid = select ? select.value : null;
                    const pid = this.value;
                    const aIn = document.querySelector('input[name="away_game_no"]');
                    setPlayerGameNoForInput(gid, pid, aIn);
                });
            }
        })();
    </script>
@endif

<script>
    (function(){
        const homeInput = document.querySelector('input[name="home_score"]');
        const awayInput = document.querySelector('input[name="away_score"]');
        const homeRadio = document.getElementById('win_home');
        const awayRadio = document.getElementById('win_away');

        function applyToggle() {
            if (!homeInput || !awayInput) return;
            if (homeRadio && homeRadio.checked) {
                homeInput.value = 1;
                awayInput.value = 0;
            } else if (awayRadio && awayRadio.checked) {
                homeInput.value = 0;
                awayInput.value = 1;
            }
        }

        document.addEventListener('DOMContentLoaded', function(){
            // initialize toggle based on current score values (only if they are exact 1/0 or 0/1)
            if (homeInput && awayInput && homeRadio && awayRadio) {
                const hs = parseInt(homeInput.value || '');
                const as = parseInt(awayInput.value || '');
                if (!isNaN(hs) && !isNaN(as)) {
                    if (hs === 1 && as === 0) homeRadio.checked = true;
                    else if (as === 1 && hs === 0) awayRadio.checked = true;
                }
            }
        });

        if (homeRadio) homeRadio.addEventListener('change', applyToggle);
        if (awayRadio) awayRadio.addEventListener('change', applyToggle);
    })();
</script>
