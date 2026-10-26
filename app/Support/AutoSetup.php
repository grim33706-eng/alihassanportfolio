<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * One-time setup for hosts whose build step cannot reach the database
 * (e.g. Wasmer Edge): runs pending migrations, optionally seeds the demo
 * case studies, and links public storage — on the first request after a
 * deploy, then never again for that set of migrations.
 *
 * Enabled with AUTO_SETUP=true. A marker file named after the current
 * migrations means later requests skip it without touching the database.
 */
class AutoSetup
{
    public static function run(): void
    {
        if (! config('portfolio.auto_setup') || app()->runningInConsole()) {
            return;
        }

        $marker = storage_path('framework/setup-'.self::migrationsFingerprint());
        if (is_file($marker)) {
            return;
        }

        try {
            $hadCaseStudies = Schema::hasTable('case_studies');

            Artisan::call('migrate', ['--force' => true]);

            // Seed the demonstration case studies only when the table is brand new,
            // so entries deleted in the admin never come back on a later deploy.
            if (! $hadCaseStudies && config('portfolio.auto_seed')) {
                Artisan::call('db:seed', ['--force' => true]);
            }

            if (! file_exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (Throwable $e) {
                    report($e);
                }
            }

            @file_put_contents($marker, now()->toIso8601String());
        } catch (Throwable $e) {
            // Leave no marker so the next request tries again.
            report($e);
        }
    }

    /**
     * Changes whenever a migration is added, so new migrations run after a deploy.
     */
    private static function migrationsFingerprint(): string
    {
        $files = glob(database_path('migrations/*.php')) ?: [];

        return substr(md5(implode('|', array_map('basename', $files))), 0, 12);
    }
}
