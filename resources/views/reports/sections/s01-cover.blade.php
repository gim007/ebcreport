{{-- S-01 Cover Page — R-47. Browsershot renders this with full CSS/SVG support. --}}
<div class="page" style="padding: 0;">
    {{-- Top color strip --}}
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
    <div class="bar-edge"></div>

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

    {{-- Bottom color strip + copyright --}}
    <div style="margin-top: auto;">
        <div style="text-align:center; font-size:9pt; color:#6b7280; padding:10pt 0;">
            &copy; {{ now()->format('Y') }} Spark Point Training LLC. All rights reserved.
        </div>
        <div class="bar-strip">
            <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
        </div>
        <div class="bar-edge"></div>
    </div>
</div>
