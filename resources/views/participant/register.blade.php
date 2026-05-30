@extends('layouts.app')

@section('title', 'Register — DISC Report')

@php
    $isUs = old('country') === 'US';
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    @include('participant._progress', ['step' => 5])

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

        {{-- R-31: phone is optional at registration; required later for SMS recovery. --}}
        <div>
            <label class="block text-sm font-medium mb-1" for="phone">
                Phone <span class="text-gray-400 font-normal">(optional &mdash; required to use SMS recovery)</span>
            </label>
            <input id="phone" name="phone" type="tel" maxlength="50"
                   value="{{ old('phone') }}" placeholder="+1 (555) 123-4567"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
            @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Mailing address — legacy parity --}}
        <fieldset class="border border-gray-200 rounded p-4">
            <legend class="text-sm font-medium px-2">Mailing address <span class="text-gray-400 font-normal">(optional)</span></legend>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1" for="address">Street address</label>
                <input id="address" name="address" type="text" maxlength="200"
                       value="{{ old('address') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="city">City</label>
                    <input id="city" name="city" type="text" maxlength="100"
                           value="{{ old('city') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="zip">ZIP / Postal code</label>
                    <input id="zip" name="zip" type="text" maxlength="20"
                           value="{{ old('zip') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    {{-- Two state widgets share the `state` name. JS toggles
                         visibility AND the `disabled` attribute so only the
                         active one submits. Default view picks the right one
                         from old('country') after a validation error. --}}
                    <label class="block text-sm font-medium mb-1" for="state_us">State / Province</label>

                    <select id="state_us" name="state"
                            data-state-widget="us"
                            @class([
                                'w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                'hidden' => ! $isUs,
                                'border-red-400' => $errors->has('state'),
                            ])
                            @if (! $isUs) disabled @endif>
                        <option value="">— Select state —</option>
                        @foreach (config('locations.us_states') as $code => $name)
                            <option value="{{ $code }}" {{ $isUs && old('state') === $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <input id="state_intl" name="state" type="text" maxlength="100"
                           data-state-widget="intl"
                           value="{{ $isUs ? '' : old('state') }}"
                           placeholder="State, province, or region"
                           @class([
                               'w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500',
                               'hidden' => $isUs,
                               'border-red-400' => $errors->has('state'),
                           ])
                           @if ($isUs) disabled @endif>

                    @error('state')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="country">Country</label>
                    <select id="country" name="country"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-400 @enderror">
                        <option value="">— Select —</option>
                        @foreach (config('locations.countries') as $code => $name)
                            <option value="{{ $code }}" {{ old('country') === $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>

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

@push('scripts')
<script>
    (function () {
        const country = document.getElementById('country');
        const usSelect = document.querySelector('[data-state-widget="us"]');
        const intlInput = document.querySelector('[data-state-widget="intl"]');
        if (!country || !usSelect || !intlInput) return;

        function swap() {
            const isUs = country.value === 'US';
            usSelect.classList.toggle('hidden', !isUs);
            intlInput.classList.toggle('hidden', isUs);
            usSelect.disabled = !isUs;
            intlInput.disabled = isUs;
            // Clear the inactive value so toggling US ⇄ intl doesn't carry
            // stale text into the submitted payload.
            if (isUs) intlInput.value = '';
            else usSelect.value = '';
        }

        country.addEventListener('change', swap);
    })();
</script>
@endpush
@endsection
