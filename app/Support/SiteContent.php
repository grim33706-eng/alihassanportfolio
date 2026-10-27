<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Layers content edited in the admin over the defaults in config/portfolio.php
 * and config/content.php.
 *
 * Each site_settings row overrides one config key (e.g. "content.home",
 * "portfolio.projects") wholesale, so templates simply read config() and get
 * the edited value. The original defaults are kept for "Reset to default".
 */
class SiteContent
{
    /** Config roots the admin may override. */
    private const ROOTS = ['portfolio', 'content'];

    private static ?array $defaults = null;

    /**
     * Snapshot the defaults, then apply stored overrides. Called once per request.
     */
    public static function apply(): void
    {
        self::$defaults ??= Arr::only(config()->all(), self::ROOTS);

        try {
            if (! Schema::hasTable('site_settings')) {
                return;
            }
            foreach (SiteSetting::all() as $setting) {
                if (self::allowed($setting->key) && is_array($setting->value)) {
                    config([$setting->key => $setting->value]);
                }
            }
        } catch (Throwable $e) {
            // No database: the site still renders with its defaults.
            report($e);
        }
    }

    /** The default (unedited) value of a config key. */
    public static function default(string $key): mixed
    {
        self::$defaults ??= Arr::only(config()->all(), self::ROOTS);

        return data_get(self::$defaults, $key);
    }

    /** The current (possibly edited) value of a config key. */
    public static function current(string $key): mixed
    {
        return config($key);
    }

    public static function isOverridden(string $key): bool
    {
        return rescue(fn () => SiteSetting::where('key', $key)->exists(), false, false);
    }

    /** Store a new value for a config key and apply it to this request. */
    public static function save(string $key, array $value): void
    {
        abort_unless(self::allowed($key), 400);
        SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        config([$key => $value]);
    }

    /**
     * Restore defaults. With $fields, only those top-level fields are reset
     * (used when several admin sections share one key, like the profile).
     */
    public static function reset(string $key, ?array $fields = null): void
    {
        $setting = SiteSetting::where('key', $key)->first();
        if (! $setting) {
            return;
        }

        if ($fields === null) {
            $setting->delete();
            config([$key => self::default($key)]);

            return;
        }

        $value = $setting->value;
        $default = (array) self::default($key);
        foreach ($fields as $field) {
            if (array_key_exists($field, $default)) {
                $value[$field] = $default[$field];
            } else {
                unset($value[$field]);
            }
        }
        $setting->update(['value' => $value]);
        config([$key => $value]);
    }

    private static function allowed(string $key): bool
    {
        return in_array(strtok($key, '.'), self::ROOTS, true);
    }
}
