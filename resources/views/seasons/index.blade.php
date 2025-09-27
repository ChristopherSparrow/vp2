@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Seasons</h1>
        <a href="{{ route('seasons.create') }}" class="btn btn-primary">Create Season</a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif


    <div class="overflow-x-auto border border-white-300">
        <table class="min-w-full w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Start Date</th>
                <th class="px-4 py-2">End Date</th>
                <th class="px-4 py-2">Current</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($seasons as $season)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $season->name }}</td>
                <td class="px-4 py-2">{{ $season->start_date->toDateString() }}</td>
                <td class="px-4 py-2">{{ $season->end_date->toDateString() }}</td>
                <td class="px-4 py-2">
                    @if($season->current)
                        <span class="text-green-600 font-semibold">Yes</span>
                    @else
                        <span class="text-gray-600">No</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('seasons.show', $season) }}" class="text-blue-600">View</a>
                        <a href="{{ route('seasons.edit', $season) }}" class="text-yellow-600">Edit</a>
                        <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="inline-flex">
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
        {{ $seasons->links() }}
    </div>
</div>
@endsection
