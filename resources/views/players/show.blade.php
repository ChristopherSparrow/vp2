@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="mb-4">
        <a href="{{ route('players.index') }}">&larr; Back to Players</a>
    </div>

    <h1 class="text-2xl font-bold mb-2">{{ $player->name }}</h1>

    <div class="border p-4">
    <p><strong>Position:</strong> {{ $player->position ?? '-' }}</p>
    <p><strong>Number:</strong> {{ $player->number ?? '-' }}</p>
        <p><strong>Created:</strong> {{ $player->created_at->toDayDateTimeString() }}</p>
    </div>
</div>
@endsection
