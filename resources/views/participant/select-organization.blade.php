@extends('layouts.app')

@section('title', 'Select Organization — DISC Report')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Select Your Organization</h1>
    <p class="text-gray-600 mt-1">Choose the organization that is administering your DISC assessment.</p>
</div>

<livewire:organization-grid />
@endsection
