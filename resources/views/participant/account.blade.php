@extends('layouts.app')

@section('title', 'My Account — DISC Report')

@section('content')
<h1 class="text-2xl font-bold mb-6">My Account</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Profile card --}}
    <div class="md:col-span-1">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-lg font-semibold mb-3">Profile</h2>
            @if ($participant)
                <p class="text-gray-800 font-medium">{{ $participant->full_name }}</p>
                <p class="text-gray-500 text-sm">{{ $user->user_email }}</p>
                <p class="mt-3 text-sm">
                    Credits remaining: <span class="font-bold text-blue-700">{{ $participant->tot_credit }}</span>
                </p>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 mt-4">
            <h2 class="text-lg font-semibold mb-3">Change Password</h2>
            <form method="POST" action="{{ route('participant.account.password') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1.5 rounded transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>

    {{-- Assessment history --}}
    <div class="md:col-span-2">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Assessment History</h2>
                @if ($participant && $participant->tot_credit > 0)
                    <a href="{{ route('participant.test') }}"
                       class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded transition">
                        Take Assessment
                    </a>
                @endif
            </div>

            @if ($results && $results->isNotEmpty())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2">Date</th>
                            <th class="pb-2">Profile</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $result)
                            @if ($result->hasBeenTaken())
                                <tr class="border-b last:border-0">
                                    <td class="py-2">{{ $result->result_date?->format('M j, Y') }}</td>
                                    <td class="py-2">
                                        @php $score = $result->score(); @endphp
                                        {{ $score->dominantLabel() }}
                                    </td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('participant.report.show', $result->test_result_id) }}"
                                           class="text-blue-600 hover:underline">View Report</a>
                                        &nbsp;&middot;&nbsp;
                                        <a href="{{ route('participant.report.download', $result->test_result_id) }}"
                                           class="text-blue-600 hover:underline">PDF</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">No assessments taken yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
