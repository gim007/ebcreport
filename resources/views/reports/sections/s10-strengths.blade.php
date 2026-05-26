<div class="page">
    @include('reports.partials.page-frame')

    <h2>Strengths &amp; Advantages</h2>

    <p>Every DISC style brings a distinct and genuine contribution to the groups and organizations it is part of. The strengths below reflect what <strong>{{ $result->participant?->stud_fname ?? 'this participant' }}</strong> naturally does well — the qualities that emerge without significant effort and that others come to rely on.</p>

    <p style="margin-top:10pt;">{!! $sections['strengths'] !!}</p>
</div>
