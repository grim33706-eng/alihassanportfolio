{{--
    Renders one schema field (config/admin.php) as a form control.
    $field — the field definition; $value — its current value;
    $name — the input name (e.g. parts[0][hero][title]); $file — the matching file-input name;
    $depth — nesting level (repeaters inside repeaters get their own placeholder).
--}}
@php
    $type = $field['type'];
    $id = 'f'.substr(md5($name), 0, 10);
    $wide = in_array($type, ['textarea', 'richarea', 'list', 'repeater', 'group', 'rich', 'checkboxes', 'icon', 'image'], true);
    $depth = $depth ?? 0;
@endphp

@if ($type === 'group')
    <fieldset class="adm-group">
        <legend class="adm-group__title">{{ $field['label'] }}</legend>
        @if (! empty($field['help']))<p class="adm-help">{{ $field['help'] }}</p>@endif
        <div class="adm-fields">
            @foreach ($field['fields'] as $sub)
                @include('admin.fields.field', [
                    'field' => $sub,
                    'value' => $value[$sub['name']] ?? null,
                    'name' => $name.'['.$sub['name'].']',
                    'file' => $file.'['.$sub['name'].']',
                    'depth' => $depth,
                ])
            @endforeach
        </div>
    </fieldset>

@elseif ($type === 'repeater')
    @php
        $placeholder = '__i'.$depth.'__';
        $items = array_values((array) $value);
    @endphp
    <div class="adm-field adm-field--wide">
        <div class="adm-label-row">
            <span class="adm-label">{{ $field['label'] }}</span>
            <span class="adm-count" data-repeater-count>{{ count($items) }}</span>
        </div>
        @if (! empty($field['help']))<p class="adm-help">{{ $field['help'] }}</p>@endif
        <div class="adm-repeater" data-repeater data-max="{{ $field['max'] ?? '' }}">
            <div class="adm-repeater__items" data-repeater-items>
                @foreach ($items as $i => $item)
                    @include('admin.fields.repeater-item', ['index' => $i, 'item' => (array) $item])
                @endforeach
            </div>
            <template data-repeater-template data-placeholder="{{ $placeholder }}">
                @include('admin.fields.repeater-item', ['index' => $placeholder, 'item' => [], 'isTemplate' => true])
            </template>
            <button type="button" class="adm-add" data-repeater-add>
                <x-icon name="spark" size="16" /> Add {{ \Illuminate\Support\Str::singular(strtolower($field['label'])) }}
            </button>
        </div>
    </div>

@else
    <div @class(['adm-field', 'adm-field--wide' => $wide])>
        @if ($type === 'toggle')
            <input type="hidden" name="{{ $name }}" value="0">
            <label class="adm-switch" for="{{ $id }}">
                <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($value)>
                <span class="adm-switch__track" aria-hidden="true"><span></span></span>
                <span class="adm-switch__label">{{ $field['label'] }}</span>
            </label>
        @else
            <label class="adm-label" for="{{ $id }}">{{ $field['label'] }}</label>

            @switch($type)
                @case('textarea')
                    <textarea class="adm-input" id="{{ $id }}" name="{{ $name }}" rows="3" data-autogrow>{{ $value }}</textarea>
                    @break

                @case('richarea')
                    <textarea class="adm-input" id="{{ $id }}" name="{{ $name }}" rows="2" data-autogrow data-rich>{{ $value }}</textarea>
                    <p class="adm-preview" data-rich-preview aria-hidden="true"></p>
                    @break

                @case('rich')
                    <input class="adm-input" type="text" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" data-rich>
                    <p class="adm-preview" data-rich-preview aria-hidden="true"></p>
                    @break

                @case('number')
                    <input class="adm-input" type="number" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" step="{{ $field['step'] ?? '1' }}">
                    @break

                @case('list')
                    <textarea class="adm-input adm-input--list" id="{{ $id }}" name="{{ $name }}" rows="{{ max(3, count((array) $value) + 1) }}" data-autogrow>{{ implode("\n", (array) $value) }}</textarea>
                    @break

                @case('select')
                    <select class="adm-input" id="{{ $id }}" name="{{ $name }}">
                        @foreach (\App\Support\ContentEditor::options($field['options']) as $optValue => $optLabel)
                            <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
                        @endforeach
                    </select>
                    @break

                @case('checkboxes')
                    @php
                        // Ticked options first, in their saved order, so saving keeps that order.
                        $opts = \App\Support\ContentEditor::options($field['options']);
                        $chosen = array_values(array_filter((array) $value, fn ($v) => array_key_exists($v, $opts)));
                        $ordered = array_merge(array_flip($chosen), $opts);
                        $ordered = array_intersect_key(array_replace($ordered, $opts), $ordered);
                    @endphp
                    {{-- Always submitted, so "nothing ticked" is distinguishable from "not on the form". --}}
                    <input type="hidden" name="{{ $name }}[]" value="">
                    <div class="adm-chips" id="{{ $id }}">
                        @foreach ($ordered as $optValue => $optLabel)
                            <label class="adm-chip">
                                <input type="checkbox" name="{{ $name }}[]" value="{{ $optValue }}" @checked(in_array($optValue, (array) $value, true))>
                                <span><x-icon :name="$optValue" size="14" /> {{ $optLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('icon')
                    <div class="adm-icons" id="{{ $id }}" role="radiogroup" aria-label="{{ $field['label'] }}">
                        @foreach (config('admin.icons') as $icon)
                            <label class="adm-icon-opt" title="{{ $icon }}">
                                <input type="radio" name="{{ $name }}" value="{{ $icon }}" @checked($value === $icon)>
                                <span><x-icon :name="$icon" size="18" /></span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('image')
                    <div @class(['adm-image', 'has-image' => $value]) data-image-field data-has-saved="{{ $value ? '1' : '0' }}">
                        <div class="adm-image__preview" data-image-preview>
                            @if ($value)
                                <img src="{{ media_url($value) }}" alt="" data-saved-image>
                            @endif
                            <span class="adm-image__empty" data-image-empty><x-icon name="image" size="26" /> No image</span>
                            <span class="adm-image__removed" data-image-removed><x-icon name="trash" size="22" /> Will be removed when you save</span>
                        </div>
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        {{-- 1 = remove the saved image on save. Set by the Remove button. --}}
                        <input type="hidden" name="{{ preg_replace('/\]$/', '__remove]', $name) }}" value="0" data-image-remove-input>
                        <div class="adm-image__actions">
                            <label class="adm-btn adm-btn--ghost adm-btn--sm adm-file">
                                <input type="file" name="{{ $file }}" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" data-image-input>
                                <x-icon name="image" size="15" /> <span data-image-pick-label>{{ $value ? 'Replace image' : 'Upload image' }}</span>
                            </label>
                            <button type="button" class="adm-btn adm-btn--danger adm-btn--sm" data-image-remove @unless ($value) hidden @endunless>
                                <x-icon name="trash" size="15" /> <span data-image-remove-label>Remove image</span>
                            </button>
                        </div>
                        <span class="adm-help" data-image-name>JPG, PNG, WebP, GIF or SVG · up to 4 MB</span>
                    </div>
                    @break

                @default
                    <input class="adm-input" type="text" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}">
            @endswitch
        @endif

        @if ($type === 'rich' || $type === 'richarea')
            <p class="adm-help">Wrap words in <code>*asterisks*</code> to highlight them.@if (! empty($field['help'])) {{ $field['help'] }}@endif</p>
        @elseif ($type === 'list')
            <p class="adm-help">{{ $field['help'] ?? 'One per line.' }}</p>
        @elseif (! empty($field['help']) && $type !== 'image')
            <p class="adm-help">{{ $field['help'] }}</p>
        @elseif (! empty($field['help']))
            <p class="adm-help">{{ $field['help'] }}</p>
        @endif
    </div>
@endif
