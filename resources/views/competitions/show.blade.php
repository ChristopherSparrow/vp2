@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold py-4">{{ $competition->name }}</h1>

    <div class="mb-2"><strong>Type:</strong> {{ $competition->type }}</div>
    <div class="mb-2"><strong>Season:</strong> {{ $competition->season?->name }}</div>

    <a href="{{ route('competitions.edit', $competition) }}" class="btn btn-secondary">Edit</a>
    <a href="{{ route('competitions.index') }}" class="ml-2">Back</a>
</div>
@endsection
