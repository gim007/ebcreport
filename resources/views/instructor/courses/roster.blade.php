@extends('layouts.instructor')

@section('title', '{{ $course->course_name }} — Roster')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
            <h1 class="text-2xl font-bold">{{ $course->course_name }}</h1>
        </div>
        @if ($course->term)
            <p class="text-gray-500 text-sm mt-1 ml-7">{{ $course->term }}{{ $course->schedule_time ? ' &middot; '.$course->schedule_time : '' }}</p>
        @endif
    </div>
</div>

@if ($results->isEmpty())
    <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-gray-500">
        No assessments have been completed for this course yet.
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b text-left text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">Participant</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">D</th>
                    <th class="px-5 py-3">I</th>
                    <th class="px-5 py-3">S</th>
                    <th class="px-5 py-3">C</th>
                    <th class="px-5 py-3">Dominant</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($results as $result)
                    @php $score = $result->score(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $result->participant?->full_name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $result->result_date?->format('M j, Y') ?? '—' }}
                        </td>
                        @foreach ($score->maskPercentile as $pct)
                            <td class="px-5 py-3 text-gray-700">{{ $pct }}</td>
                        @endforeach
                        <td class="px-5 py-3 font-medium text-blue-700">{{ $score->dominantLabel() }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('instructor.courses.roster.report', [$course->course_id, $result->test_result_id]) }}"
                               class="text-blue-600 hover:underline">View Report</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-sm text-gray-400 mt-3">{{ $results->count() }} {{ Str::plural('assessment', $results->count()) }}</p>
@endif
@endsection
