<?php

use App\Support\SiteContent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A saved copy of the career timeline replaces the defaults wholesale, so it
 * needs the new niches added too, matched to each job by company.
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('site_settings')->where('key', 'portfolio.experience')->first();
        if (! $row) {
            return;
        }

        $niches = collect(SiteContent::default('portfolio.experience'))->pluck('niches', 'company');
        $jobs = array_map(function ($job) use ($niches) {
            $job['niches'] ??= $niches[$job['company'] ?? ''] ?? [];

            return $job;
        }, json_decode($row->value, true));

        DB::table('site_settings')->where('id', $row->id)->update([
            'value' => json_encode($jobs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function down(): void
    {
        // Harmless to keep.
    }
};
