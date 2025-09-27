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
</div>
