<?php

/*
|--------------------------------------------------------------------------
| Page content (defaults)
|--------------------------------------------------------------------------
|
| Headings, leads, labels and short lists for every page. Everything here is
| editable in the admin (/admin); edits are stored in the site_settings table
| and layered over these defaults, so "Reset to default" always returns here.
|
| Formatting in text fields:
|   *words*        → shown in the gradient highlight
|   a line break   → kept as a line break where the design allows
|   {name} {first_name} {role} {location} → replaced with the profile values
|
*/

return [

    // Shared across pages
    'site' => [
        'monogram' => 'AH',
        'brand_tagline' => 'Performance Marketing',
        // Search engines. Keywords also feed the meta keywords tag; alternate names are
        // other names people search for the site by (used in the WebSite schema).
        'seo' => [
            'keywords' => [
                'Ali Hassan performance marketer',
                'Ali Hassan Meta Ads expert',
                'Ali Hassan Google Ads expert',
                'Ali Hassan ad expert',
                'Ali Meta Ads expert',
                'Ali Google Ads expert',
                'Ali ad expert',
                'Hassan Ads expert',
                'performance marketer Lahore',
                'Meta Ads expert Pakistan',
                'Google Ads expert Pakistan',
                'PPC specialist Lahore',
                'Facebook and Instagram Ads expert',
                'TikTok Ads specialist',
                'paid media manager',
            ],
            'alternate_names' => ['Hassan Ads Expert', 'Ali Hassan Ad Expert'],
            'google_verification' => '',
            'bing_verification' => '',
        ],
        'nav_cta' => "Let's talk",
        'cta' => [
            'eyebrow' => 'Get in touch',
            'title' => "Ready to scale what's working?",
            'text' => "Whether you're hiring for a performance marketing role or need someone to run your Meta and Google Ads, let's talk about your goals.",
            'primary' => 'Contact me',
            'secondary' => 'View LinkedIn',
        ],
        'footer' => [
            'eyebrow' => 'Open to new projects & roles',
            'headline' => "Let's make your ad spend\nwork harder.",
            'button' => 'Start a conversation',
            'note' => '{name} is a performance marketer and Meta Ads & Google Ads expert in {location}, running Meta, Google and TikTok Ads for e-commerce sales and B2B/B2C lead generation.',
            'disclaimer' => 'Figures come from documented work history and reflect past performance, not guaranteed results.',
        ],
        'case_studies' => [
            'eyebrow' => 'Before & after',
            'title' => 'The difference, side by side',
            'all_button' => 'All case studies',
            'did_label' => 'What {first_name} did',
            'improved_label' => 'What improved',
            'demo_notice' => 'Cards marked “Demonstration” describe real projects but use placeholder graphics in place of screenshots. No figures are shown until genuine campaign data is added.',
        ],
    ],

    'home' => [
        'seo_title' => '{name} — Performance Marketer & Meta, Google Ads Expert',
        'seo_description' => '{name} — performance marketer and Meta & Google Ads expert in Lahore, Pakistan, with 4+ years of PPC, TikTok Ads, e-commerce and lead-gen campaigns.',
        'hero' => [
            'title' => "Paid media that turns ad spend into *revenue* and qualified\u{00A0}leads.",
            'primary' => 'View my work',
            'secondary' => 'Contact me',
            'chips' => [
                ['value' => '~4x', 'label' => "Average ROAS\ne-commerce portfolio"],
                ['value' => '8–10x', 'label' => "Top-performing\ncampaigns"],
            ],
            'platforms_chip' => 'Meta · Google · TikTok',
            'markets_label' => 'Campaigns run for',
            'markets' => ['USA', 'Canada', 'UK', 'France', 'Australia', 'UAE', 'Saudi Arabia', 'Oman', 'Azerbaijan', 'Uzbekistan', 'Bangladesh', 'Pakistan'],
        ],
        'platforms' => [
            'eyebrow' => 'Core platforms',
            'title' => 'Two platforms. One goal: *profitable growth.*',
            'lead' => 'Meta Ads builds demand and drives sales; Google Ads captures the intent that already exists. Here is what I run on each.',
            'also_label' => 'Also running:',
        ],
        'intro' => [
            'eyebrow' => 'Introduction',
            'title' => "Hi, I'm {first_name}. I run ads that have to *pay for themselves.*",
            'facts' => [
                ['icon' => 'users', 'text' => 'Led a 7-person PPC team'],
                ['icon' => 'layers', 'text' => 'Up to 16 brands in one portfolio'],
                ['icon' => 'globe', 'text' => '12 markets across 4 continents'],
            ],
            'button' => 'More about me',
        ],
        'services' => [
            'eyebrow' => 'Services',
            'title' => 'What I can take off your plate',
            'button' => 'All services',
        ],
        'skills' => [
            'eyebrow' => 'Expertise',
            'title' => 'The toolkit behind the results',
            'marquee' => ['Meta Ads', 'Google Search', 'Performance Max', 'Remarketing', 'TikTok Ads', 'GA4', 'Google Tag Manager', 'Meta Pixel', 'A/B Testing', 'Lead Generation', 'E-commerce', 'Bidding Strategy'],
        ],
        'work' => [
            'eyebrow' => 'Selected work',
            'title' => 'Campaigns across categories and continents',
            'lead' => 'Client names are kept confidential. Projects are described by industry, market and platform.',
        ],
        'explore' => [
            'eyebrow' => 'Full portfolio',
            'title' => 'Explore every campaign',
            'text' => 'E-commerce, lead generation and real estate work, filterable by platform, category and market.',
            'button' => 'View all projects',
        ],
        'case_studies' => [
            'title' => 'Before {first_name}. *After {first_name}.*',
            'lead' => 'Selected case studies comparing campaign performance before and after {first_name} took over.',
        ],
        'international' => [
            'eyebrow' => 'International experience',
            'title' => 'Campaigns built for *twelve markets*',
            'lead' => 'From e-commerce sales in the UK, USA, Canada and Australia and service-business leads in France, to real estate leads in Dubai, Azerbaijan and Uzbekistan and Meta campaigns across the Gulf and South Asia.',
            'button' => 'Explore the markets',
        ],
        'achievements' => [
            'eyebrow' => 'Achievements',
            'title' => 'Documented milestones',
            'items' => [
                ['headline' => '~4x average ROAS', 'detail' => 'Across an e-commerce portfolio of up to 10 brands, with top campaigns reaching 8–10x — The Social Links.'],
                ['headline' => '2–3 → 10 brands', 'detail' => 'Scaled the managed portfolio while handling approximately PKR 60,000–80,000 in daily Meta ad spend.'],
                ['headline' => 'Team of 10+ led', 'detail' => 'Paid media specialists, designers and video creators across 5 e-commerce and 11 lead-generation brands — NextQuick Solutions.'],
                ['headline' => '7-person PPC team', 'detail' => 'Leading PPC and Google Ads specialists on lead generation for USA and Canada clients — Dynasty Marketing Agency.'],
                ['headline' => '15 accounts at once', 'detail' => 'Managed local lead-generation clients end to end — Orbit Creator.'],
            ],
            'button' => 'See results in context',
        ],
        'education' => [
            'eyebrow' => 'Qualifications',
            'title' => 'Education',
        ],
        'cta' => ['title' => '', 'text' => ''],
    ],

    'about' => [
        'seo_title' => 'About {name}, Performance Marketer & Ad Expert',
        'seo_description' => 'Meet {name}, a performance marketer and ad expert with 4+ years managing Meta, Google and TikTok Ads and leading PPC teams for e-commerce and lead-gen brands.',
        'hero' => [
            'eyebrow' => 'About me',
            'title' => 'Four years of turning *ad budgets* into measurable growth.',
            'lead' => 'From managing 15 local lead-generation accounts to leading PPC and marketing teams for brands across North America, Europe, the Gulf and Asia-Pacific.',
            'primary' => 'Work with me',
            'secondary' => 'LinkedIn',
        ],
        'background' => [
            'eyebrow' => 'Background',
            'title' => 'A performance marketer who leads with data — and with people.',
            'facts' => [
                ['icon' => 'briefcase', 'label' => 'Current focus', 'value' => 'PPC Team Lead at Dynasty Marketing Agency, Performance Marketing Manager at Zela Boost, plus paid and social media at SuperWise International and Chapter001'],
                ['icon' => 'users', 'label' => 'Leadership', 'value' => 'Seven-person PPC team; cross-functional marketing teams of 10+'],
                ['icon' => 'globe', 'label' => 'Markets', 'value' => 'USA, Canada, UK, France, Australia, UAE, Saudi Arabia, Oman, Azerbaijan, Uzbekistan, Bangladesh and Pakistan'],
                ['icon' => 'map-pin', 'label' => 'Based in', 'value' => '{location}'],
            ],
        ],
        'specialisation' => [
            'eyebrow' => 'Specialisation',
            'title' => 'Meta Ads and Google Ads, run as one system',
            'cards' => [
                ['icon' => 'meta', 'title' => 'Meta Ads', 'text' => 'E-commerce sales for fashion, home textiles, jewellery and food brands, plus B2B and B2C lead generation — with audience segmentation, remarketing and constant creative testing.'],
                ['icon' => 'google', 'title' => 'Google Ads', 'text' => 'Search, Display, Performance Max and remarketing — backed by keyword research, bidding strategy, ad copywriting and Quality Score and CTR improvements.'],
            ],
        ],
        'approach' => [
            'eyebrow' => 'Approach',
            'title' => 'How I run a campaign',
            'lead' => "The same loop, whether it's one account or a sixteen-brand portfolio.",
        ],
        'experience' => ['eyebrow' => 'Experience', 'title' => 'Career timeline'],
        'skills' => ['eyebrow' => 'Skills', 'title' => 'Professional skills'],
        'education' => ['eyebrow' => 'Education', 'title' => 'Qualifications'],
        'cta' => ['title' => "Let's talk about your next campaign.", 'text' => ''],
    ],

    'services' => [
        'seo_title' => 'Meta Ads & Google Ads Management Services',
        'seo_description' => 'Hire {name}, a Meta Ads and Google Ads expert, for campaign strategy, PPC management, GTM & GA4 conversion tracking, creative testing and reporting.',
        'hero' => [
            'eyebrow' => 'Services',
            'title' => 'Paid advertising, *managed end to end.*',
            'lead' => 'Strategy, setup, tracking, creative testing and optimisation for e-commerce sales and B2B/B2C lead generation.',
            'button' => 'Explore services',
        ],
        'platforms' => [
            'eyebrow' => 'By platform',
            'title' => "What's included on each platform",
            'also_label' => 'Also available:',
        ],
        'process' => ['eyebrow' => 'Working together', 'title' => 'From tracking setup to scale'],
        'industries' => [
            'eyebrow' => 'Industries',
            'title' => 'Experience across sectors',
            'items' => [
                ['icon' => 'cart', 'title' => 'E-commerce', 'text' => 'Fashion, home textiles, bedding, jewellery and food brands.'],
                ['icon' => 'map-pin', 'title' => 'Real estate', 'text' => 'Paid lead campaigns in Dubai, Azerbaijan and Uzbekistan, plus real estate accounts in a multi-brand portfolio.'],
                ['icon' => 'display', 'title' => 'Software', 'text' => 'Lead-generation campaigns for software businesses.'],
                ['icon' => 'briefcase', 'title' => 'Service businesses', 'text' => 'B2B and B2C leads for service businesses and local companies.'],
            ],
        ],
        'faq' => [
            'eyebrow' => 'FAQ',
            'title' => 'Questions about *working with {first_name}*',
            'items' => [
                ['q' => 'Who is {name}?', 'a' => '{name} is a performance marketer based in {location} with 4+ years in paid advertising. He runs Meta Ads, Google Ads and TikTok Ads for e-commerce sales and B2B/B2C lead generation, and has led a seven-person PPC team.'],
                ['q' => 'Is {name} a Meta Ads expert?', 'a' => 'Yes. Meta Ads (Facebook and Instagram) are his main platform: account structure, prospecting and remarketing, creative testing with designers and video editors, and Meta Pixel setup. Across an e-commerce portfolio his campaigns averaged about 4x ROAS, with top campaigns at 8–10x.'],
                ['q' => 'Does {first_name} manage Google Ads too?', 'a' => 'Yes — Search, Display, Performance Max and remarketing campaigns, with keyword research, bidding strategy, Quality Score and CTR improvement, and conversion tracking through Google Tag Manager and GA4.'],
                ['q' => 'Which countries has {first_name} run ad campaigns for?', 'a' => 'Twelve markets: the UK, France, the USA, Canada, Australia, the UAE, Saudi Arabia, Oman, Bangladesh, Azerbaijan, Uzbekistan and Pakistan.'],
                ['q' => 'What kinds of businesses does {first_name} work with?', 'a' => 'E-commerce brands (fashion, home textiles, bedding, jewellery and food), real estate, software and service businesses — for both online sales and lead generation.'],
                ['q' => 'How can I hire {name}?', 'a' => 'Send a message through the contact page or connect on LinkedIn. He is open to freelance ad management projects and full-time performance marketing roles.'],
            ],
        ],
        'cta' => ['title' => 'Need a hand with your ad accounts?', 'text' => ''],
    ],

    'portfolio' => [
        'seo_title' => 'Meta, Google & TikTok Ads Portfolio',
        'seo_description' => 'Paid ads portfolio of {name}, Meta & Google Ads expert: e-commerce, lead generation and real estate campaigns in the UK, USA, Canada, Australia and UAE.',
        'hero' => [
            'eyebrow' => 'Portfolio',
            'title' => 'Selected *advertising projects*',
            'lead' => 'Case studies from my work history, described by industry, market and platform. Client names are kept confidential and graphics are illustrative.',
        ],
        'filters' => [
            'title' => 'Filter projects',
            'subtitle' => 'By platform, category or market',
            'showing' => 'Showing',
            'all_projects' => 'All projects',
            'reset' => 'Reset filters',
            'empty' => 'No projects match those filters.',
        ],
        'case_studies' => [
            'title' => 'Before & after case studies',
            'lead' => 'Drag the slider to compare a brand’s results before {first_name} started with what changed after.',
        ],
        'cta' => ['title' => 'Have a campaign like these in mind?', 'text' => ''],
    ],

    'results' => [
        'seo_title' => 'Ad Results: ~4x ROAS, 8–10x Peak Campaigns',
        'seo_description' => 'Documented results from ad expert {name}: about 4x average ROAS across an e-commerce portfolio, top campaigns at 8–10x, and portfolios of up to 16 brands.',
        'hero' => [
            'eyebrow' => 'Results',
            'title' => 'Results, with the *context* they were earned in.',
            'lead' => "Every figure below comes from documented work history, alongside where and when it was achieved. Past performance doesn't guarantee future results.",
            'pin_average' => '~4x avg',
            'pin_peak' => '8–10x peak',
        ],
        'case_studies' => [
            'title' => 'See the difference: *before & after*',
            'lead' => 'Each case study compares a brand’s advertising performance before {first_name}’s involvement with the results after it. Drag the slider, switch to side-by-side, or open any screenshot full size.',
        ],
        'roas' => [
            'eyebrow' => 'Return on ad spend',
            'title' => 'Around 4x on average. Up to 10x at the top.',
            'lead' => 'Measured across the e-commerce portfolio at The Social Links (Dec 2024 – Jun 2026) — fashion, home textiles, jewellery and food brands selling into Australia, Canada, the UK and the USA.',
            'chart_title' => 'ROAS, The Social Links e-commerce portfolio',
            'average' => 4,
            'average_label' => '~4x',
            'average_caption' => 'average',
            'average_tip' => 'Portfolio average: approximately 4x ROAS',
            'peak_from' => 8,
            'peak_to' => 10,
            'peak_label' => '8–10x',
            'peak_caption' => 'top campaigns',
            'peak_tip' => 'Top-performing campaigns: 8–10x ROAS',
            'breakeven_caption' => 'break-even',
            'table_caption' => 'Return on ad spend, The Social Links e-commerce portfolio, Dec 2024 – Jun 2026',
            'table_average' => 'Approximately 4x',
            'note' => 'ROAS = revenue attributed to ads ÷ ad spend. A 4x ROAS means roughly four units of revenue for every unit spent.',
        ],
        'scale' => [
            'eyebrow' => 'Scale',
            'title' => 'Trusted with bigger portfolios, year after year',
            'chart_title' => 'Portfolio size by role',
            'chart_subtitle' => 'Brands or client accounts managed at the same time',
            'bars' => [
                ['role' => 'NextQuick Solutions', 'detail' => '5 e-commerce + 11 lead-gen brands', 'value' => 16, 'unit' => 'brands'],
                ['role' => 'Orbit Creator', 'detail' => 'Local client accounts at once', 'value' => 15, 'unit' => 'accounts'],
                ['role' => 'The Social Links', 'detail' => 'Brands managed simultaneously', 'value' => 10, 'unit' => 'brands'],
                ['role' => 'Zela Boost', 'detail' => '3 e-commerce + 5 lead-gen brands', 'value' => 8, 'unit' => 'brands'],
            ],
            'growth_label' => 'Portfolio growth · The Social Links',
            'growth_from' => '2–3',
            'growth_from_unit' => 'accounts',
            'growth_to' => '10',
            'growth_to_unit' => 'brands at once',
            'spend_label' => 'Daily Meta ad spend managed',
            'spend_value' => 'PKR 60,000–80,000',
            'spend_note' => 'Approximate, per day, across the portfolio',
            'teams_label' => 'Teams led',
            'teams_a' => '7',
            'teams_a_unit' => 'PPC specialists',
            'teams_b' => '10+',
            'teams_b_unit' => 'marketing team',
        ],
        'drivers' => [
            'eyebrow' => 'What drives the numbers',
            'title' => 'The optimisation work behind the results',
            'items' => [
                ['icon' => 'pulse', 'title' => 'Reliable tracking', 'text' => 'Meta Pixel, GTM and GA4 conversion tracking set up so results are measured on real conversion events.'],
                ['icon' => 'layers', 'title' => 'Creative testing', 'text' => 'A/B tests on hooks, angles and formats; weaker assets revised with designers and video editors.'],
                ['icon' => 'target', 'title' => 'Audience & keyword refinement', 'text' => 'Segments, keywords and bids adjusted from campaign and lead-quality data.'],
                ['icon' => 'gauge', 'title' => 'Search efficiency', 'text' => 'Quality Score and click-through-rate improvements on Google Search campaigns.'],
                ['icon' => 'chart', 'title' => 'Budget allocation', 'text' => 'Budgets moved between campaigns, audiences and brands towards what performs.'],
                ['icon' => 'funnel', 'title' => 'Lead-quality review', 'text' => 'Lead quality, conversion volume and acquisition costs reviewed — not just lead counts.'],
            ],
        ],
        'markets' => [
            'eyebrow' => 'By market',
            'title' => 'Where the work was done',
            'fine_print' => 'Cost per lead, cost per acquisition and conversion volume are monitored across accounts. Client-level figures are not published on this site.',
        ],
        'cta' => ['title' => "Let's set targets for your account.", 'text' => ''],
    ],

    'international' => [
        'seo_title' => 'International PPC & Paid Social Campaigns',
        'seo_description' => 'Performance marketer {name} has run Meta, Google and TikTok Ads for 12 markets, including the UK, France, USA, Canada, Australia, UAE, Saudi Arabia and Pakistan.',
        'hero' => [
            'eyebrow' => 'International experience',
            'title' => 'One strategist, *twelve markets.*',
            'lead' => 'Paid campaigns for e-commerce brands and lead-generation clients across North America, Europe, the Gulf, Central and South Asia and Australia — run from Lahore.',
        ],
        'markets' => [
            'eyebrow' => 'Markets',
            'title' => "Where I've run campaigns",
            'lead' => 'Grouped by the type of work done in each market.',
        ],
        'cards' => [
            ['icon' => 'cart', 'title' => 'E-commerce sales', 'where' => 'Australia · Canada · UK · USA', 'text' => 'Conversion-focused campaigns for fashion, home textiles, jewellery and food brands, including Google Search, Performance Max and remarketing for an international bedding brand. Portfolio average ~4x ROAS.'],
            ['icon' => 'funnel', 'title' => 'B2B & B2C lead generation', 'where' => 'USA · Canada', 'text' => 'Qualified lead generation through Meta, Google and TikTok Ads, with a focus on conversion performance and lead quality.'],
            ['icon' => 'briefcase', 'title' => 'Service-business leads', 'where' => 'UK · France', 'text' => 'Meta and TikTok lead campaigns with tailored targeting and creative tests, on daily budgets of PKR 20,000–40,000 across client accounts, optimised for cost per lead.'],
            ['icon' => 'map-pin', 'title' => 'Real estate leads', 'where' => 'Dubai · Azerbaijan · Uzbekistan', 'text' => 'Real estate and other paid lead campaigns, with creative angles and hooks directed by performance data.'],
            ['icon' => 'meta', 'title' => 'Meta campaigns', 'where' => 'Saudi Arabia · UAE · Oman · Bangladesh · Pakistan', 'text' => 'Meta campaigns for company and client brands, optimised through audience segmentation, budget monitoring and A/B tests.'],
            ['icon' => 'users', 'title' => 'Local lead generation', 'where' => 'Pakistan', 'text' => 'Lead generation for local businesses, managing up to 15 client accounts at the same time.'],
        ],
        'cta' => [
            'title' => 'Selling into a new market?',
            'text' => "I've planned audiences, budgets and creative for campaigns across four continents. Let's talk about where you want to grow.",
        ],
    ],

    'contact' => [
        'seo_title' => 'Hire a Meta & Google Ads Expert',
        'seo_description' => 'Contact {name}, performance marketer and Meta & Google Ads expert in Lahore, Pakistan, about ad campaigns, PPC management or full-time roles.',
        'hero' => [
            'eyebrow' => 'Contact',
            'title' => "Let's talk about *growing your results.*",
            'lead' => "Recruiting for a performance marketing role, or looking for someone to run your Meta and Google Ads? Send a message and I'll get back to you.",
        ],
        'form_title' => 'Send a message',
        'location_suffix' => 'Remote & on-site',
        'tips_title' => 'Good things to include',
        'tips' => [
            "Your business or the role you're hiring for",
            "Platforms you're using or considering",
            'Target markets and main goal — sales or leads',
            'Rough monthly budget, if known',
        ],
    ],

];
