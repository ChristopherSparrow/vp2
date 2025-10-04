@php
    $team = $team ?? null;
    $name = old('name', $team->name ?? '');
    $location = old('location', $team->location ?? '');
    $season_id = old('season_id', $team->season_id ?? '');

    // If no season selected, default to the current season when available.
    if (empty($season_id) && isset($seasons)) {
        // $seasons may be a Collection or array; be defensive.
        $currentSeason = collect($seasons)->firstWhere('current', true) ?? null;
        $season_id = $currentSeason->id ?? '';
    }
@endphp

<div class="container mx-auto py-4">
    <div class="border rounded-lg p-6 shadow-sm bg-white">
        <h2 class="text-xl font-bold mb-4">Team Details</h2>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ $name }}" class="form-input mt-1 block w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" value="{{ $location }}" class="form-input mt-1 block w-full" placeholder="City, State, Country" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Season</label>
                <select name="season_id" class="form-select mt-1 block w-full" required>
                    <option value="">-- Select Season --</option>
                    @foreach($seasons as $s)
                        <option value="{{ $s->id }}" {{ $season_id == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->start_date->toDateString() }})</option>
                    @endforeach
                </select>

                @php
                    $selectedCurrent = collect($seasons)->firstWhere('current', true);
                @endphp
                @if($selectedCurrent)
                    <p class="text-sm text-gray-500 mt-2">Defaulting to current season: <span class="font-medium">{{ $selectedCurrent->name }}</span></p>
                @endif
            </div>
        </div>
    </div>
</div>
