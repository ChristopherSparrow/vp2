@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center py-4">
        <h1 class="text-2xl font-bold">Competitions</h1>
        <a href="{{ route('competitions.create') }}" class="btn btn-primary">New Competition</a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Season</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competitions as $competition)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $competition->name }}</td>
                    <td class="px-4 py-2">{{ $competition->type }}</td>
                    <td class="px-4 py-2">{{ $competition->season?->name }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('competitions.show', $competition) }}" class="text-blue-600">View</a>
                        <a href="{{ route('competitions.edit', $competition) }}" class="text-yellow-600 ml-2">Edit</a>
                        <form action="{{ route('competitions.destroy', $competition) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $competitions->links() }}</div>
</div>
@endsection
