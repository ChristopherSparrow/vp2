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
                        <option value="{{ $g->id }}" {{ (string)($values['game_id'] ?? '') === (string)$g->id ? 'selected' : '' }}>{{ $g->competition->name ?? 'Game' }} — {{ optional($g->date)->format('Y-m-d') ?? '' }}</option>
                    @endforeach
                </select>
                @error('game_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Game No</label>
                <input type="number" name="game_no" value="{{ $values['game_no'] ?? '' }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" min="1" max="12" />
                @error('game_no')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Home Player</label>
                <select name="home_player" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @foreach($players as $p)
                        <option value="{{ $p->id }}" {{ (string)($values['home_player'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('home_player')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Away Player</label>
                <select name="away_player" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">--</option>
                    @foreach($players as $p)
                        <option value="{{ $p->id }}" {{ (string)($values['away_player'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
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
