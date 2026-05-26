<div class="page">
    @include('reports.partials.page-frame')

    <h2 style="border-left-color:#f9a825;">Profile Tensions</h2>
    <p>The chart below illustrates six profile tensions — each representing the relative difference between pairs of DISC dimensions. Both the Mask (solid) and Latent (hatched) profiles are shown.</p>
    <div style="text-align:center; margin: 12pt 0;">{!! $charts['tension'] !!}</div>
</div>
