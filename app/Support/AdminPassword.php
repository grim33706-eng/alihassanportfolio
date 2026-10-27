<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Hash;

/**
 * The admin password: a hash saved from Settings takes over from ADMIN_PASSWORD in .env.
 */
class AdminPassword
{
    private const KEY = 'admin.password_hash';

    public static function configured(): bool
    {
        return filled(config('portfolio.admin_password')) || self::hash() !== null;
    }

    public static function check(string $password): bool
    {
        if ($hash = self::hash()) {
            return Hash::check($password, $hash);
        }

        $expected = (string) config('portfolio.admin_password');

        return $expected !== '' && hash_equals($expected, $password);
    }

    public static function set(string $password): void
    {
        SiteSetting::updateOrCreate(['key' => self::KEY], ['value' => ['hash' => Hash::make($password)]]);
    }

    private static function hash(): ?string
    {
        return rescue(fn () => SiteSetting::where('key', self::KEY)->value('value')['hash'] ?? null, null, false);
    }
}
