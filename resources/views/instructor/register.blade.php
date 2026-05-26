<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instructor Registration — DISC Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12">

<div class="w-full max-w-lg bg-white border border-gray-200 rounded-lg shadow-sm p-8">
    <h1 class="text-2xl font-bold mb-1">Instructor Registration</h1>
    <p class="text-gray-600 mb-6 text-sm">Create your instructor account. Your account will be reviewed before full access is granted.</p>

    <form method="POST" action="{{ route('instructor.register.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
                <input id="first_name" name="first_name" type="text" required
                       value="{{ old('first_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="last_name">Last Name</label>
                <input id="last_name" name="last_name" type="text" required
                       value="{{ old('last_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email Address</label>
            <input id="email" name="email" type="email" required
                   value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="org_id">Organization</label>
            <select id="org_id" name="org_id" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('org_id') border-red-400 @enderror">
                <option value="">— Select your organization —</option>
                @foreach ($organizations as $org)
                    <option value="{{ $org->uni_id }}" {{ old('org_id') == $org->uni_id ? 'selected' : '' }}>
                        {{ $org->uni_name }}
                    </option>
                @endforeach
            </select>
            @error('org_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

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

        <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded transition text-sm">
            Create Instructor Account
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
        Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a>
    </p>
</div>

</body>
</html>
