@extends('layouts.app')

@section('title', 'Choose payment method — DISC Report')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('participant._progress', ['step' => 6])

    <h1 class="text-2xl font-bold mb-1">Choose how to pay</h1>
    <p class="text-gray-600 mb-6">
        Registration for <strong>{{ $course->course_name }}</strong>
        @if (! empty($course->course_price))
            &mdash; <span class="text-gray-800 font-medium">${{ number_format($course->course_price, 2) }}</span>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-4">
        {{-- Card payment --}}
        <a href="{{ route('participant.payment', $course->course_id) }}"
           class="block bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition">
            <div class="h-1 w-12 rounded mb-4" style="background:#1565c0;"></div>
            <h2 class="text-lg font-semibold mb-1">Pay with credit or debit card</h2>
            <p class="text-gray-500 text-sm mb-3">Secure checkout via Stripe. International cards accepted.</p>
            <span class="inline-block text-blue-700 font-medium text-sm">Continue to checkout →</span>
        </a>

        {{-- Prepaid / scholarship code --}}
        <a href="{{ route('participant.prepaid') }}"
           class="block bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-amber-500 transition">
            <div class="h-1 w-12 rounded mb-4" style="background:#f9a825;"></div>
            <h2 class="text-lg font-semibold mb-1">Use a prepaid / scholarship code</h2>
            <p class="text-gray-500 text-sm mb-3">Apply a code provided by your instructor or organization to waive the fee.</p>
            <span class="inline-block text-amber-700 font-medium text-sm">Enter code →</span>
        </a>
    </div>

    <p class="text-center text-xs text-gray-500 mt-6">
        Need to leave? Your account is saved. You can sign in later from the
        <a href="{{ url('/') }}" class="text-blue-600 hover:underline">home page</a>.
    </p>
</div>
@endsection
