@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Teams</h1>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">Create Team</a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto border border-white-300">
        <table class="min-w-full w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Location</th>
                <th class="px-4 py-2">Season</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($teams as $team)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $team->name }}</td>
                <td class="px-4 py-2">{{ $team->location }}</td>
                <td class="px-4 py-2">{{ $team->season?->name }}</td>
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

    <div class="mt-4">
        {{ $teams->links() }}
    </div>
</div>
@endsection
