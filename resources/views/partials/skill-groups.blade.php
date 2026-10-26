{{-- Skill group cards. Expects $skills (group => items); optional $columns (2, 3 or 4). --}}
@php
    $groupIcons = [
        'Advertising platforms' => 'target',
        'Tracking & analytics' => 'pulse',
        'Growth & optimisation' => 'chart',
        'Social & AI content' => 'spark',
        'Leadership' => 'users',
    ];
@endphp

<div class="skills-grid skills-grid--{{ $columns ?? 4 }}">
    @foreach ($skills as $group => $items)
        <article class="skill-card" data-reveal-card data-tilt>
            <div class="skill-card__glow" aria-hidden="true"></div>
            <header class="skill-card__head">
                <span class="icon-chip icon-chip--sm"><x-icon :name="$groupIcons[$group] ?? 'spark'" size="20" /></span>
                <h3 class="skill-card__title">{{ $group }}</h3>
                <span class="skill-card__count" aria-label="{{ count($items) }} skills">{{ str_pad(count($items), 2, '0', STR_PAD_LEFT) }}</span>
            </header>
            <ul class="skill-card__list" role="list">
                @foreach ($items as $item)
                    @php
                        [$name, $detail] = array_pad(explode(' — ', $item, 2), 2, null);
                    @endphp
                    <li class="skill-card__item">
                        <x-icon name="check" size="16" />
                        <span>
                            {{ $name }}
                            @if ($detail)<small>{{ str_replace(', ', ' · ', $detail) }}</small>@endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </article>
    @endforeach
</div>
