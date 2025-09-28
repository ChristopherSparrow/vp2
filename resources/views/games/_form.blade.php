@php
    $isEdit = isset($game) && $game;
    $values = old();
    if ($isEdit) {
        $values = array_merge($game->toArray(), $values ?? []);
    }
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4 max-w-lg">
    @csrf
    @if(in_array(strtoupper($method), ['PUT', 'PATCH']))
        @method($method)
    @endif

    <div>
        <label>Competition</label>
        <select name="competition_id" required class="form-input">
            <option value="">Select</option>
            @foreach($competitions as $c)
                <option value="{{ $c->id }}" {{ (string)($values['competition_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Home Team</label>
        <select name="home_team_id" class="form-input">
            <option value="">--</option>
            @foreach($teams as $t)
                <option value="{{ $t->id }}" {{ (string)($values['home_team_id'] ?? '') === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Away Team</label>
        <select name="away_team_id" class="form-input">
            <option value="">--</option>
            @foreach($teams as $t)
                <option value="{{ $t->id }}" {{ (string)($values['away_team_id'] ?? '') === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Home Player</label>
        <select name="home_indiv_id" class="form-input">
            <option value="">--</option>
            @foreach($players as $p)
                <option value="{{ $p->id }}" {{ (string)($values['home_indiv_id'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Away Player</label>
        <select name="away_indiv_id" class="form-input">
            <option value="">--</option>
            @foreach($players as $p)
                <option value="{{ $p->id }}" {{ (string)($values['away_indiv_id'] ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Home Score</label>
        <input type="number" name="home_score" value="{{ $values['home_score'] ?? '' }}" class="form-input" min="0" />
    </div>

    <div>
        <label>Away Score</label>
        <input type="number" name="away_score" value="{{ $values['away_score'] ?? '' }}" class="form-input" min="0" />
    </div>

    <div>
        <label>Date</label>
        <input type="datetime-local" name="date" value="{{ isset($values['date']) ? 
            \Carbon\Carbon::parse($values['date'])->format('Y-m-d\TH:i') : '' }}" class="form-input" />
    </div>

    <div>
        <button class="btn">Save</button>
    </div>
</form>
