<div class="page">

    <h2>Your DISC Profile</h2>
    <h3>Scoring Summary</h3>

    <table class="scoring-grid">
        <tr>
            <td class="mask">
                <div class="chart-title">Mask</div>
                <div class="chart-sub">Normal Everyday Conditions</div>
                <div style="text-align:center;">{!! $charts['mask'] !!}</div>
                <div class="chart-cap">
                    <strong class="dim-d">Mask</strong> &mdash; Your Adapted Style. The Mask graph reflects how you tend to behave in your everyday working environment — the style you consciously or unconsciously adopt to meet the demands of your current role or situation.
                </div>
            </td>
            <td class="shift">
                <div class="chart-title">Shift</div>
                <div class="chart-sub">The Difference</div>
                <div style="text-align:center;">{!! $charts['shift'] !!}</div>
                <div class="chart-cap">
                    <strong class="dim-s">Shift</strong> &mdash; The Difference. The Shift graph shows the degree to which your adapted style (Mask) differs from your natural style (Latent). Small shifts indicate your environment allows you to show up close to your natural self.
                </div>
            </td>
            <td class="latent">
                <div class="chart-title">Latent</div>
                <div class="chart-sub">Stressful Conditions</div>
                <div style="text-align:center;">{!! $charts['latent'] !!}</div>
                <div class="chart-cap">
                    <strong class="dim-c">Latent</strong> &mdash; Your Natural Style. The Latent graph reveals your instinctive, core behavioral style — how you naturally respond when under pressure, stress, or when your guard is down.
                </div>
            </td>
        </tr>
    </table>

    <div class="snapshot">
        <span class="h">Snapshot of Your Style</span>
        @foreach ($snapshotTags as $tag)
            <span class="tag">{{ $tag }}</span>
        @endforeach
    </div>
</div>
