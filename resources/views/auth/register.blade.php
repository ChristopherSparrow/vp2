@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-semibold mb-4">Register</h1>
            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded border-gray-300" />
                    @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full rounded border-gray-300" />
                    @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input name="password" type="password" class="mt-1 block w-full rounded border-gray-300" />
                    @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="mt-1 block w-full rounded border-gray-300" />
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Register</button>
                    <a href="{{ route('login') }}" class="text-sm text-blue-600">Already have an account?</a>
                </div>
            </form>
        </div>
    </div>
@endsection
