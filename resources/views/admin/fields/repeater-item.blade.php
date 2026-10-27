{{--
    One item of a repeater. Expects $field (the repeater), $index, $item, $name, $file, $depth.
    $isTemplate marks the blank copy used by "Add".
--}}
@php
    $labelKey = $field['item_label'] ?? ($field['fields'][0]['name'] ?? null);
    $subKey = $field['item_sub'] ?? null;
    $title = $labelKey ? ($item[$labelKey] ?? '') : '';
    $isTemplate = $isTemplate ?? false;
@endphp
<div class="adm-item" data-repeater-item @if (! $isTemplate && count((array) ($field['fields'] ?? [])) > 2) data-collapsed @endif>
    <div class="adm-item__head">
        <span class="adm-item__num" data-item-num aria-hidden="true"></span>
        <button type="button" class="adm-item__title" data-item-toggle aria-expanded="{{ $isTemplate ? 'true' : 'false' }}">
            <span data-item-title data-source="{{ $labelKey }}">{{ is_array($title) ? '' : ($title !== '' ? $title : 'New item') }}</span>
            @if ($subKey && ! empty($item[$subKey]) && is_string($item[$subKey]))
                <small>{{ $item[$subKey] }}</small>
            @endif
        </button>
        <div class="adm-item__tools">
            <button type="button" class="adm-icon-btn" data-item-up aria-label="Move up"><x-icon name="arrow-right" size="15" class="adm-rot-up" /></button>
            <button type="button" class="adm-icon-btn" data-item-down aria-label="Move down"><x-icon name="arrow-right" size="15" class="adm-rot-down" /></button>
            <button type="button" class="adm-icon-btn adm-icon-btn--danger" data-item-remove aria-label="Remove"><x-icon name="trash" size="15" /></button>
            <button type="button" class="adm-icon-btn" data-item-toggle aria-label="Expand or collapse"><x-icon name="chevron-right" size="15" class="adm-chevron" /></button>
        </div>
    </div>
    <div class="adm-item__body">
        <div class="adm-fields">
            @foreach ($field['fields'] as $sub)
                @include('admin.fields.field', [
                    'field' => $sub,
                    'value' => $item[$sub['name']] ?? null,
                    'name' => $name.'['.$index.']['.$sub['name'].']',
                    'file' => $file.'['.$index.']['.$sub['name'].']',
                    'depth' => $depth + 1,
                ])
            @endforeach
        </div>
    </div>
</div>
