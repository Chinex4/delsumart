@extends('layouts.app') @section('content')
    <div class="mx-auto max-w-md px-4 py-16">
        <h1 class="text-3xl font-black text-blue-950">Choose a new password</h1>
        <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-4 border bg-white p-6">@csrf<input
                type="hidden" name="token" value="{{ $token }}"><label class="block text-sm font-semibold">Email<input
                    type="email" name="email" required value="{{ old('email', $email) }}"
                    class="mt-1 w-full border p-3"></label><label class="block text-sm font-semibold">New password<input
                    type="password" name="password" required class="mt-1 w-full border p-3"></label><label
                class="block text-sm font-semibold">Confirm password<input type="password" name="password_confirmation"
                    required class="mt-1 w-full border p-3"></label>
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-700">{{ $error }}</p>
            @endforeach
            <button class="w-full bg-blue-950 px-4 py-3 font-bold text-white">Reset password</button>
        </form>
    </div>
@endsection
