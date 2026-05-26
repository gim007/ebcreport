@extends('layouts.app')

@section('title', 'Select Course — DISC Report')

@section('content')
<div class="mb-2 text-sm text-gray-500">
    <a href="{{ route('participant.organizations') }}" class="hover:underline">Organizations</a>
    &rsaquo;
    <a href="{{ route('participant.instructors', $instructor->uni_id) }}" class="hover:underline">Instructors</a>
    &rsaquo; {{ $instructor->ins_fname }} {{ $instructor->ins_lname }}
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold">Select a Course</h1>
    <p class="text-gray-600 mt-1">Choose the course for which you are registering.</p>
</div>

<livewire:course-grid :instructor-id="$instructor->ins_id" />
@endsection
