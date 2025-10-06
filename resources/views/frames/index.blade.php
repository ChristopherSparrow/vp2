@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">Frames</h1>
            <a href="{{ route('frames.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">New Frame</a>
        </div>

        <div class="bg-white rounded shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Game</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frame No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Home</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Away</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($frames as $frame)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->game->competition->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->game_no }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->homePlayer->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->awayPlayer->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $frame->home_score ?? '-' }} — {{ $frame->away_score ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                <a href="{{ route('frames.show', $frame) }}" class="text-green-600">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $frames->links() }}
        </div>
    </div>
@endsection
