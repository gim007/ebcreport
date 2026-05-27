{{-- Participant registration progress breadcrumb.
     Usage: @include('participant._progress', ['step' => 3])
     Steps: 1 Terms · 2 Organization · 3 Instructor · 4 Course · 5 Your Info · 6 Payment --}}
@php
    $steps = [
        ['label' => 'Terms',        'short' => 'Terms'],
        ['label' => 'Organization', 'short' => 'Org'],
        ['label' => 'Instructor',   'short' => 'Inst.'],
        ['label' => 'Course',       'short' => 'Course'],
        ['label' => 'Your Info',    'short' => 'Info'],
        ['label' => 'Payment',      'short' => 'Pay'],
    ];
    $current = max(1, min(count($steps), (int) ($step ?? 1)));
@endphp

<nav aria-label="Registration progress" class="mb-8">
    {{-- Mobile: compact "Step N of 6" --}}
    <div class="md:hidden text-sm text-gray-600 mb-2">
        Step {{ $current }} of {{ count($steps) }} &mdash;
        <span class="font-semibold text-gray-900">{{ $steps[$current - 1]['label'] }}</span>
    </div>

    {{-- Desktop: numbered breadcrumb --}}
    <ol class="hidden md:flex items-center justify-between w-full text-xs">
        @foreach ($steps as $i => $s)
            @php
                $n        = $i + 1;
                $isDone   = $n <  $current;
                $isActive = $n === $current;
                $circle   = $isActive
                    ? 'bg-blue-600 text-white ring-4 ring-blue-100 border-blue-600'
                    : ($isDone
                        ? 'bg-green-600 text-white border-green-600'
                        : 'bg-white text-gray-400 border-gray-300');
                $label    = $isActive
                    ? 'text-gray-900 font-semibold'
                    : ($isDone ? 'text-green-700' : 'text-gray-500');
                $bar      = $isDone ? 'bg-green-500' : 'bg-gray-200';
            @endphp
            <li class="flex-1 flex items-center">
                <div class="flex flex-col items-center min-w-0">
                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-semibold {{ $circle }}">
                        @if ($isDone)
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.296a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 011.414-1.414L8.5 12.086l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="mt-1 truncate max-w-[7rem] text-center {{ $label }}">{{ $s['label'] }}</span>
                </div>
                @if (! $loop->last)
                    <div class="flex-1 h-0.5 mx-2 -mt-5 {{ $bar }}"></div>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
