@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $season->name }}</h1>

    <dl>
        <dt class="font-semibold">Start Date</dt>
        <dd class="mb-2">{{ $season->start_date->toDateString() }}</dd>

        <dt class="font-semibold">End Date</dt>
        <dd class="mb-2">{{ $season->end_date->toDateString() }}</dd>
    </dl>

    <div class="mt-4">
        <a href="{{ route('seasons.edit', $season) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('seasons.index') }}" class="ml-2">Back</a>
    </div>
</div>
@endsection
