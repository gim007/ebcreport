@extends('layouts.instructor')

@section('title', 'My Account — Instructor Portal')

@section('content')
@php
    $selectedCountry = old('country', $instructor->ins_country);
    $selectedState   = old('state',   $instructor->ins_state);
    $isUs            = $selectedCountry === 'US';
    $timezones = [
        'America/New_York'    => 'Eastern Time (US)',
        'America/Chicago'     => 'Central Time (US)',
        'America/Denver'      => 'Mountain Time (US)',
        'America/Los_Angeles' => 'Pacific Time (US)',
        'America/Anchorage'   => 'Alaska Time',
        'Pacific/Honolulu'    => 'Hawaii-Aleutian Time',
        'Europe/London'       => 'London (GMT/BST)',
        'Europe/Berlin'       => 'Berlin / Central Europe',
        'Asia/Kolkata'        => 'India Standard Time',
        'Asia/Singapore'      => 'Singapore',
        'Australia/Sydney'    => 'Sydney',
    ];
@endphp

<h1 class="text-2xl font-bold mb-2">My Account</h1>
<p class="text-gray-500 text-sm mb-6">
    Update your contact information, address, and password. Your username, organization, and approval status are managed by an administrator.
</p>

{{-- ──────────────────────────────────────────────── --}}
{{-- Read-only header card                            --}}
{{-- ──────────────────────────────────────────────── --}}
<div class="bg-white border border-gray-200 rounded-lg p-5 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
    <div>
        <div class="text-gray-500 text-xs uppercase tracking-wide mb-0.5">Username</div>
        <div class="font-medium">{{ $instructor->user?->user_login_id }}</div>
    </div>
    <div>
        <div class="text-gray-500 text-xs uppercase tracking-wide mb-0.5">Organization</div>
        <div class="font-medium">{{ $instructor->organization?->uni_name ?? '—' }}</div>
    </div>
    <div>
        <div class="text-gray-500 text-xs uppercase tracking-wide mb-0.5">Approval status</div>
        <div>
            @php
                $approved = $instructor->isApproved();
                $rejected = $instructor->admin_approval === 'Rejected';
            @endphp
            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{
                $approved ? 'bg-green-100 text-green-800' :
                ($rejected ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
            }}">
                {{ $instructor->admin_approval ?? 'Pending' }}
            </span>
        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────── --}}
{{-- Profile form                                     --}}
{{-- ──────────────────────────────────────────────── --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold mb-4">Profile</h2>

    <form method="POST" action="{{ route('instructor.account.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Identity --}}
        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1" for="title">Title <span class="text-gray-400 font-normal">(optional)</span></label>
                <input id="title" name="title" type="text" maxlength="50"
                       value="{{ old('title', $instructor->ins_title) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="col-span-5">
                <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
                <input id="first_name" name="first_name" type="text" required
                       value="{{ old('first_name', $instructor->ins_fname) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="col-span-5">
                <label class="block text-sm font-medium mb-1" for="last_name">Last Name</label>
                <input id="last_name" name="last_name" type="text" required
                       value="{{ old('last_name', $instructor->ins_lname) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="gender">Gender <span class="text-gray-400 font-normal">(optional)</span></label>
            <select id="gender" name="gender"
                    class="w-full md:w-1/2 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— None —</option>
                @foreach (['Male', 'Female', 'Other', 'Prefer not to say'] as $g)
                    <option value="{{ $g }}" {{ old('gender', $instructor->ins_gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
        </div>

        {{-- Contact --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input id="email" name="email" type="email" required
                       value="{{ old('email', $instructor->ins_email) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="phone">
                    Phone <span class="text-gray-400 font-normal">(required for SMS recovery)</span>
                </label>
                <input id="phone" name="phone" type="tel" required maxlength="50"
                       value="{{ old('phone', $instructor->ins_phone) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Mailing address --}}
        <fieldset class="border border-gray-200 rounded p-4">
            <legend class="text-sm font-medium px-2">Mailing address</legend>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="address">Street address</label>
                    <input id="address" name="address" type="text" required maxlength="200"
                           value="{{ old('address', $instructor->ins_address) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="address_cont">Apt / Suite <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="address_cont" name="address_cont" type="text" maxlength="200"
                           value="{{ old('address_cont', $instructor->ins_address_cont) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="city">City</label>
                    <input id="city" name="city" type="text" required maxlength="100"
                           value="{{ old('city', $instructor->ins_city) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    {{-- Two state widgets share `name="state"`. JS toggles
                         visibility and the `disabled` attribute so only the
                         active one submits. Default view picks the right one
                         from the saved country (or old() after a validation
                         error). --}}
                    <label class="block text-sm font-medium mb-1" for="state_us">State / Province</label>

                    <select id="state_us" name="state" required
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

                    <input id="state_intl" name="state" type="text" required maxlength="100"
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
                    <input id="zip" name="zip" type="text" required maxlength="20"
                           value="{{ old('zip', $instructor->ins_zip) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="country">Country</label>
                    <select id="country" name="country" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-400 @enderror">
                        <option value="">— Select —</option>
                        @foreach (config('locations.countries') as $code => $name)
                            <option value="{{ $code }}" {{ $selectedCountry === $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="timezone">Timezone</label>
                    <select id="timezone" name="timezone" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach ($timezones as $tz => $label)
                            <option value="{{ $tz }}" {{ old('timezone', $instructor->ins_timezone) === $tz ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </fieldset>

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded transition text-sm">
            Save profile
        </button>
    </form>
</div>

{{-- ──────────────────────────────────────────────── --}}
{{-- Password form                                    --}}
{{-- ──────────────────────────────────────────────── --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-bold mb-4">Change password</h2>

    <form method="POST" action="{{ route('instructor.account.password') }}" class="space-y-4 max-w-md">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1" for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
            @error('current_password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="password">New password</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
            <p class="text-xs text-gray-500 mt-1">At least 8 characters.</p>
            @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded transition text-sm">
            Update password
        </button>
    </form>
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
