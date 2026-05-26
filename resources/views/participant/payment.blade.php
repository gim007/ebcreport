@extends('layouts.app')

@section('title', 'Payment — DISC Report')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-1">Complete Your Registration</h1>
    <p class="text-gray-600 mb-6">
        Pay for <strong>{{ $course->course_name }}</strong>
        @if (isset($course->course_price))
            &mdash; <strong>${{ number_format($course->course_price, 2) }}</strong>
        @endif
    </p>

    <div id="payment-form" class="space-y-4">
        <div id="payment-element" class="border border-gray-300 rounded px-3 py-3"></div>
        <p id="payment-message" class="text-red-600 text-sm hidden"></p>

        <form method="POST" action="{{ route('participant.payment.charge', $course->course_id) }}" id="stripe-form">
            @csrf
            <input type="hidden" name="payment_method_id" id="payment_method_id">
            <button id="pay-button" type="button"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
                Pay Now
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-gray-500 mt-4">
        Have a prepaid code?
        <a href="{{ route('participant.prepaid') }}" class="text-blue-600 hover:underline">Enter it here</a>
    </p>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
const elements = stripe.elements();
const paymentElement = elements.create('card');
paymentElement.mount('#payment-element');

document.getElementById('pay-button').addEventListener('click', async () => {
    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: paymentElement,
    });
    if (error) {
        const msg = document.getElementById('payment-message');
        msg.textContent = error.message;
        msg.classList.remove('hidden');
        return;
    }
    document.getElementById('payment_method_id').value = paymentMethod.id;
    document.getElementById('stripe-form').submit();
});
</script>
@endpush
