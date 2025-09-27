@php
    $team = $team ?? null;
    $name = old('name', $team->name ?? '');
    $location = old('location', $team->location ?? '');
    $season_id = old('season_id', $team->season_id ?? '');
@endphp

<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ $name }}" class="form-input mt-1 block w-full" required />
    </div>

    <div>
        <label class="block font-medium">Location</label>
        <input type="text" name="location" value="{{ $location }}" class="form-input mt-1 block w-full" placeholder="City, State, Country" />
    </div>

    <div>
        <label class="block font-medium">Season</label>
        <select name="season_id" class="form-select mt-1 block w-full" required>
            <option value="">-- Select Season --</option>
            @foreach($seasons as $s)
                <option value="{{ $s->id }}" {{ $season_id == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->start_date->toDateString() }})</option>
            @endforeach
        </select>
    </div>
</div>
