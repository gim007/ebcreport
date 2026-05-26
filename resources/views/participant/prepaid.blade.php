@extends('layouts.app')

@section('title', 'Redeem Code — DISC Report')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-1">Redeem a Code</h1>
    <p class="text-gray-600 mb-6">Enter your scholarship or prepaid code to add an assessment credit.</p>

    <form method="POST" action="{{ route('participant.prepaid.redeem') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="code">Code</label>
            <input id="code" name="code" type="text" required
                   value="{{ old('code') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-400 @enderror">
            @error('code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
            Redeem Code
        </button>
    </form>
</div>
@endsection
