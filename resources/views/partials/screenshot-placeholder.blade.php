{{--
    Neutral stand-in for a missing screenshot. Both stages use identical shapes so
    the placeholder never implies a result; only the tint differs. Expects $stage.
--}}
<div class="ph ph--{{ $stage }}" role="img" aria-label="Placeholder: {{ $stage }} screenshot not added yet">
    <div class="ph__chrome"><span></span><span></span><span></span><i></i></div>
    <div class="ph__body">
        <div class="ph__tiles"><span></span><span></span><span></span><span></span></div>
        <div class="ph__chart">
            @foreach ([40, 55, 48, 62, 58, 70, 66, 74] as $h)
                <span style="--h: {{ $h }}%"></span>
            @endforeach
        </div>
        <div class="ph__rows"><span></span><span></span><span></span></div>
    </div>
    <div class="ph__stamp">
        <x-icon name="image" size="18" />
        <span><strong>{{ ucfirst($stage) }} screenshot</strong> · placeholder</span>
    </div>
</div>
