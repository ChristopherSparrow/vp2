@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold py-4">New Competition</h1>

    @include('competitions._form', ['competition' => null])
</div>
@endsection
