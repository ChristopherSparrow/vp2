@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md p-6 rounded shadow">
            <h1 class="text-2xl font-semibold mb-4">Login</h1>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-white-700">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full bg-gray-800 text-white rounded border-gray-300" />
                    @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input name="password" type="password" class="mt-1 block w-full rounded border-gray-300" />
                    @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Login</button>
                    <a href="{{ route('register') }}" class="text-sm text-blue-600">Create account</a>
                </div>
            </form>
        </div>
    </div>
@endsection
