@extends('layouts.guest')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Confirm Password</h2>
        <p class="text-sm text-slate-600">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
            <div class="mt-2 text-sm text-slate-600">
                 <input type="password" name="password" id="password" required autocomplete="current-password" autofocus
                       class="block w-full rounded-lg border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                       placeholder="••••••••">
            </div>
             @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                Confirm
            </button>
        </div>
    </form>
@endsection
