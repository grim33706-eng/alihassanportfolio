<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Turns the admin schema (config/admin.php) into form values and back:
 * reads the current content for a section's parts, and cleans, validates
 * and stores what the admin form submits.
 */
class ContentEditor
{
    public const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    public const IMAGE_MAX_KB = 4096;

    /* ------------------------------------------------------------------
       Reading
    ------------------------------------------------------------------ */

    /** Current value of a part, shaped for the form. */
    public static function formValue(array $part): mixed
    {
        $value = SiteContent::current($part['key']);

        if (isset($part['map'])) {
            // { "Group": [...] }  →  [ {group: "Group", items: [...]} ]
            return collect((array) $value)
                ->map(fn ($items, $name) => [$part['map']['key'] => $name, $part['map']['value'] => $items])
                ->values()
                ->all();
        }

        return $value;
    }

    /** Options for select / checkbox fields. */
    public static function options(string|array $source): array
    {
        if (is_array($source)) {
            return $source;
        }

        return match ($source) {
            'categories' => config('portfolio.categories'),
            'platforms' => config('portfolio.platform_labels'),
            default => [],
        };
    }

    /* ------------------------------------------------------------------
       Saving
    ------------------------------------------------------------------ */

    /** Save every part of a section from the request input. */
    public static function saveParts(array $parts, array $input, array $files): void
    {
        $before = self::mediaIn($parts);

        foreach ($parts as $i => $part) {
            $data = $input[$i] ?? [];
            $upload = $files[$i] ?? [];

            if (! empty($part['strings'])) {
                SiteContent::save($part['key'], self::lines($data['text'] ?? ''));
            } elseif (isset($part['item'])) {
                $items = self::items($part['item'], $data['items'] ?? [], $upload['items'] ?? []);
                if (isset($part['map'])) {
                    $items = collect($items)
                        ->filter(fn ($row) => filled($row[$part['map']['key']] ?? null))
                        ->mapWithKeys(fn ($row) => [$row[$part['map']['key']] => $row[$part['map']['value']] ?? []])
                        ->all();
                }
                SiteContent::save($part['key'], $items);
            } else {
                $current = (array) SiteContent::current($part['key']);
                $clean = self::fields($part['fields'], $data, $upload, $current, partial: true);
                SiteContent::save($part['key'], array_replace($current, $clean));
            }
        }

        self::pruneMedia($before, $parts);
    }

    /** Reset every part of a section to its defaults. */
    public static function resetParts(array $parts): void
    {
        $before = self::mediaIn($parts);

        foreach ($parts as $part) {
            if (isset($part['fields'])) {
                SiteContent::reset($part['key'], array_column($part['fields'], 'name'));
            } else {
                SiteContent::reset($part['key']);
            }
        }

        self::pruneMedia($before, $parts);
    }

    /** Uploaded files (under media/) that the parts' current content refers to. */
    private static function mediaIn(array $parts): array
    {
        return collect($parts)
            ->flatMap(fn ($part) => Arr::flatten((array) SiteContent::current($part['key'])))
            ->filter(fn ($value) => is_string($value) && str_starts_with($value, 'media/'))
            ->unique()
            ->all();
    }

    /** Delete uploads that are no longer used, e.g. after a list item was removed or a reset. */
    private static function pruneMedia(array $before, array $parts): void
    {
        foreach (array_diff($before, self::mediaIn($parts)) as $path) {
            self::deleteMedia($path);
        }
    }

    /**
     * Clean an object's fields. $current holds the existing values. With $partial, a field
     * that was not submitted at all keeps its current value instead of being emptied.
     */
    private static function fields(array $fields, array $data, array $files, array $current = [], bool $partial = false): array
    {
        $out = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $submitted = array_key_exists($name, $data) || isset($files[$name]) || array_key_exists($name.'__remove', $data);
            if ($partial && ! $submitted) {
                if (array_key_exists($name, $current)) {
                    $out[$name] = $current[$name];
                }
                continue;
            }
            $out[$name] = $field['type'] === 'group'
                ? array_replace((array) ($current[$name] ?? []), self::fields($field['fields'], (array) ($data[$name] ?? []), (array) ($files[$name] ?? []), (array) ($current[$name] ?? []), $partial))
                : self::value($field, $data[$name] ?? null, $files[$name] ?? null, $data, $current[$name] ?? null);
        }

        // Fill empty anchors from their source field.
        foreach ($fields as $field) {
            if (! empty($field['slug_from']) && blank($out[$field['name']])) {
                $out[$field['name']] = Str::slug((string) ($out[$field['slug_from']] ?? ''));
            }
        }

        return $out;
    }

    /** Clean a list of objects, keeping the submitted order and dropping empty rows. */
    private static function items(array $fields, mixed $rows, mixed $files): array
    {
        $out = [];
        foreach ((array) $rows as $key => $row) {
            if (! is_array($row)) {
                continue;
            }
            $clean = self::fields($fields, $row, (array) ($files[$key] ?? []), $row);
            if (self::isEmpty($clean)) {
                continue;
            }
            $out[] = $clean;
        }

        return $out;
    }

    private static function value(array $field, mixed $value, mixed $file, array $siblings, mixed $current): mixed
    {
        return match ($field['type']) {
            'number' => self::number($value),
            'toggle' => (bool) $value,
            // Keep the submitted order (the form lists ticked options first, in their saved order).
            'checkboxes' => array_values(array_unique(array_filter((array) $value, fn ($v) => array_key_exists($v, self::options($field['options']))))),
            'select' => array_key_exists((string) $value, self::options($field['options']))
                ? (string) $value
                : (string) array_key_first(self::options($field['options'])),
            'list' => self::lines($value),
            'textarea', 'richarea' => self::text($value, multiline: true),
            'group' => self::fields($field['fields'], (array) $value, (array) $file, (array) $current),
            'repeater' => array_slice(self::items($field['fields'], $value, $file), 0, $field['max'] ?? PHP_INT_MAX),
            'image' => self::image($field, $value, $file, ! empty($siblings[$field['name'].'__remove'])),
            default => self::text($value),
        };
    }

    private static function text(mixed $value, bool $multiline = false): string
    {
        $text = trim(str_replace("\r\n", "\n", (string) $value));
        if (! $multiline) {
            $text = preg_replace('/\s*\n\s*/', ' ', $text);
        }
        if (mb_strlen($text) > 5000) {
            throw ValidationException::withMessages(['content' => 'One of the text fields is longer than 5,000 characters.']);
        }

        return $text;
    }

    private static function lines(mixed $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $value)), 'strlen'));
    }

    private static function number(mixed $value): int|float
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return str_contains((string) $value, '.') ? (float) $value : (int) $value;
    }

    private static function image(array $field, mixed $currentPath, mixed $file, bool $remove): ?string
    {
        $currentPath = is_string($currentPath) && $currentPath !== '' ? $currentPath : null;

        if ($file instanceof UploadedFile) {
            // `mimes` checks the file's actual content, not just its name.
            $invalid = Validator::make(['file' => $file], ['file' => ['file', 'mimes:'.implode(',', self::IMAGE_TYPES), 'max:'.self::IMAGE_MAX_KB]])->fails();
            if ($invalid) {
                throw ValidationException::withMessages([
                    'image' => '"'.$field['label'].'" must be a JPG, PNG, WebP, GIF or SVG image of up to 4 MB.',
                ]);
            }
            self::deleteMedia($currentPath);

            return $file->store('media', 'public');
        }

        if ($remove) {
            self::deleteMedia($currentPath);

            return null;
        }

        return $currentPath;
    }

    /** Only files the admin uploaded (under media/) are ever deleted. */
    private static function deleteMedia(?string $path): void
    {
        if ($path && str_starts_with($path, 'media/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private static function isEmpty(array $row): bool
    {
        foreach (Arr::flatten($row) as $value) {
            if (is_string($value) && $value !== '') {
                return false;
            }
            if (is_numeric($value) && $value != 0) {
                return false;
            }
        }

        return true;
    }
}
