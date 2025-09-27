@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $team->name }}</h1>

    <dl>
        <dt class="font-semibold">Location</dt>
        <dd class="mb-2">{{ $team->location }}</dd>

        <dt class="font-semibold">Season</dt>
        <dd class="mb-2">{{ $team->season?->name }}</dd>
    </dl>

    <div class="mt-4">
        <a href="{{ route('teams.edit', $team) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('teams.index') }}" class="ml-2">Back</a>
    </div>
</div>
@endsection
