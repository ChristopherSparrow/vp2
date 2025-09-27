@php
    $season = $season ?? $season ?? null;
    $name = old('name', $season->name ?? '');
    $start = old('start_date', isset($season) ? $season->start_date?->toDateString() : '');
    $end = old('end_date', isset($season) ? $season->end_date?->toDateString() : '');
@endphp

<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ $name }}" class="form-input mt-1 block w-full" required />
    </div>

    <div>
        <label class="block font-medium">Start Date</label>
        <input type="date" name="start_date" value="{{ $start }}" class="form-input mt-1 block w-full" required />
    </div>

    <div>
        <label class="block font-medium">End Date</label>
        <input type="date" name="end_date" value="{{ $end }}" class="form-input mt-1 block w-full" required />
    </div>

    <div class="flex items-center space-x-2">
        <input type="hidden" name="current" value="0">
        <input id="current" type="checkbox" name="current" value="1" {{ old('current', isset($season) ? ($season->current ? 'checked' : '') : '') == '1' ? 'checked' : '' }} class="form-checkbox" />
        <label for="current" class="font-medium">Current</label>
    </div>
</div>
