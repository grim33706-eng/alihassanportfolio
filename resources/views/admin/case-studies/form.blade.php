@extends('admin.layout')

@php
    $cs = $caseStudy;
    $editing = $cs->exists;
    $metrics = old('metrics', $cs->metrics ?? []);
    $metricRows = array_pad(array_values($metrics), max(4, count($metrics) + 1), []);
    $platforms = old('platforms', $cs->platforms ?? []);
@endphp

@section('title', $editing ? 'Edit case study' : 'New case study')

@section('content')
<div class="admin-head">
    <div>
        <a href="{{ route('admin.case-studies.index') }}" class="admin-back"><x-icon name="chevron-left" size="16" /> All case studies</a>
        <h1 class="admin-title">{{ $editing ? $cs->title : 'New case study' }}</h1>
    </div>
    @if ($editing)
        <a href="{{ route('results') }}#case-{{ $cs->slug }}" target="_blank" rel="noopener" class="btn-ui btn-ui--ghost btn-ui--sm"><span class="btn-ui__label">Preview</span><x-icon name="arrow-up-right" size="16" /></a>
    @endif
</div>

<form method="POST" enctype="multipart/form-data"
    action="{{ $editing ? route('admin.case-studies.update', $cs) : route('admin.case-studies.store') }}" class="admin-form">
    @csrf
    @if ($editing) @method('PUT') @endif

    {{-- ===== Details ===== --}}
    <section class="panel admin-section">
        <h2 class="admin-section__title">Details</h2>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="admin-label" for="title">Title *</label>
                <input class="admin-input" id="title" name="title" value="{{ old('title', $cs->title) }}" required maxlength="160">
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="slug">URL slug <small>(auto from title)</small></label>
                <input class="admin-input" id="slug" name="slug" value="{{ old('slug', $cs->slug) }}" maxlength="120">
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="brand">Brand name <small>(optional — leave empty if confidential)</small></label>
                <input class="admin-input" id="brand" name="brand" value="{{ old('brand', $cs->brand) }}" maxlength="120">
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="industry">Industry *</label>
                <input class="admin-input" id="industry" name="industry" value="{{ old('industry', $cs->industry) }}" required maxlength="120">
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="country">Target country / market *</label>
                <input class="admin-input" id="country" name="country" value="{{ old('country', $cs->country) }}" required maxlength="120">
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="category">Category *</label>
                <select class="admin-input" id="category" name="category" required>
                    @foreach (config('portfolio.categories') as $key => $label)
                        <option value="{{ $key }}" @selected(old('category', $cs->category) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="admin-label" for="period">Comparison period</label>
                <input class="admin-input" id="period" name="period" value="{{ old('period', $cs->period) }}" maxlength="80" placeholder="e.g. Jan – Mar 2026 vs Apr – Jun 2026">
            </div>
            <div class="col-md-4">
                <span class="admin-label">Platforms *</span>
                <div class="admin-checks">
                    @foreach (config('portfolio.platform_labels') as $key => $label)
                        <label class="admin-check"><input type="checkbox" name="platforms[]" value="{{ $key }}" @checked(in_array($key, $platforms, true))> {{ $label }}</label>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Story ===== --}}
    <section class="panel admin-section">
        <h2 class="admin-section__title">What Ali did &amp; what improved</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="admin-label" for="strategy">Strategy / work performed *</label>
                <textarea class="admin-input" id="strategy" name="strategy" rows="4" required maxlength="3000">{{ old('strategy', $cs->strategy) }}</textarea>
            </div>
            <div class="col-12">
                <label class="admin-label" for="improvements">Improvements achieved <small>(only what the screenshots support)</small></label>
                <textarea class="admin-input" id="improvements" name="improvements" rows="3" maxlength="3000">{{ old('improvements', $cs->improvements) }}</textarea>
            </div>
        </div>
    </section>

    {{-- ===== Metrics ===== --}}
    <section class="panel admin-section">
        <h2 class="admin-section__title">Verified metrics <small>(optional)</small></h2>
        <p class="admin-muted">Only enter figures visible in the screenshots. Leave a row empty to skip it. Numeric values animate and show the % change automatically.</p>
        <div class="admin-metrics">
            <div class="admin-metrics__head"><span>Metric</span><span>Before</span><span>After</span><span>Unit</span><span>Better when</span></div>
            @foreach ($metricRows as $i => $m)
                <div class="admin-metrics__row">
                    <input class="admin-input" name="metrics[{{ $i }}][label]" value="{{ $m['label'] ?? '' }}" placeholder="e.g. ROAS" aria-label="Metric {{ $i + 1 }} label">
                    <input class="admin-input" name="metrics[{{ $i }}][before]" value="{{ $m['before'] ?? '' }}" placeholder="1.8" aria-label="Metric {{ $i + 1 }} before">
                    <input class="admin-input" name="metrics[{{ $i }}][after]" value="{{ $m['after'] ?? '' }}" placeholder="4.2" aria-label="Metric {{ $i + 1 }} after">
                    <input class="admin-input" name="metrics[{{ $i }}][unit]" value="{{ $m['unit'] ?? '' }}" placeholder="x, %, PKR" aria-label="Metric {{ $i + 1 }} unit">
                    <select class="admin-input" name="metrics[{{ $i }}][direction]" aria-label="Metric {{ $i + 1 }} direction">
                        <option value="up" @selected(($m['direction'] ?? 'up') === 'up')>Higher</option>
                        <option value="down" @selected(($m['direction'] ?? '') === 'down')>Lower (e.g. CPL)</option>
                    </select>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== Screenshots ===== --}}
    <section class="panel admin-section">
        <h2 class="admin-section__title">Screenshots</h2>
        <p class="admin-muted">JPG, PNG or WebP, up to 8 MB each. The first image of each stage (lowest order number) is used in the comparison slider, so use the same view and size for before and after where possible.</p>

        <div class="row g-4">
            @foreach (['before' => 'Before Ali’s work', 'after' => 'After Ali’s work'] as $stage => $heading)
                <div class="col-lg-6">
                    <div class="admin-stage admin-stage--{{ $stage }}">
                        <h3 class="admin-stage__title">{{ $heading }}</h3>

                        @if ($editing)
                            <div class="admin-shots">
                                @forelse ($cs->images->where('stage', $stage) as $image)
                                    <div class="admin-shot">
                                        <a href="{{ $image->url() }}" target="_blank" rel="noopener"><img src="{{ $image->url() }}" alt="" loading="lazy"></a>
                                        <input class="admin-input" name="images[{{ $image->id }}][caption]" value="{{ $image->caption }}" placeholder="Caption" aria-label="Caption">
                                        <div class="admin-shot__row">
                                            <label>Order <input class="admin-input admin-input--xs" type="number" min="0" name="images[{{ $image->id }}][sort_order]" value="{{ $image->sort_order }}"></label>
                                            <label class="admin-check admin-check--danger"><input type="checkbox" name="images[{{ $image->id }}][delete]" value="1"> Remove</label>
                                        </div>
                                    </div>
                                @empty
                                    <p class="admin-muted">No {{ $stage }} screenshots yet.</p>
                                @endforelse
                            </div>
                        @endif

                        <label class="admin-drop">
                            <x-icon name="image" size="22" />
                            <span>Add {{ $stage }} screenshots</span>
                            <input type="file" name="{{ $stage }}_images[]" accept="image/jpeg,image/png,image/webp" multiple data-file-count>
                            <small data-file-label>No files chosen</small>
                        </label>
                        <input class="admin-input mt-2" name="new_captions[{{ $stage }}]" placeholder="Caption for new {{ $stage }} uploads (optional)" maxlength="200">
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== Visibility ===== --}}
    <section class="panel admin-section">
        <h2 class="admin-section__title">Visibility</h2>
        <div class="admin-checks admin-checks--stack">
            <label class="admin-check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $cs->is_published))> <span><strong>Published</strong> — shown on the website</span></label>
            <label class="admin-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $cs->is_featured))> <span><strong>Featured</strong> — also shown on the Home and Portfolio pages</span></label>
            <label class="admin-check"><input type="checkbox" name="is_demo" value="1" @checked(old('is_demo', $cs->is_demo))> <span><strong>Demonstration</strong> — labelled as a placeholder. Untick once real screenshots are added.</span></label>
        </div>
        <div class="mt-3" style="max-width: 200px">
            <label class="admin-label" for="sort_order">Display order <small>(lower first)</small></label>
            <input class="admin-input" type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', $cs->sort_order ?? 0) }}">
        </div>
    </section>

    <div class="admin-actions">
        <x-button type="submit" icon="check">{{ $editing ? 'Save changes' : 'Create case study' }}</x-button>
    </div>
</form>

@if ($editing)
    <form method="POST" action="{{ route('admin.case-studies.destroy', $cs) }}" class="admin-delete"
        onsubmit="return confirm('Delete this case study and all its screenshots? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-ui btn-ui--ghost btn-ui--sm admin-danger"><span class="btn-ui__label">Delete case study</span><x-icon name="trash" size="16" /></button>
    </form>
@endif

<script>
    // Show how many files were picked in each upload box.
    document.querySelectorAll('[data-file-count]').forEach(function (input) {
        input.addEventListener('change', function () {
            var n = input.files.length;
            input.parentElement.querySelector('[data-file-label]').textContent = n ? n + ' file' + (n > 1 ? 's' : '') + ' selected' : 'No files chosen';
        });
    });
</script>
@endsection
