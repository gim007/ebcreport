@extends('layouts.instructor')

@section('title', 'Report — Instructor Portal')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.roster', $course->course_id) }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
        <div>
            <h1 class="text-xl font-bold">
                {{ $result->participant?->full_name ?? 'Participant' }} — DISC Report
            </h1>
            <p class="text-sm text-gray-500">{{ $course->course_name }} &middot; {{ $result->result_date?->format('M j, Y') }}</p>
        </div>
    </div>
</div>

@include('reports.partials.score-summary', ['score' => $score])
@include('reports.partials.sections', ['sections' => $sections, 'score' => $score, 'svgs' => $svgs])
@endsection
