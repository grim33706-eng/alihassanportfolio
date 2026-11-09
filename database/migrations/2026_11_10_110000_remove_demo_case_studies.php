<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Real case studies have replaced the three demonstration cards.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('case_studies')
            ->where('is_demo', true)
            ->whereIn('slug', ['demo-ecommerce-portfolio', 'demo-uk-france-leads', 'demo-bedding-google-ads'])
            ->delete();
    }

    public function down(): void
    {
        // The demonstration cards are not restored.
    }
};
