
@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Stats for {{ $competition->name }}</h1>

    <p class="text-gray-600">This is a placeholder stats page for the competition. Replace with real stats as needed.</p>

    <div class="mt-6">
        <a href="{{ url()->previous() }}" class="px-3 py-1 rounded bg-white border text-sm hover:bg-gray-50">Back</a>
    </div>
</div>
@endsection
