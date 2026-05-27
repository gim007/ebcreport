{{-- S-01 Cover Page — R-47.
     The global .print-header / .print-footer render the four-color bars and
     copyright on every page including this one; the cover only supplies its
     own internal layout (compass + logotype + name). --}}
<div class="page">
    <div class="cover">
        <div class="cover-hero">
            <div class="cover-compass">{!! $charts['compass'] !!}</div>
            <div class="cover-logotype">
                <div class="word">DISC</div>
                <div class="underline">
                    <span style="background:#2e7d32;"></span>
                    <span style="background:#c62828;"></span>
                    <span style="background:#1565c0;"></span>
                    <span style="background:#f9a825;"></span>
                </div>
                <div class="report">R E P O R T</div>
                <div class="tag">Discover.&nbsp;&nbsp;Adapt.&nbsp;&nbsp;Connect.</div>
            </div>
        </div>

        <div class="cover-prepared">PREPARED FOR</div>
        <div class="cover-name">{{ $result->participant?->full_name ?? 'Participant' }}</div>
        <div class="cover-date">{{ ($result->result_date ?? now())->format('F d, Y') }}</div>

        @if (! empty($logoData))
            <div class="cover-logo-slot">
                <img src="{{ $logoData }}" alt="{{ $organization?->uni_name }}">
            </div>
        @endif
    </div>
</div>
