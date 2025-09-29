@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-semibold mb-4">Edit Frame</h1>

        @include('frames._form', ['action' => route('frames.update', $frame), 'method' => 'PUT'])
    </div>
@endsection
