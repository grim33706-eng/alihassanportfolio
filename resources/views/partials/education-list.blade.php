{{-- Education and certification cards. Expects $education and $certifications. --}}
<div class="edu-list">
    @foreach ($education as $item)
        <div class="edu-card" data-reveal-card>
            <span class="icon-chip icon-chip--sm"><x-icon name="graduation" size="20" /></span>
            <div>
                <p class="edu-card__title">{{ $item['title'] }}</p>
                <p class="edu-card__school">{{ $item['school'] }}</p>
                <p class="edu-card__period">{{ $item['period'] }}</p>
            </div>
        </div>
    @endforeach
    @foreach ($certifications as $cert)
        <div class="edu-card" data-reveal-card>
            <span class="icon-chip icon-chip--sm"><x-icon name="check" size="20" /></span>
            <div>
                <p class="edu-card__title">{{ $cert['title'] }}</p>
                <p class="edu-card__school">{{ $cert['issuer'] }}</p>
                <p class="edu-card__period">{{ $cert['year'] }}</p>
            </div>
        </div>
    @endforeach
</div>
