<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

/**
 * Demonstration case studies so the Before & After layout can be seen before
 * real screenshots exist. They describe documented work only: no screenshots,
 * no metrics, and every one is flagged is_demo (shown as "Demonstration").
 * Replace or edit them from /admin/case-studies once real data is available.
 */
class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $demos = [
            [
                'slug' => 'demo-ecommerce-portfolio',
                'title' => 'Scaling a multi-brand e-commerce portfolio',
                'industry' => 'Fashion & home textiles',
                'country' => 'Australia, Canada, UK & USA',
                'category' => 'ecommerce',
                'platforms' => ['meta', 'google', 'tiktok'],
                'period' => 'Dec 2024 – Jun 2026',
                'strategy' => 'Rebuilt campaigns around conversion objectives, set up GA4 and GTM conversion tracking, and ran structured audience and creative A/B tests before scaling budget into the winners.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'demo-uk-france-leads',
                'title' => 'Lead generation for UK & French service businesses',
                'industry' => 'Service businesses',
                'country' => 'United Kingdom & France',
                'category' => 'leads',
                'platforms' => ['meta', 'tiktok'],
                'period' => 'Mar 2026 – Aug 2026',
                'strategy' => 'Tailored audience targeting and creative tests on conversion-focused lead campaigns, with daily budgets reallocated across client accounts to bring cost per lead down.',
                'sort_order' => 2,
            ],
            [
                'slug' => 'demo-bedding-google-ads',
                'title' => 'Google Ads for an international bedding brand',
                'industry' => 'Home textiles / bedding',
                'country' => 'International',
                'category' => 'ecommerce',
                'platforms' => ['google'],
                'period' => 'Dec 2024 – Jun 2026',
                'strategy' => 'Restructured Search campaigns with fresh keyword research and ad copy, added Performance Max and remarketing, and worked on Quality Score and click-through rate.',
                'sort_order' => 3,
            ],
        ];

        foreach ($demos as $demo) {
            CaseStudy::updateOrCreate(
                ['slug' => $demo['slug']],
                $demo + ['is_demo' => true, 'is_featured' => true, 'is_published' => true, 'metrics' => null],
            );
        }
    }
}
