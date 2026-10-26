<?php

/*
|--------------------------------------------------------------------------
| Portfolio content
|--------------------------------------------------------------------------
|
| Every fact on the website lives here. Profile, experience and results come
| from Ali Hassan's CV ("Ali Hassan Performance Marketing Manager.pdf").
| Additional international campaigns (UK & France; Saudi Arabia, UAE, Oman,
| Bangladesh & Pakistan) come from the second source document and were
| confirmed by the site owner as Ali's work on 2026-10-25, along with the
| roles listed in that document (SuperWise, Chapter001, IU Affilboost,
| Digimarkyl, Adymizing, Nextech Skills). Edit this file to
| update the site — no template changes are needed for new projects,
| results, services or roles.
|
| Rule of thumb: if a fact is not in a source document, it does not go here.
|
*/

return [

    // Password for /admin (before-and-after case studies). Empty = admin disabled.
    'admin_password' => env('ADMIN_PASSWORD'),

    // Hosting without database access at build time: create tables on first request.
    // AUTO_SEED also loads the demonstration case studies when that table is first created.
    'auto_setup' => (bool) env('AUTO_SETUP', false),
    'auto_seed' => (bool) env('AUTO_SEED', false),

    'person' => [
        'name' => 'Ali Hassan',
        'first_name' => 'Ali',
        'role' => 'Performance Marketing Manager',
        'location' => 'Lahore, Pakistan',
        'years' => '4+',
        'email' => env('PORTFOLIO_CONTACT_EMAIL'),
        'phone' => '+92 309 0796683',
        'linkedin' => 'https://www.linkedin.com/in/ali-hassan-sandhu',
        'linkedin_label' => 'linkedin.com/in/ali-hassan-sandhu',
        'show_email' => (bool) env('PORTFOLIO_SHOW_EMAIL', true),
        // Phone is hidden until its publication is approved.
        'show_phone' => (bool) env('PORTFOLIO_SHOW_PHONE', false),
        'notify_email' => env('PORTFOLIO_NOTIFY_EMAIL'),

        'headline' => 'Paid media that turns ad spend into revenue and qualified leads.',
        'intro' => 'Performance Marketing Manager with 4+ years in paid acquisition, e-commerce sales and B2B/B2C lead generation across Meta, Google and TikTok Ads — for brands selling into the USA, Canada, the UK and Australia.',
        'summary' => [
            'I plan, launch and scale paid campaigns that are built around one question: what does each click, lead and sale actually return? Over four years I have moved from managing local lead-generation accounts to leading a seven-person PPC team and cross-functional marketing teams of 10+ people.',
            'My work covers the full performance loop — account structure, audience and keyword research, conversion tracking with Meta Pixel, Google Tag Manager and GA4, creative direction with designers and video editors, and the day-to-day optimisation that keeps budgets pointed at what works.',
            'Across a portfolio of up to ten e-commerce brands, that loop delivered an average return on ad spend of approximately 4x, with top-performing campaigns reaching 8–10x.',
        ],
    ],

    /*
    | Headline figures. Each one carries the context it was measured in.
    */
    'stats' => [
        ['value' => 4, 'suffix' => '+', 'label' => 'Years in paid media', 'context' => 'Feb 2022 – present'],
        ['value' => 4, 'prefix' => '~', 'suffix' => 'x', 'label' => 'Average ROAS', 'context' => 'E-commerce portfolio, The Social Links'],
        ['value' => 10, 'prefix' => '8–', 'suffix' => 'x', 'label' => 'Peak campaign ROAS', 'context' => 'Top-performing campaigns, same portfolio'],
        ['value' => 7, 'suffix' => '', 'label' => 'Person PPC team led', 'context' => 'Dynasty Marketing Agency'],
    ],

    /*
    | Homepage tabs. Meta Ads first, Google Ads second.
    | Only services evidenced in the CV are listed.
    */
    'platforms' => [
        'meta' => [
            'name' => 'Meta Ads',
            'tagline' => 'Facebook & Instagram campaigns for sales and leads',
            'summary' => 'Meta is where most of my e-commerce revenue has been built — from fashion and home textiles to jewellery and food brands — and where I run B2B and B2C lead generation for clients in North America and beyond.',
            'services' => [
                ['icon' => 'target', 'title' => 'Audience targeting & segmentation', 'text' => 'Cold, retargeting and lookalike audiences, segmented and tuned to lead quality and acquisition cost — not just volume.'],
                ['icon' => 'cart', 'title' => 'E-commerce sales campaigns', 'text' => 'Conversion-focused campaigns for fashion, home textiles, jewellery and food brands in Australia, Canada, the UK and the USA.'],
                ['icon' => 'funnel', 'title' => 'B2B & B2C lead generation', 'text' => 'Lead campaigns for real estate, software and service businesses, monitored for lead quality and conversion volume.'],
                ['icon' => 'repeat', 'title' => 'Remarketing strategy', 'text' => 'Remarketing planned alongside prospecting to bring warm audiences back to convert.'],
                ['icon' => 'layers', 'title' => 'Creative testing & direction', 'text' => 'A/B tests on hooks, angles and formats, briefed to designers and video editors and revised from performance data.'],
                ['icon' => 'pulse', 'title' => 'Meta Pixel & conversion tracking', 'text' => 'Pixel setup and conversion events that give the algorithm — and the reporting — reliable signals.'],
            ],
            'metrics' => [
                ['value' => 'PKR 60–80k', 'label' => 'Daily Meta ad spend managed'],
                ['value' => 'Up to 10', 'label' => 'Brands run simultaneously'],
            ],
        ],
        'google' => [
            'name' => 'Google Ads',
            'tagline' => 'Search, Performance Max & remarketing that capture intent',
            'summary' => 'On Google I focus on capturing existing demand: tight keyword strategy, ad copy that earns clicks, and Performance Max and remarketing that close the loop — including for an international bedding brand.',
            'services' => [
                ['icon' => 'search', 'title' => 'Search campaigns', 'text' => 'Keyword research, match strategy and ad copywriting for Search campaigns built around buying intent.'],
                ['icon' => 'spark', 'title' => 'Performance Max', 'text' => 'Performance Max campaigns for e-commerce, managed alongside Search and remarketing.'],
                ['icon' => 'display', 'title' => 'Display & remarketing', 'text' => 'Display and remarketing campaigns that re-engage visitors who did not convert first time.'],
                ['icon' => 'gauge', 'title' => 'Quality Score & CTR', 'text' => 'Ongoing Quality Score and click-through-rate improvements to lower costs and lift ad rank.'],
                ['icon' => 'bid', 'title' => 'Bidding strategy', 'text' => 'Bid management and bidding strategy chosen to match each account\'s conversion goals and budget.'],
                ['icon' => 'tag', 'title' => 'GTM & GA4 tracking', 'text' => 'Google Tag Manager and GA4 conversion tracking so optimisation decisions rest on accurate data.'],
            ],
            'metrics' => [
                ['value' => 'Search · PMax', 'label' => 'Plus Display and remarketing'],
                ['value' => 'GTM + GA4', 'label' => 'Conversion tracking stack'],
            ],
        ],
    ],

    // Other platforms named in the CV.
    'also_runs' => ['TikTok Ads', 'TikTok Pixel', 'Snapchat Ads'],

    /*
    | Services page. Grouped into the categories requested in the brief,
    | each backed by CV evidence.
    */
    'services' => [
        [
            'slug' => 'meta-ads',
            'icon' => 'meta',
            'title' => 'Meta Ads Management',
            'text' => 'End-to-end Facebook and Instagram campaigns for e-commerce sales and lead generation, from account structure to daily optimisation.',
            'points' => ['Campaign setup & account structure', 'Prospecting and remarketing', 'Creative testing with design & video teams', 'Meta Pixel setup'],
        ],
        [
            'slug' => 'google-ads',
            'icon' => 'google',
            'title' => 'Google Ads Management',
            'text' => 'Search, Display, Performance Max and remarketing campaigns that capture intent and turn it into sales and enquiries.',
            'points' => ['Search & Performance Max', 'Display & remarketing', 'Ad copywriting', 'Quality Score & CTR improvement'],
        ],
        [
            'slug' => 'strategy',
            'icon' => 'compass',
            'title' => 'Campaign Strategy & Planning',
            'text' => 'Campaign objectives, budget distribution and channel mix planned around each brand\'s goals — across single accounts or multi-brand portfolios.',
            'points' => ['Objectives & funnel planning', 'Budget allocation', 'Multi-brand portfolio management', 'TikTok Ads alongside Meta & Google'],
        ],
        [
            'slug' => 'research',
            'icon' => 'target',
            'title' => 'Audience & Keyword Research',
            'text' => 'Finding the people and the search terms that convert — and cutting the ones that only spend.',
            'points' => ['Keyword research & bidding strategy', 'Audience targeting & segmentation', 'Local and international market targeting', 'Lead-quality review'],
        ],
        [
            'slug' => 'tracking',
            'icon' => 'pulse',
            'title' => 'Conversion Tracking & Analytics',
            'text' => 'Tracking that platforms and people can trust: pixels, tags and analytics configured so every optimisation is based on real conversions.',
            'points' => ['Meta Pixel & TikTok Pixel', 'Google Tag Manager', 'GA4 conversion tracking', 'Lead and conversion event tracking'],
        ],
        [
            'slug' => 'optimisation',
            'icon' => 'chart',
            'title' => 'Campaign Optimisation & Reporting',
            'text' => 'Regular performance reviews that turn results into the next round of tests — and clear reporting on what to keep, change or cut.',
            'points' => ['A/B testing & creative optimisation', 'Bid, budget & audience adjustments', 'Performance reporting', 'Team review of conversion signals'],
        ],
    ],

    'process' => [
        ['title' => 'Track', 'text' => 'Set up Meta Pixel, GTM and GA4 conversion events first, so every decision rests on reliable data.'],
        ['title' => 'Plan', 'text' => 'Define objectives, audiences, keywords and budget distribution for sales or lead generation.'],
        ['title' => 'Launch & test', 'text' => 'Launch with structured creative and audience tests, briefed with designers and video editors.'],
        ['title' => 'Optimise & report', 'text' => 'Review performance, conversion signals and lead quality; shift budget, bids and creative to what works.'],
    ],

    'skills' => [
        'Advertising platforms' => ['Meta Ads', 'Google Ads — Search, Display, Performance Max, Remarketing', 'TikTok Ads', 'Snapchat Ads'],
        'Tracking & analytics' => ['Meta Pixel setup', 'TikTok Pixel setup', 'Google Tag Manager', 'GA4 conversion tracking', 'Campaign reporting & performance analysis'],
        'Growth & optimisation' => ['Keyword research & bidding strategy', 'Quality Score & CTR optimisation', 'Audience targeting & segmentation', 'A/B testing & creative optimisation', 'Ad copywriting'],
        'Social & AI content' => ['Social media management & posting', 'Content calendars & scheduling', 'Organic growth & community engagement', 'AI content — ChatGPT, Gemini', 'AI videos & reels — HeyGen, Captions AI'],
        'Leadership' => ['Team leadership', 'Multi-brand portfolio management', 'Client communication', 'E-commerce campaigns', 'Lead generation'],
    ],

    'experience' => [
        [
            'role' => 'PPC Specialist (Team Lead)',
            'company' => 'Dynasty Marketing Agency',
            'mode' => 'On-site',
            'period' => 'Aug 2026 – Present',
            'highlights' => [
                'Lead a seven-person team of PPC and Google Ads specialists — campaign priorities, account workflows, client delivery and performance reporting.',
                'Generate qualified B2B and B2C leads through Meta, Google and TikTok Ads for clients in the USA and Canada, with a focus on conversion performance and lead quality.',
                'Oversee end-to-end campaign setup, keyword research, audience targeting, budget allocation, bid management and account optimisation.',
                'Work with graphic designers and video editors on ad concepts, messaging experiments, video formats and revisions to weaker assets.',
            ],
        ],
        [
            'role' => 'Senior Performance Marketing Specialist',
            'company' => 'SuperWise International Pvt. Ltd.',
            'mode' => 'Social & paid media',
            'period' => 'Aug 2026 – Present',
            'highlights' => [
                'Manage the company\'s social media accounts end to end — content strategy, monthly content calendars, regular posting and consistent brand messaging.',
                'Create posts, short-form videos, reels, scripts and captions with ChatGPT, Gemini, HeyGen and Captions AI to keep a steady content pipeline.',
                'Plan, build, launch and optimise Meta Ads and Google Ads campaigns — setup, audience targeting, creatives, budgets and ongoing testing.',
                'Review reach, impressions, engagement, CTR, CPC and lead metrics to refine content and improve campaign efficiency.',
            ],
        ],
        [
            'role' => 'Performance Marketing Manager',
            'company' => 'Zela Boost',
            'mode' => 'Remote',
            'period' => 'Jun 2026 – Present',
            'highlights' => [
                'Manage Meta, Google and TikTok campaigns for real estate, e-commerce, software and service-based businesses.',
                'Oversee an eight-brand portfolio: three e-commerce and five lead-generation accounts, including day-to-day setup and delivery.',
                'Plan objectives, targeting, budget distribution, remarketing, ad copy and creative testing for sales and paid leads.',
                'Track lead quality, conversion volume and acquisition costs, adjusting segments, bid strategies, budgets and creative tests.',
            ],
        ],
        [
            'role' => 'Performance Marketing Team Lead',
            'company' => 'NextQuick Solutions',
            'mode' => 'Marketing Team',
            'period' => 'Jul 2026 – Aug 2026',
            'highlights' => [
                'Led a cross-functional marketing team of 10+ — paid media specialists, graphic designers and video content creators.',
                'Managed advertising for five e-commerce brands and lead generation for 11 more brands across a multi-account portfolio.',
                'Ran real estate and other paid lead campaigns targeting Dubai, Azerbaijan and Uzbekistan.',
                'Directed creative angles, hooks, ad formats and tests based on campaign performance data.',
            ],
        ],
        [
            'role' => 'Performance Marketer',
            'company' => 'Chapter001',
            'mode' => 'Remote',
            'period' => 'Mar 2026 – Present',
            'highlights' => [
                'Launch and optimise Meta and TikTok campaigns for education, cloud restaurant and local-business clients — targeting, bids, budgets and creative testing.',
                'Plan social content calendars and posting, with AI-assisted copy, video concepts, reels and engagement-focused creatives.',
                'Monitor reach, engagement, traffic, CTR, CPL and ROAS, using reports to improve both organic content and paid results.',
            ],
        ],
        [
            'role' => 'Senior Performance Marketer',
            'company' => 'IU Affilboost',
            'mode' => 'UK & France clients',
            'period' => 'Mar 2026 – Aug 2026',
            'highlights' => [
                'Managed Meta Ads and TikTok Ads for UK and France service businesses, generating qualified leads through tailored targeting, creative tests and conversion-focused campaigns.',
                'Led the marketing team and managed daily ad budgets of PKR 20,000–40,000 across client accounts, testing audiences and creatives to optimise CPL.',
                'Developed social content strategies and publishing plans, working with creators on branded posts, reels and AI-assisted video.',
                'Analysed campaign KPIs and audience behaviour; prepared reports with recommendations aligned to business objectives.',
            ],
        ],
        [
            'role' => 'Assistant Performance Marketer',
            'company' => 'Digimarkyl',
            'mode' => 'Remote',
            'period' => 'Dec 2025 – Jul 2026',
            'highlights' => [
                'Helped execute and optimise Meta campaigns in Pakistan, Saudi Arabia, the UAE, Oman and Bangladesh through segmentation, budget monitoring and A/B tests.',
                'Supported company and client social media through content calendars, posting, creative coordination and AI-assisted posts and videos.',
                'Tracked campaign and social KPIs, contributed performance reports and worked with creative teams on brand presence and lead generation.',
            ],
        ],
        [
            'role' => 'Performance Marketer (Paid Internship)',
            'company' => 'Adymizing',
            'mode' => 'Internship',
            'period' => 'Sep 2025 – Dec 2025',
            'highlights' => [
                'Managed Meta campaigns for e-commerce, digital services and education, testing cold, retargeting and lookalike audiences.',
                'Monitored ROAS, CTR, CPL and CPA while supporting content planning, scheduled publishing and ad creative preparation.',
            ],
        ],
        [
            'role' => 'Performance Marketer (Internship)',
            'company' => 'Nextech Skills',
            'mode' => 'Internship',
            'period' => 'May 2025 – Aug 2025',
            'highlights' => [
                'Supported Meta Ads setup, student lead-generation funnels, audience targeting and A/B testing.',
                'Tracked CTR, CPM, CPL and conversions; assisted with content calendars, posting and AI-supported short-form video concepts.',
            ],
        ],
        [
            'role' => 'Media Buyer',
            'company' => 'The Social Links',
            'mode' => 'International campaigns',
            'period' => 'Dec 2024 – Jun 2026',
            'highlights' => [
                'Launched and optimised conversion-focused campaigns driving e-commerce sales in Australia, Canada, the UK and the USA.',
                'Scaled from two or three accounts to as many as 10 brands at once, managing approximately PKR 60,000–80,000 in daily Meta ad spend.',
                'Delivered an average ROAS of approximately 4x across the portfolio, with top campaigns reaching 8–10x.',
                'Managed Google Search, Performance Max and remarketing for an international bedding brand; expanded fashion and food brands onto TikTok Ads.',
            ],
        ],
        [
            'role' => 'Media Buyer',
            'company' => 'Orbit Creator',
            'mode' => 'Multan, Pakistan',
            'period' => 'Feb 2022 – Dec 2024',
            'highlights' => [
                'Managed lead-generation campaigns for local businesses through Meta, Google and TikTok Ads.',
                'Handled up to 15 client accounts at once — setup, targeting, publishing, budget monitoring and optimisation.',
                'Implemented conversion tracking with Meta Pixel, Google Tag Manager and GA4, tracking leads and conversion events to improve acquisition.',
            ],
        ],
    ],

    'education' => [
        ['title' => 'Bachelor\'s in Computer Science', 'school' => 'Time\'s Institute, Multan, Pakistan', 'period' => '2020 – 2024'],
        ['title' => 'FSc (Pre-Engineering)', 'school' => 'Pakistan College, Multan', 'period' => '2017 – 2019'],
    ],

    // No certifications are listed in the CV. Add them here once confirmed,
    // e.g. ['title' => 'Google Ads Search Certification', 'issuer' => 'Google', 'year' => '2026'].
    'certifications' => [],

    /*
    | Portfolio case studies. Client names are not in the CV, so each project
    | is described by industry and market. "outcome" is only set where the CV
    | documents a result for that body of work.
    |
    | platforms: meta | google | tiktok      category: ecommerce | leads | real-estate
    */
    'projects' => [
        [
            'slug' => 'ecommerce-portfolio-scale',
            'title' => 'Scaling a multi-brand e-commerce portfolio',
            'category' => 'ecommerce',
            'industry' => 'Fashion, home textiles, jewellery & food',
            'markets' => ['Australia', 'Canada', 'UK', 'USA'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Grow e-commerce sales profitably while scaling from a handful of accounts to a full brand portfolio.',
            'work' => ['Conversion-focused campaign builds', 'Audience segmentation & A/B testing', 'GA4 & GTM conversion tracking', 'Creative optimisation'],
            'outcome' => '~4x average ROAS across the portfolio; top campaigns at 8–10x. Grew from 2–3 to up to 10 brands at PKR 60–80k daily Meta spend.',
            'period' => 'Dec 2024 – Jun 2026',
            'featured' => true,
        ],
        [
            'slug' => 'international-bedding-brand',
            'title' => 'Google Ads for an international bedding brand',
            'category' => 'ecommerce',
            'industry' => 'Home textiles / bedding',
            'markets' => ['International'],
            'platforms' => ['google'],
            'objective' => 'Capture high-intent search demand and recover lost visitors for an online bedding store.',
            'work' => ['Google Search campaigns', 'Performance Max', 'Remarketing', 'Keyword research, bidding & Quality Score work'],
            'outcome' => null,
            'period' => 'Dec 2024 – Jun 2026',
            'featured' => true,
        ],
        [
            'slug' => 'gulf-central-asia-real-estate',
            'title' => 'Real estate lead generation across three markets',
            'category' => 'real-estate',
            'industry' => 'Real estate',
            'markets' => ['Dubai (UAE)', 'Azerbaijan', 'Uzbekistan'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Generate paid leads for real estate offers in the Gulf and Central Asia.',
            'work' => ['Lead campaign setup & account structure', 'Audience selection', 'Creative angle & hook testing', 'Budget decisions & analysis'],
            'outcome' => null,
            'period' => 'Jul 2026 – Aug 2026',
            'featured' => true,
        ],
        [
            'slug' => 'uk-france-service-leads',
            'title' => 'Qualified leads for UK & French service businesses',
            'category' => 'leads',
            'industry' => 'Service businesses',
            'markets' => ['UK', 'France'],
            'platforms' => ['meta', 'tiktok'],
            'objective' => 'Generate qualified leads for service businesses in the UK and France at an efficient cost per lead.',
            'work' => ['Tailored audience targeting', 'Creative testing', 'Conversion-focused campaigns', 'CPL optimisation', 'KPI reporting & recommendations'],
            'outcome' => 'Daily ad budgets of PKR 20,000–40,000 managed across client accounts.',
            'period' => 'Mar 2026 – Aug 2026',
            'featured' => true,
        ],
        [
            'slug' => 'gulf-south-asia-meta',
            'title' => 'Meta campaigns across the Gulf & South Asia',
            'category' => 'leads',
            'industry' => 'Company & client brands',
            'markets' => ['Saudi Arabia', 'UAE', 'Oman', 'Bangladesh', 'Pakistan'],
            'platforms' => ['meta'],
            'objective' => 'Strengthen brand presence and lead generation for company and client accounts in five markets.',
            'work' => ['Audience segmentation', 'Budget monitoring', 'A/B testing', 'KPI tracking & performance reports'],
            'outcome' => null,
            'period' => 'Dec 2025 – Jul 2026',
            'featured' => false,
        ],
        [
            'slug' => 'north-america-lead-gen',
            'title' => 'B2B & B2C lead generation for North American clients',
            'category' => 'leads',
            'industry' => 'Agency clients — B2B & B2C',
            'markets' => ['USA', 'Canada'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Deliver qualified leads with strong conversion performance — not just lead volume.',
            'work' => ['Keyword research & audience targeting', 'Budget allocation & bid management', 'Ad-testing priorities from lead-quality reviews', 'Leading a seven-person PPC team'],
            'outcome' => null,
            'period' => 'Aug 2026 – Present',
            'featured' => false,
        ],
        [
            'slug' => 'tiktok-expansion',
            'title' => 'Taking fashion & food brands onto TikTok',
            'category' => 'ecommerce',
            'industry' => 'Fashion & food',
            'markets' => ['Australia', 'Canada', 'UK', 'USA'],
            'platforms' => ['tiktok', 'meta'],
            'objective' => 'Open a new acquisition channel alongside core Meta Ads activity.',
            'work' => ['TikTok Ads campaign execution', 'Cross-platform creative testing', 'Audience segmentation'],
            'outcome' => null,
            'period' => 'Dec 2024 – Jun 2026',
            'featured' => false,
        ],
        [
            'slug' => 'eight-brand-portfolio',
            'title' => 'Running an eight-brand sales & leads portfolio',
            'category' => 'leads',
            'industry' => 'Real estate, e-commerce, software & services',
            'markets' => ['Multiple markets'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Drive sales for three e-commerce brands and paid leads for five service businesses.',
            'work' => ['Remarketing strategy', 'Budget distribution', 'Ad copy & creative testing', 'Lead quality & CPA monitoring'],
            'outcome' => null,
            'period' => 'Jun 2026 – Present',
            'featured' => false,
        ],
        [
            'slug' => 'multi-account-team-portfolio',
            'title' => 'Leading a 16-brand marketing operation',
            'category' => 'leads',
            'industry' => 'E-commerce & lead generation',
            'markets' => ['Multiple markets'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Coordinate paid media, design and video teams across five e-commerce and 11 lead-generation brands.',
            'work' => ['Team of 10+ across media, design & video', 'Account structures & audience selection', 'Creative direction from performance data'],
            'outcome' => null,
            'period' => 'Jul 2026 – Aug 2026',
            'featured' => false,
        ],
        [
            'slug' => 'local-business-leads',
            'title' => 'Local lead generation for 15 concurrent clients',
            'category' => 'leads',
            'industry' => 'Local businesses',
            'markets' => ['Pakistan'],
            'platforms' => ['meta', 'google', 'tiktok'],
            'objective' => 'Generate leads for local businesses with tight local-market targeting.',
            'work' => ['Campaign setup & publishing', 'Local-market audience targeting', 'Meta Pixel, GTM & GA4 tracking', 'Budget monitoring & optimisation'],
            'outcome' => 'Up to 15 client accounts managed at the same time.',
            'period' => 'Feb 2022 – Dec 2024',
            'featured' => false,
        ],
    ],

    'categories' => [
        'ecommerce' => 'E-commerce',
        'leads' => 'Lead generation',
        'real-estate' => 'Real estate',
    ],

    'platform_labels' => [
        'meta' => 'Meta Ads',
        'google' => 'Google Ads',
        'tiktok' => 'TikTok Ads',
    ],

    /*
    | Markets. lat/lng drive the 3D globe on the International page.
    */
    'markets' => [
        ['name' => 'United Kingdom', 'code' => 'UK', 'lat' => 51.5, 'lng' => -0.1, 'region' => 'Europe', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'E-commerce sales & service leads', 'work' => 'Conversion campaigns for fashion, home textiles, jewellery and food brands, plus qualified lead generation for service businesses.'],
        ['name' => 'France', 'code' => 'FR', 'lat' => 48.9, 'lng' => 2.35, 'region' => 'Europe', 'platforms' => ['meta', 'tiktok'], 'focus' => 'Service-business leads', 'work' => 'Qualified lead generation for service businesses through tailored targeting and creative tests.'],
        ['name' => 'United States', 'code' => 'US', 'lat' => 39.8, 'lng' => -98.6, 'region' => 'North America', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'E-commerce & B2B/B2C leads', 'work' => 'E-commerce sales campaigns and qualified lead generation for agency clients.'],
        ['name' => 'Canada', 'code' => 'CA', 'lat' => 45.4, 'lng' => -75.7, 'region' => 'North America', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'E-commerce & B2B/B2C leads', 'work' => 'E-commerce sales campaigns and qualified lead generation for agency clients.'],
        ['name' => 'Australia', 'code' => 'AU', 'lat' => -33.9, 'lng' => 151.2, 'region' => 'Oceania', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'E-commerce sales', 'work' => 'Conversion campaigns for fashion, home textiles, jewellery and food brands.'],
        ['name' => 'Dubai, UAE', 'code' => 'AE', 'lat' => 25.2, 'lng' => 55.3, 'region' => 'Middle East', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'Real estate leads & Meta campaigns', 'work' => 'Paid lead campaigns for real estate and other offers, plus Meta campaigns for company and client brands.'],
        ['name' => 'Saudi Arabia', 'code' => 'SA', 'lat' => 24.7, 'lng' => 46.7, 'region' => 'Middle East', 'platforms' => ['meta'], 'focus' => 'Meta campaigns', 'work' => 'Meta campaigns optimised through audience segmentation, budget monitoring and A/B tests.'],
        ['name' => 'Oman', 'code' => 'OM', 'lat' => 23.6, 'lng' => 58.4, 'region' => 'Middle East', 'platforms' => ['meta'], 'focus' => 'Meta campaigns', 'work' => 'Meta campaigns optimised through audience segmentation, budget monitoring and A/B tests.'],
        ['name' => 'Bangladesh', 'code' => 'BD', 'lat' => 23.8, 'lng' => 90.4, 'region' => 'South Asia', 'platforms' => ['meta'], 'focus' => 'Meta campaigns', 'work' => 'Meta campaigns optimised through audience segmentation, budget monitoring and A/B tests.'],
        ['name' => 'Azerbaijan', 'code' => 'AZ', 'lat' => 40.4, 'lng' => 49.9, 'region' => 'Caucasus', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'Real estate leads', 'work' => 'Paid lead campaigns for real estate and other offers.'],
        ['name' => 'Uzbekistan', 'code' => 'UZ', 'lat' => 41.3, 'lng' => 69.2, 'region' => 'Central Asia', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'Real estate leads', 'work' => 'Paid lead campaigns for real estate and other offers.'],
        ['name' => 'Pakistan', 'code' => 'PK', 'lat' => 31.5, 'lng' => 74.3, 'region' => 'Home market', 'platforms' => ['meta', 'google', 'tiktok'], 'focus' => 'Local lead generation', 'work' => 'Lead generation for local businesses (up to 15 accounts at once) and Meta campaigns for company and client brands.'],
    ],

];
