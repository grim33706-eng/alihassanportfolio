<?php

use App\Support\SiteContent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A saved profile replaces the defaults wholesale, so it needs the new
 * portrait too. A photo already uploaded in the admin is left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('site_settings')->where('key', 'portfolio.person')->first();
        if (! $row) {
            return;
        }

        $person = json_decode($row->value, true);
        if (empty($person['photo'])) {
            $person['photo'] = SiteContent::default('portfolio.person.photo');
            DB::table('site_settings')->where('id', $row->id)->update([
                'value' => json_encode($person, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        }
    }

    public function down(): void
    {
        // Harmless to keep.
    }
};
