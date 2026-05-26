@extends('layouts.app')

@section('title', 'Register — DISC Report')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-1">Create Your Account</h1>
    <p class="text-gray-600 mb-6">Register to take the DISC assessment for <strong>{{ $course->course_name }}</strong>.</p>

    <form method="POST" action="{{ route('participant.register.store', $course->course_id) }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
                <input id="first_name" name="first_name" type="text" required
                       value="{{ old('first_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="last_name">Last Name</label>
                <input id="last_name" name="last_name" type="text" required
                       value="{{ old('last_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email Address</label>
            <input id="email" name="email" type="email" required
                   value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="username">Username</label>
            <input id="username" name="username" type="text" required minlength="4" maxlength="50"
                   value="{{ old('username') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-red-400 @enderror">
            @error('username')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
            @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="gender">Gender <span class="text-gray-400 font-normal">(optional)</span></label>
            <select id="gender" name="gender"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Prefer not to say</option>
                <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
                <option value="Prefer not to say" {{ old('gender') === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="scholarship_code">
                Scholarship / Prepaid Code <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <input id="scholarship_code" name="scholarship_code" type="text"
                   value="{{ old('scholarship_code') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('scholarship_code') border-red-400 @enderror">
            @error('scholarship_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-1">Enter a code to waive the registration fee.</p>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
            Create Account &amp; Continue
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
        Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a>
    </p>
</div>
@endsection
