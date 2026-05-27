<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instructor Registration &mdash; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Four-color page chrome (matches landing / login / terms) --}}
<div class="flex h-[6pt]">
    <div class="flex-1 bg-[#2e7d32]"></div>
    <div class="flex-1 bg-[#c62828]"></div>
    <div class="flex-1 bg-[#1565c0]"></div>
    <div class="flex-1 bg-[#f9a825]"></div>
</div>
<div class="h-1 bg-gray-900"></div>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8">
        <h1 class="text-2xl font-bold mb-1">Instructor Registration</h1>
        <p class="text-gray-600 mb-6 text-sm">
            Create your instructor account. You'll receive an email verification link after submitting.
            An administrator will review and approve your account before full access is granted.
        </p>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-300 text-red-800 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('instructor.register.store') }}" class="space-y-5">
            @csrf

            {{-- Identity --}}
            <div class="grid grid-cols-12 gap-3">
                <div class="col-span-3">
                    <label class="block text-sm font-medium mb-1" for="title">Title <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="title" name="title" type="text" maxlength="50"
                           value="{{ old('title') }}" placeholder="Dr."
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-4">
                    <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" required value="{{ old('first_name') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                    @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-5">
                    <label class="block text-sm font-medium mb-1" for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" required value="{{ old('last_name') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                    @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="gender">Gender <span class="text-gray-400 font-normal">(optional)</span></label>
                <select id="gender" name="gender"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Prefer not to say</option>
                    @foreach (['Male', 'Female', 'Other', 'Prefer not to say'] as $g)
                        <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Contact (R-31 phone required) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="email">Email Address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="phone">Phone</label>
                    <input id="phone" name="phone" type="tel" required maxlength="50"
                           value="{{ old('phone') }}" placeholder="+1 (555) 123-4567"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Organization --}}
            <div>
                <label class="block text-sm font-medium mb-1" for="org_id">Organization</label>
                <select id="org_id" name="org_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('org_id') border-red-400 @enderror">
                    <option value="">&mdash; Select your organization &mdash;</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->uni_id }}" {{ old('org_id') == $org->uni_id ? 'selected' : '' }}>
                            {{ $org->uni_name }}
                        </option>
                    @endforeach
                </select>
                @error('org_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Mailing address --}}
            <fieldset class="border border-gray-200 rounded p-4">
                <legend class="text-sm font-medium px-2">Mailing address</legend>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="address">Street address</label>
                        <input id="address" name="address" type="text" required maxlength="200" value="{{ old('address') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="address_cont">Apt / Suite <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input id="address_cont" name="address_cont" type="text" maxlength="200" value="{{ old('address_cont') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="city">City</label>
                        <input id="city" name="city" type="text" required maxlength="100" value="{{ old('city') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="state">
                            State / Province <span class="text-gray-400 font-normal text-xs">(US: 2-letter)</span>
                        </label>
                        <input id="state" name="state" type="text" required maxlength="100" value="{{ old('state') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('state') border-red-400 @enderror">
                        @error('state')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="zip">ZIP / Postal</label>
                        <input id="zip" name="zip" type="text" required maxlength="20" value="{{ old('zip') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="country">Country</label>
                        <select id="country" name="country" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-400 @enderror">
                            <option value="">&mdash; Select &mdash;</option>
                            @foreach ([
                                'US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom',
                                'AU' => 'Australia', 'IN' => 'India', 'DE' => 'Germany', 'FR' => 'France',
                                'IE' => 'Ireland', 'NZ' => 'New Zealand', 'ZA' => 'South Africa',
                                'MX' => 'Mexico', 'BR' => 'Brazil', 'JP' => 'Japan',
                                'SG' => 'Singapore', 'NL' => 'Netherlands', 'ES' => 'Spain',
                            ] as $code => $name)
                                <option value="{{ $code }}" {{ old('country') === $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="timezone">Timezone</label>
                        <select id="timezone" name="timezone" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">&mdash; Select &mdash;</option>
                            @foreach ([
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
                            ] as $tz => $label)
                                <option value="{{ $tz }}" {{ old('timezone') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

            {{-- Account credentials --}}
            <fieldset class="border border-gray-200 rounded p-4">
                <legend class="text-sm font-medium px-2">Sign-in credentials</legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="username">Username</label>
                        <input id="username" name="username" type="text" required minlength="4" maxlength="50"
                               value="{{ old('username') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-red-400 @enderror">
                        @error('username')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="password">Password</label>
                        <input id="password" name="password" type="password" required minlength="8"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </fieldset>

            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded transition text-sm">
                Create Instructor Account &amp; Send Verification Email
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a>
        </p>
    </div>
</div>

</body>
</html>
