@extends('layouts.app')

@section('title', 'Select Instructor — DISC Report')

@section('content')
@include('participant._progress', ['step' => 3])

<div class="mb-2 text-sm text-gray-500">
    <a href="{{ route('participant.organizations') }}" class="hover:underline">Organizations</a>
    &rsaquo; {{ $org->uni_name }}
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold">Select Your Instructor</h1>
    <p class="text-gray-600 mt-1">Choose the instructor who will administer your assessment at {{ $org->uni_name }}.</p>
</div>

<livewire:instructor-grid :org-id="$org->uni_id" />
@endsection
