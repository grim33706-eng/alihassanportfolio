<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an optional website link to case studies, and the real September vs
 * October Google Ads comparisons. Their screenshots ship in public/assets.
 * Ids descend through this list's reverse, so the first entry shows first.
 */
return new class extends Migration
{
    private const STRATEGY = 'Managed this Google Ads account. The figures compare September 2026 with October 2026, as reported in the Google Ads overview.';

    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->string('website')->nullable()->after('brand');
        });

        foreach (array_reverse($this->studies()) as $study) {
            if (DB::table('case_studies')->where('slug', $study['slug'])->exists()) {
                continue;
            }

            $id = DB::table('case_studies')->insertGetId([
                'slug' => $study['slug'],
                'title' => $study['title'],
                'brand' => $study['brand'],
                'website' => $study['website'],
                'industry' => $study['industry'],
                'country' => $study['country'],
                'category' => 'leads',
                'platforms' => json_encode(['google']),
                'period' => 'Sep 2026 vs Oct 2026',
                'strategy' => self::STRATEGY,
                'improvements' => $study['improvements'],
                'metrics' => json_encode($study['metrics'], JSON_UNESCAPED_UNICODE),
                'is_demo' => false,
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['before' => 'September 2026', 'after' => 'October 2026'] as $stage => $caption) {
                $path = 'assets/img/results/ba-'.$study['slug'].'-'.$stage.'.jpg';
                [$width, $height] = @getimagesize(public_path($path)) ?: [null, null];

                DB::table('case_study_images')->insert([
                    'case_study_id' => $id,
                    'stage' => $stage,
                    'path' => $path,
                    'caption' => $caption,
                    'width' => $width,
                    'height' => $height,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('case_studies')->whereIn('slug', array_column($this->studies(), 'slug'))->delete();

        Schema::table('case_studies', function (Blueprint $table) {
            $table->dropColumn('website');
        });
    }

    private function studies(): array
    {
        $row = fn ($label, $before, $after, $unit = '', $direction = 'up') => compact('label', 'before', 'after', 'unit', 'direction');

        return [
            [
                'slug' => 'ann-arbor-smiles-google-ads',
                'title' => 'Conversions tripled in one month for Ann Arbor Smiles',
                'brand' => 'Ann Arbor Smiles',
                'website' => 'https://annarborsmiles.com/',
                'industry' => 'Dental',
                'country' => 'USA',
                'improvements' => 'Conversions rose from 51.67 to 164 and clicks from 269 to 906, while cost per conversion fell from $63.28 to $39.76.',
                'metrics' => [
                    $row('Conversions', '51.67', '164'),
                    $row('Clicks', '269', '906'),
                    $row('Cost per conversion', '63.28', '39.76', '$', 'down'),
                    $row('Cost', '$3.27K', '$6.52K'),
                ],
            ],
            [
                'slug' => 'childrens-dentistry-texas-google-ads',
                'title' => "Conversions doubled for Children's Dentistry of Texas",
                'brand' => "Children's Dentistry of Texas & Orthodontics",
                'website' => 'https://childrensdentistryoftexas.com/',
                'industry' => 'Dental',
                'country' => 'USA',
                'improvements' => 'Conversions rose from 35 to 74 and clicks from 177 to 293, while cost per conversion fell from $101 to $54.96.',
                'metrics' => [
                    $row('Conversions', '35', '74'),
                    $row('Clicks', '177', '293'),
                    $row('Cost per conversion', '101', '54.96', '$', 'down'),
                    $row('Cost', '$3.53K', '$4.07K'),
                ],
            ],
            [
                'slug' => 'google-ads-cost-per-conversion-halved',
                'title' => 'Cost per conversion cut by more than half',
                'brand' => null,
                'website' => null,
                'industry' => 'Private client',
                'country' => 'Undisclosed',
                'improvements' => 'Conversions rose from 23 to 57 and clicks from 165 to 214, while cost per conversion fell from $68.21 to $30.83.',
                'metrics' => [
                    $row('Conversions', '23', '57'),
                    $row('Clicks', '165', '214'),
                    $row('Cost per conversion', '68.21', '30.83', '$', 'down'),
                    $row('Cost', '$1.57K', '$1.76K'),
                ],
            ],
            [
                'slug' => 'google-ads-account-scaled',
                'title' => 'Conversions tripled as the account scaled',
                'brand' => null,
                'website' => null,
                'industry' => 'Private client',
                'country' => 'Undisclosed',
                'improvements' => 'Conversions rose from 17 to 52 and clicks from 238 to 428 as the budget grew. Cost per conversion rose from $105 to $131.',
                'metrics' => [
                    $row('Conversions', '17', '52'),
                    $row('Clicks', '238', '428'),
                    $row('Cost per conversion', '105', '131', '$', 'down'),
                    $row('Cost', '$1.78K', '$6.79K'),
                ],
            ],
        ];
    }
};
