@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $season->name }}</h1>

    <dl>
        <dt class="font-semibold">Start Date</dt>
        <dd class="mb-2">{{ $season->start_date->toDateString() }}</dd>

        <dt class="font-semibold">End Date</dt>
        <dd class="mb-2">{{ $season->end_date->toDateString() }}</dd>

        <dt class="font-semibold">Current</dt>
        <dd class="mb-2">{{ $season->current ? 'Yes' : 'No' }}</dd>
    </dl>

    <div class="mt-4">
        <a href="{{ route('seasons.edit', $season) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('seasons.index') }}" class="ml-2">Back</a>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Teams in this Season</h2>

        @if($season->teams->isEmpty())
            <div class="text-gray-600">No teams for this season.</div>
        @else
            <div class="overflow-x-auto border border-white-300 mt-2">
                <table class="min-w-full w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Location</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($season->teams as $team)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $team->name }}</td>
                            <td class="px-4 py-2">{{ $team->location }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('teams.show', $team) }}" class="text-blue-600">View</a>
                                    <a href="{{ route('teams.edit', $team) }}" class="text-yellow-600">Edit</a>
                                    <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
