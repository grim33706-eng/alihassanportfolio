@props(['service', 'index' => null])

<article {{ $attributes->merge(['class' => 'service-card']) }} data-tilt data-reveal-card>
    <div class="service-card__glow" aria-hidden="true"></div>
    <div class="service-card__head">
        <span class="icon-chip"><x-icon :name="$service['icon']" /></span>
        @if ($index)
            <span class="service-card__index">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
        @endif
    </div>
    <h3 class="service-card__title">{{ $service['title'] }}</h3>
    <p class="service-card__text">{{ $service['text'] }}</p>
    @if (! empty($service['points']))
        <ul class="check-list" role="list">
            @foreach ($service['points'] as $point)
                <li><x-icon name="check" size="16" /> {{ $point }}</li>
            @endforeach
        </ul>
    @endif
</article>
