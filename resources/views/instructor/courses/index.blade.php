@extends('layouts.instructor')

@section('title', 'My Courses — Instructor Portal')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">My Courses</h1>
    <a href="{{ route('instructor.courses.create') }}"
       class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded transition">
        + New Course
    </a>
</div>

@if ($courses->isEmpty())
    <p class="text-gray-500">No courses yet. <a href="{{ route('instructor.courses.create') }}" class="text-blue-600 hover:underline">Create one</a>.</p>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b text-left text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Course</th>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Term</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3">Assessments</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $course->course_name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $course->course_code ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $course->term ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $course->course_price ? '$'.number_format($course->course_price, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $course->test_results_count }}</td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <a href="{{ route('instructor.courses.roster', $course->course_id) }}"
                               class="text-blue-600 hover:underline">Roster</a>
                            <a href="{{ route('instructor.courses.edit', $course->course_id) }}"
                               class="text-gray-500 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('instructor.courses.destroy', $course->course_id) }}"
                                  class="inline"
                                  onsubmit="return confirm('Delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $courses->links() }}</div>
@endif
@endsection
