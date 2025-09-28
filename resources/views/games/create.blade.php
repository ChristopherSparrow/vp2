@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold">New Game</h1>

    @include('games._form', ['action' => route('games.store'), 'method' => 'POST', 'game' => null])
</div>
@endsection
