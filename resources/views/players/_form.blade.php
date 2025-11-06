@php
    $player = $player ?? null;
    $name = old('name', $player->name ?? '');
    $position = null;
    $phone = old('phone', $player->phone ?? '');
    $selectedTeamIds = old('team_ids', $player ? $player->playerTeams()->whereIn('team_id', $teams->pluck('id'))->pluck('team_id')->toArray() : []);
@endphp

<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ $name }}" class="form-input mt-1 block w-full" required />
    </div>



    <div>
        <label class="block font-medium">Phone</label>
        <input type="tel" name="phone" value="{{ $phone }}" class="form-input mt-1 block w-full" />
    </div>

    <div>
        <label class="block font-medium">Teams (select one or more for the current season)</label>
        <select name="team_ids[]" multiple class="form-select mt-1 block w-full h-40">
            @foreach($teams as $team)
                <option value="{{ $team->id }}" {{ in_array($team->id, $selectedTeamIds) ? 'selected' : '' }}>{{ $team->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- team multi-select UI; JavaScript row editor removed in favor of simpler selection --}}
            {{-- Existing assignments will be rendered here by JS on load --}}
