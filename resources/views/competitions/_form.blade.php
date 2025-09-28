@php
    $action = $competition ? route('competitions.update', $competition) : route('competitions.store');
    $method = $competition ? 'PUT' : 'POST';
@endphp

@if ($errors->any())
    <div class="mb-4 text-red-600">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @if($competition)
        @method('PUT')
    @endif

    <div>
        <label class="block">Name</label>
        <input type="text" name="name" value="{{ old('name', $competition?->name) }}" class="w-full" />
    </div>

    <div>
        <label class="block">Type</label>
        <select name="type" class="w-full">
            @foreach($types as $t)
                <option value="{{ $t }}" @selected(old('type', $competition?->type) === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Season</label>
        <select name="season_id" class="w-full">
            @foreach($seasons as $season)
                <option value="{{ $season->id }}" @selected(old('season_id', $competition?->season_id) == $season->id)>{{ $season->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('competitions.index') }}" class="ml-2">Cancel</a>
    </div>
</form>
