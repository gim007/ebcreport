<div class="page">
    @include('reports.partials.page-frame')

    <h2>Motivating Factors</h2>
    <p>Understanding what drives <strong>{{ $result->participant?->stud_fname ?? 'you' }}</strong> — and what depletes their energy — is essential for sustained performance and engagement. These are not preferences in the casual sense; they are psychological needs that, when met, produce energy, and when ignored, produce friction.</p>
    <p>{!! $sections['motivating'] !!}</p>
</div>
