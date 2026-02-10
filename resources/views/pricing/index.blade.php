@extends('layouts.guest')

@section('content')
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-400">Pricing</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-white sm:text-5xl">Pricing plans for teams of&nbsp;all&nbsp;sizes</p>
            </div>
            <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-zinc-300">
                Choose an affordable plan that’s packed with the best features for engaging your audience, creating customer loyalty, and driving sales.
            </p>
            
            <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-y-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-x-8 xl:gap-x-12">
                @foreach($plans as $plan)
                    <x-plan-card :plan="$plan" />
                @endforeach
            </div>
        </div>
    </div>
@endsection
