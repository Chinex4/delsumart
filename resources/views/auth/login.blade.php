@extends('layouts.app') @section('content')
    <div class="mx-auto max-w-md px-4 py-16">
        <div class="mb-8">
            <p class="text-sm font-bold text-blue-700">SECURE SIGN IN</p>
            <h1 class="mt-2 text-3xl font-black text-blue-950">Welcome back</h1>
            <p class="mt-2 text-sm text-slate-600">After your password, we'll send a one-time code to your registered email.
            </p>
        </div>
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">@csrf<label class="block"><span
                    class="text-sm font-semibold">Email or matric number</span><input name="login" required
                    value="{{ old('login') }}"
                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 focus:border-blue-900 focus:outline-none"></label><label
                class="block"><span class="text-sm font-semibold">Password</span><input name="password" type="password"
                    required class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3"></label>
            @error('login')
                <p class="text-sm text-red-700">{{ $message }}</p>
            @enderror
            <button class="w-full rounded-lg bg-blue-950 px-5 py-3 font-bold text-white">Continue securely</button>
        </form>
    </div>
@endsection
