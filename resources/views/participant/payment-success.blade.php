@extends('layouts.app')

@section('title', 'Payment Successful — DISC Report')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="text-5xl mb-4">&#10003;</div>
    <h1 class="text-2xl font-bold mb-2">Payment Successful</h1>
    <p class="text-gray-600 mb-6">
        You are registered for <strong>{{ $course->course_name }}</strong>.
        Your assessment credit has been added to your account.
    </p>
    <a href="{{ route('participant.test') }}"
       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded transition">
        Take the Assessment
    </a>
</div>
@endsection
