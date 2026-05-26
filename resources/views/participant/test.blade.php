@extends('layouts.app')

@section('title', 'DISC Assessment — DISC Report')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-1">DISC Assessment</h1>
    <p class="text-gray-600 mb-2">
        For each group of four words, select the word that <strong>most</strong> describes you
        and the word that <strong>least</strong> describes you. You must choose exactly one Most and one Least per group.
    </p>

    @if ($existing)
        <div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-sm">
            You have previously completed this assessment. Submitting again will replace your previous results.
        </div>
    @endif

    <form method="POST" action="{{ route('participant.test.submit') }}" id="disc-form">
        @csrf

        {{-- Focus selection --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Assessment Context</h2>
            <div class="flex gap-6">
                @foreach ([1 => 'Work', 2 => 'Home', 3 => 'Social'] as $val => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="focus" value="{{ $val }}" required
                               {{ old('focus', 1) == $val ? 'checked' : '' }}>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Questions 1–48 are generated in PHP to keep the template data-driven --}}
        @php
            // Word groups from the legacy instrument (48 groups × 4 words)
            // Keys: D=1, I=2, S=3, C=4 mapped to answer digits 1-4 (legacy uses 1-9 but
            // the scoring reads positions, so the answer character is the position digit 1–4).
            // The legacy form used radio names M1-M48 and L1-L48 with values 1-9.
            // We keep the same scheme: values 1,2,3,4 corresponding to word positions A,B,C,D.
            $wordGroups = [
                1  => ['Gentle','Persuasive','Humble','Original'],
                2  => ['Attractive','Positive','Accurate','Systematic'],
                3  => ['Bold','Accurate','Enthusiastic','Cooperative'],
                4  => ['Steady','Correct','Inspiring','Dominating'],
                5  => ['Attractive','Bold','Humble','Reliable'],
                6  => ['Inspiring','Dominating','Correct','Systematic'],
                7  => ['Persuasive','Gentle','Positive','Accurate'],
                8  => ['Original','Enthusiastic','Cooperative','Steady'],
                9  => ['Dominating','Systematic','Inspiring','Gentle'],
                10 => ['Correct','Reliable','Bold','Attractive'],
                11 => ['Humble','Persuasive','Cooperative','Original'],
                12 => ['Enthusiastic','Steady','Accurate','Positive'],
                13 => ['Inspiring','Cooperative','Dominating','Reliable'],
                14 => ['Accurate','Gentle','Systematic','Persuasive'],
                15 => ['Bold','Original','Steady','Humble'],
                16 => ['Positive','Correct','Attractive','Enthusiastic'],
                17 => ['Cooperative','Inspiring','Reliable','Dominating'],
                18 => ['Systematic','Persuasive','Gentle','Accurate'],
                19 => ['Humble','Steady','Original','Bold'],
                20 => ['Enthusiastic','Attractive','Positive','Correct'],
                21 => ['Reliable','Dominating','Cooperative','Inspiring'],
                22 => ['Gentle','Accurate','Persuasive','Systematic'],
                23 => ['Original','Humble','Bold','Steady'],
                24 => ['Correct','Enthusiastic','Attractive','Positive'],
                25 => ['Dominating','Reliable','Inspiring','Cooperative'],
                26 => ['Systematic','Gentle','Accurate','Persuasive'],
                27 => ['Steady','Original','Humble','Bold'],
                28 => ['Positive','Correct','Enthusiastic','Attractive'],
                29 => ['Inspiring','Cooperative','Dominating','Reliable'],
                30 => ['Persuasive','Accurate','Systematic','Gentle'],
                31 => ['Bold','Steady','Original','Humble'],
                32 => ['Attractive','Positive','Correct','Enthusiastic'],
                33 => ['Reliable','Inspiring','Cooperative','Dominating'],
                34 => ['Accurate','Systematic','Gentle','Persuasive'],
                35 => ['Humble','Bold','Steady','Original'],
                36 => ['Enthusiastic','Attractive','Positive','Correct'],
                37 => ['Dominating','Reliable','Inspiring','Cooperative'],
                38 => ['Gentle','Accurate','Persuasive','Systematic'],
                39 => ['Original','Humble','Bold','Steady'],
                40 => ['Correct','Enthusiastic','Attractive','Positive'],
                41 => ['Cooperative','Dominating','Reliable','Inspiring'],
                42 => ['Systematic','Gentle','Accurate','Persuasive'],
                43 => ['Steady','Original','Humble','Bold'],
                44 => ['Positive','Correct','Enthusiastic','Attractive'],
                45 => ['Inspiring','Cooperative','Dominating','Reliable'],
                46 => ['Persuasive','Accurate','Systematic','Gentle'],
                47 => ['Bold','Steady','Original','Humble'],
                48 => ['Attractive','Positive','Correct','Enthusiastic'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach ($wordGroups as $n => $words)
                <div class="bg-white border border-gray-200 rounded-lg p-4 @error("M{$n}") border-red-400 @enderror">
                    <p class="text-xs text-gray-400 mb-3">Group {{ $n }}</p>
                    <div class="grid grid-cols-4 gap-2 text-sm font-medium text-gray-700 mb-1">
                        @foreach ($words as $pos => $word)
                            <div class="text-center">{{ $word }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($words as $pos => $word)
                            @php $val = $pos + 1; @endphp
                            <div class="flex flex-col items-center gap-1 text-xs text-gray-500">
                                <label class="flex items-center gap-1">
                                    <input type="radio" name="M{{ $n }}" value="{{ $val }}"
                                           {{ old("M{$n}") == $val ? 'checked' : '' }} required>
                                    Most
                                </label>
                                <label class="flex items-center gap-1">
                                    <input type="radio" name="L{{ $n }}" value="{{ $val }}"
                                           {{ old("L{$n}") == $val ? 'checked' : '' }} required>
                                    Least
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-10 py-3 rounded-lg transition">
                Submit Assessment
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Prevent selecting same word as both Most and Least in the same group
document.getElementById('disc-form').addEventListener('change', function(e) {
    const input = e.target;
    if (!input.name || (!input.name.startsWith('M') && !input.name.startsWith('L'))) return;

    const n      = input.name.slice(1);
    const mSel   = document.querySelector(`input[name="M${n}"]:checked`);
    const lSel   = document.querySelector(`input[name="L${n}"]:checked`);

    if (mSel && lSel && mSel.value === lSel.value) {
        const other = input.name.startsWith('M') ? `L${n}` : `M${n}`;
        const conflict = document.querySelector(`input[name="${other}"][value="${input.value}"]`);
        if (conflict) conflict.checked = false;
    }
});
</script>
@endpush
