@extends('layouts.app')

@section('title', 'Your DISC Report — DISC Report')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Your DISC Report</h1>
    <a href="{{ route('participant.report.download', $result->test_result_id) }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded transition">
        Download PDF
    </a>
</div>

@include('reports.partials.score-summary', ['score' => $score])
@include('reports.partials.sections', ['sections' => $sections, 'score' => $score, 'svgs' => $svgs])
@endsection
