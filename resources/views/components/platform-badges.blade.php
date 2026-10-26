@props(['platforms'])

<ul {{ $attributes->merge(['class' => 'platform-badges']) }} role="list">
    @foreach ($platforms as $platform)
        <li class="platform-badge platform-badge--{{ $platform }}">
            <x-icon :name="$platform" size="14" />
            {{ config('portfolio.platform_labels.'.$platform) }}
        </li>
    @endforeach
</ul>
