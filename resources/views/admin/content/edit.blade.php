@extends('admin.layout')

@section('title', $section['label'])
@section('crumb', $section['group'])

@php
    // Build the cards: each top-level group / repeater gets its own card; loose fields share one.
    $cards = [];
    foreach ($section['parts'] as $i => $part) {
        $value = $values[$i];
        if (! empty($part['strings'])) {
            $cards[] = ['title' => $part['label'] ?? $section['label'], 'help' => $part['help'] ?? null, 'kind' => 'strings', 'part' => $i, 'value' => $value];
        } elseif (isset($part['item'])) {
            $cards[] = ['title' => $part['label'] ?? $section['label'], 'help' => $part['help'] ?? null, 'kind' => 'list', 'part' => $i, 'value' => $value,
                'field' => ['name' => 'items', 'label' => $part['label'] ?? 'Items', 'type' => 'repeater', 'fields' => $part['item'],
                    'item_label' => $part['item_label'] ?? null, 'item_sub' => $part['item_sub'] ?? null]];
        } else {
            $loose = [];
            foreach ($part['fields'] as $field) {
                if (in_array($field['type'], ['group', 'repeater'], true)) {
                    $cards[] = ['title' => $field['label'], 'help' => $field['help'] ?? null, 'kind' => 'field', 'part' => $i, 'field' => $field, 'value' => $value[$field['name']] ?? null];
                } else {
                    $loose[] = $field;
                }
            }
            if ($loose) {
                array_unshift($cards, ['title' => 'General', 'help' => null, 'kind' => 'loose', 'part' => $i, 'fields' => $loose, 'value' => $value]);
            }
        }
    }
@endphp

@section('content')
<div class="adm-page-head">
    <span class="adm-page-head__icon"><x-icon :name="$section['icon']" size="22" /></span>
    <div class="adm-page-head__text">
        <h1 class="adm-h1">{{ $section['label'] }}</h1>
        <p class="adm-muted">{{ $section['description'] }}</p>
        <p class="adm-meta">
            @if ($edited)
                <span class="adm-dot adm-dot--edited"></span> Edited {{ \Illuminate\Support\Carbon::parse($edited)->diffForHumans() }}
            @else
                <span class="adm-dot"></span> Showing the original content
            @endif
        </p>
    </div>
    <div class="adm-page-head__actions">
        @if (! empty($section['route']))
            <a href="{{ route($section['route']) }}" target="_blank" rel="noopener" class="adm-btn adm-btn--ghost">View page <x-icon name="arrow-up-right" size="15" /></a>
        @endif
    </div>
</div>

@if (count($cards) > 3)
    <nav class="adm-jump" aria-label="Sections on this page">
        @foreach ($cards as $n => $card)
            <a href="#card-{{ $n }}">{{ $card['title'] }}</a>
        @endforeach
    </nav>
@endif

<form method="POST" action="{{ route('admin.content.update', $id) }}" enctype="multipart/form-data" class="adm-form" data-adm-form id="content-form">
    @csrf
    @method('PUT')

    @foreach ($cards as $n => $card)
        @php
            $base = 'parts['.$card['part'].']';
            $fileBase = 'files['.$card['part'].']';
        @endphp
        <section class="adm-card" id="card-{{ $n }}">
            <header class="adm-card__head">
                <h2 class="adm-card__title">{{ $card['title'] }}</h2>
                @if ($card['help'])<p class="adm-help">{{ $card['help'] }}</p>@endif
            </header>
            <div class="adm-card__body">
                @switch($card['kind'])
                    @case('loose')
                        <div class="adm-fields">
                            @foreach ($card['fields'] as $field)
                                @include('admin.fields.field', ['field' => $field, 'value' => $card['value'][$field['name']] ?? null, 'name' => $base.'['.$field['name'].']', 'file' => $fileBase.'['.$field['name'].']'])
                            @endforeach
                        </div>
                        @break
                    @case('field')
                        @if ($card['field']['type'] === 'group')
                            <div class="adm-fields">
                                @foreach ($card['field']['fields'] as $sub)
                                    @include('admin.fields.field', ['field' => $sub, 'value' => $card['value'][$sub['name']] ?? null, 'name' => $base.'['.$card['field']['name'].']['.$sub['name'].']', 'file' => $fileBase.'['.$card['field']['name'].']['.$sub['name'].']'])
                                @endforeach
                            </div>
                        @else
                            @include('admin.fields.field', ['field' => array_merge($card['field'], ['help' => null]), 'value' => $card['value'], 'name' => $base.'['.$card['field']['name'].']', 'file' => $fileBase.'['.$card['field']['name'].']'])
                        @endif
                        @break
                    @case('list')
                        @include('admin.fields.field', ['field' => $card['field'], 'value' => $card['value'], 'name' => $base.'[items]', 'file' => $fileBase.'[items]'])
                        @break
                    @case('strings')
                        <div class="adm-field adm-field--wide">
                            <label class="adm-label" for="strings-{{ $n }}">Items</label>
                            <textarea class="adm-input adm-input--list" id="strings-{{ $n }}" name="{{ $base }}[text]" rows="4" data-autogrow>{{ implode("\n", (array) $card['value']) }}</textarea>
                            <p class="adm-help">One per line.</p>
                        </div>
                        @break
                @endswitch
            </div>
        </section>
    @endforeach

    <div class="adm-savebar" data-savebar>
        <p class="adm-savebar__state" data-save-state><span class="adm-dot"></span> All changes saved</p>
        <div class="adm-savebar__actions">
            <button type="submit" form="reset-form" class="adm-btn adm-btn--ghost" data-confirm="Reset {{ $section['label'] }} to the original content? Your edits in this section will be lost.">Reset to original</button>
            <button type="submit" class="adm-btn adm-btn--primary"><x-icon name="check" size="16" /> Save changes</button>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('admin.content.reset', $id) }}" id="reset-form" hidden>@csrf</form>
@endsection
