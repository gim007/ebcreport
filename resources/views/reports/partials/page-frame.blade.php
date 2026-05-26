{{-- Top & bottom four-color bars + footer copyright (R-47).
     Absolute-positioned inside each .page; Chrome handles this cleanly. --}}
<div class="page-top">
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
</div>
<div class="page-bottom">
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
    <div class="bar-edge"></div>
    <div class="meta">&copy; {{ now()->format('Y') }} Spark Point Training LLC. All rights reserved.</div>
</div>
