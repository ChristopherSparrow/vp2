@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Players</h1>
        <a href="{{ route('players.create') }}" class="btn btn-primary">Create Player</a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto border border-white-300">
        <table class="min-w-full w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Current Team</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($players as $player)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $player->name }}</td>
                <td class="px-4 py-2">{{ optional($player->currentTeam())->name ?? '-' }}</td>
                <td class="px-4 py-2">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('players.show', $player) }}" class="text-blue-600">View</a>
                        <a href="{{ route('players.edit', $player) }}" class="text-yellow-600">Edit</a>
                        <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline-flex">
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
        {{ $players->links() }}
    </div>
</div>
@endsection
