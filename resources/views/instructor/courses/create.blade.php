@extends('layouts.instructor')

@section('title', 'New Course — Instructor Portal')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
        <h1 class="text-2xl font-bold">New Course</h1>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <form method="POST" action="{{ route('instructor.courses.store') }}">
            @csrf
            @include('instructor.courses._form')
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded transition text-sm">
                    Create Course
                </button>
                <a href="{{ route('instructor.courses.index') }}"
                   class="text-gray-500 hover:text-gray-700 px-6 py-2 text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
