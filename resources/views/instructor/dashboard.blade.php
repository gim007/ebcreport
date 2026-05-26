@extends('layouts.instructor')

@section('title', 'Dashboard — Instructor Portal')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Welcome, {{ $instructor->full_name }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $instructor->organization->uni_name ?? '' }}</p>
    </div>
    <a href="{{ route('instructor.courses.create') }}"
       class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded transition">
        + New Course
    </a>
</div>

@if ($courses->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-gray-500">
        <p class="mb-3">You haven't created any courses yet.</p>
        <a href="{{ route('instructor.courses.create') }}" class="text-blue-600 hover:underline">Create your first course</a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($courses as $course)
            <div class="bg-white border border-gray-200 rounded-lg p-5 flex flex-col">
                <h3 class="font-semibold text-gray-800 mb-1">{{ $course->course_name }}</h3>
                @if ($course->term)
                    <p class="text-xs text-gray-500">{{ $course->term }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">
                    {{ $course->test_results_count }}
                    {{ Str::plural('assessment', $course->test_results_count) }} completed
                </p>
                <div class="mt-auto pt-4 flex gap-3 text-sm">
                    <a href="{{ route('instructor.courses.roster', $course->course_id) }}"
                       class="text-blue-600 hover:underline">Roster</a>
                    <a href="{{ route('instructor.courses.edit', $course->course_id) }}"
                       class="text-gray-500 hover:underline">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
