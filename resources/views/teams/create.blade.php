@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Create Team</h1>

    @if($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('teams.store') }}" method="POST">
        @csrf
        @include('teams._form')

        <div class="mt-4">
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('teams.index') }}" class="ml-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
