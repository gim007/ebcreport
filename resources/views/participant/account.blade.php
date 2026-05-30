@extends('layouts.app')

@section('title', 'My Account — DISC Report')

@section('content')
@php
    // Pre-compute country/state widget defaults so a fresh page load AND a
    // re-render after validation both pick the right state widget.
    $selectedCountry = old('country', $participant?->stud_country);
    $selectedState   = old('state',   $participant?->stud_state);
    $isUs            = $selectedCountry === 'US';
@endphp

<h1 class="text-2xl font-bold mb-6">My Account</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ─────────────────────────────────────────────── --}}
    {{-- Summary + assessment history (left rail)        --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="lg:col-span-1 space-y-4">

        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-lg font-semibold mb-3">Account Summary</h2>
            @if ($participant)
                <p class="text-gray-800 font-medium">{{ $participant->full_name }}</p>
                <p class="text-gray-500 text-sm break-all">{{ $user->user_email }}</p>
                <p class="text-gray-500 text-sm mt-1">Username: <span class="font-mono">{{ $user->user_login_id }}</span></p>

                <div class="mt-4 px-3 py-2 bg-blue-50 border border-blue-100 rounded text-sm flex items-center justify-between">
                    <span>Credits remaining</span>
                    <span class="font-bold text-blue-700 text-lg">{{ $participant->tot_credit }}</span>
                </div>

                @if ($participant->tot_credit > 0)
                    <a href="{{ route('participant.test') }}"
                       class="mt-3 block text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded transition">
                        Take Assessment
                    </a>
                @else
                    <a href="{{ route('participant.organizations') }}"
                       class="mt-3 block text-center bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 rounded transition">
                        Enroll in Another Course
                    </a>
                @endif
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-lg font-semibold mb-3">Assessment History</h2>
            @if ($results && $results->isNotEmpty())
                <ul class="divide-y text-sm">
                    @foreach ($results as $result)
                        @if ($result->hasBeenTaken())
                            <li class="py-2 flex items-center justify-between">
                                <span class="text-gray-700">{{ $result->result_date?->format('M j, Y') }}</span>
                                <span class="text-xs">
                                    <a href="{{ route('participant.report.show', $result->test_result_id) }}"
                                       class="text-blue-600 hover:underline">View</a>
                                    &middot;
                                    <a href="{{ route('participant.report.download', $result->test_result_id) }}"
                                       class="text-blue-600 hover:underline">PDF</a>
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm">No assessments taken yet.</p>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-lg font-semibold mb-3">Change Password</h2>
            <form method="POST" action="{{ route('participant.account.password') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1" for="current_password">Current Password</label>
                    <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" for="password">New Password</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                    @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1.5 rounded transition">
                    Update Password
                </button>
            </form>
        </div>

    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- Profile edit form (right)                       --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-lg font-bold mb-1">Profile</h2>
            <p class="text-gray-500 text-sm mb-5">
                Update your personal details. Username and enrollment (instructor, course, credits) are managed by your administrator.
            </p>

            @if (session('status'))
                <div class="mb-4 px-3 py-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('participant.account.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="first_name">First name</label>
                        <input id="first_name" name="first_name" type="text" required maxlength="100"
                               value="{{ old('first_name', $participant?->stud_fname) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                        @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="last_name">Last name</label>
                        <input id="last_name" name="last_name" type="text" required maxlength="100"
                               value="{{ old('last_name', $participant?->stud_lname) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                        @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="email">Email</label>
                        <input id="email" name="email" type="email" required maxlength="255"
                               value="{{ old('email', $participant?->stud_email ?: $user->user_email) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="phone">
                            Phone <span class="text-gray-400 font-normal text-xs">(optional)</span>
                        </label>
                        <input id="phone" name="phone" type="tel" maxlength="50"
                               value="{{ old('phone', $participant?->stud_phone) }}"
                               placeholder="+1 (555) 123-4567"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                        @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="gender">
                        Gender <span class="text-gray-400 font-normal text-xs">(optional)</span>
                    </label>
                    <select id="gender" name="gender"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Prefer not to say —</option>
                        @foreach (['Male', 'Female', 'Other', 'Prefer not to say'] as $opt)
                            <option value="{{ $opt }}" {{ old('gender', $participant?->stud_gender) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Mailing address --}}
                <fieldset class="border border-gray-200 rounded p-4">
                    <legend class="text-sm font-medium px-2">Mailing address <span class="text-gray-400 font-normal">(optional)</span></legend>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1" for="address">Street address</label>
                        <input id="address" name="address" type="text" maxlength="200"
                               value="{{ old('address', $participant?->stud_address) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-sm font-medium mb-1" for="city">City</label>
                            <input id="city" name="city" type="text" maxlength="100"
                                   value="{{ old('city', $participant?->stud_city) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            {{-- Two state widgets share name="state". JS toggles
                                 visibility and disabled so only the visible one
                                 submits. Default view comes from saved country. --}}
                            <label class="block text-sm font-medium mb-1" for="state_us">State / Province</label>

                            <select id="state_us" name="state"
                                    data-state-widget="us"
                                    @class([
                                        'w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500',
                                        'hidden' => ! $isUs,
                                        'border-red-400' => $errors->has('state'),
                                    ])
                                    @if (! $isUs) disabled @endif>
                                <option value="">— Select state —</option>
                                @foreach (config('locations.us_states') as $code => $name)
                                    <option value="{{ $code }}" {{ $isUs && $selectedState === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>

                            <input id="state_intl" name="state" type="text" maxlength="100"
                                   data-state-widget="intl"
                                   value="{{ $isUs ? '' : $selectedState }}"
                                   placeholder="State, province, or region"
                                   @class([
                                       'w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500',
                                       'hidden' => $isUs,
                                       'border-red-400' => $errors->has('state'),
                                   ])
                                   @if ($isUs) disabled @endif>

                            @error('state')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" for="zip">ZIP / Postal</label>
                            <input id="zip" name="zip" type="text" maxlength="20"
                                   value="{{ old('zip', $participant?->stud_zip) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="country">Country</label>
                        <select id="country" name="country"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-400 @enderror">
                            <option value="">— Select —</option>
                            @foreach (config('locations.countries') as $code => $name)
                                <option value="{{ $code }}" {{ $selectedCountry === $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </fieldset>

                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-6 rounded transition text-sm">
                    Save profile
                </button>
            </form>
        </div>
    </div>
</div>

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
@endsection
