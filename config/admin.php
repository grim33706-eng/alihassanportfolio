<?php

/*
|--------------------------------------------------------------------------
| Admin content editor schema
|--------------------------------------------------------------------------
|
| Every editable section of the site. The admin forms are generated from this
| file, so making something new editable is a matter of adding a field here.
|
| A section has one or more "parts"; each part edits one config key:
|   - 'fields' → an object; only the listed fields are saved (others untouched)
|   - 'item'   → a list of objects (add / remove / reorder)
|   - 'strings'→ a list of plain strings (one per line)
|   - 'map'    → an object of name => list, edited as a list of {name, items}
|
| Field types: text, rich (supports *highlight*), richarea (multi-line rich),
| textarea, number, toggle, select, checkboxes, icon, list (one per line),
| repeater (list of objects), group (nested object), image.
|
*/

// Pages with a 3D hero animation (Home, International) pass $image = false so it is never replaced.
$hero = fn (array $extra = [], bool $image = true) => ['name' => 'hero', 'type' => 'group', 'label' => 'Hero', 'fields' => array_merge([
    ['name' => 'eyebrow', 'label' => 'Small label above the title', 'type' => 'text'],
    ['name' => 'title', 'label' => 'Title', 'type' => 'rich'],
    ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea'],
], $image ? [
    ['name' => 'image', 'label' => 'Hero image (optional)', 'type' => 'image', 'help' => 'Shown beside the title in place of the decorative graphic.'],
] : [], $extra)];

$heading = fn (string $name, string $label, bool $lead = false, array $extra = []) => [
    'name' => $name, 'type' => 'group', 'label' => $label, 'fields' => array_merge(
        [
            ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
            ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
        ],
        $lead ? [['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea']] : [],
        $extra,
    ),
];

$seo = [
    ['name' => 'seo_title', 'label' => 'Browser tab title', 'type' => 'text', 'help' => 'Shown as "Title | Name" in the browser tab and Google results (the name is not repeated if the title already contains it). Keep it under about 60 characters.'],
    ['name' => 'seo_description', 'label' => 'Search engine description', 'type' => 'textarea', 'help' => 'The snippet under the title in Google. Aim for 140–160 characters and include the main keywords naturally.'],
];

$cta = ['name' => 'cta', 'type' => 'group', 'label' => 'Call-to-action band', 'help' => 'Leave empty to use the shared default from Pages → Shared.', 'fields' => [
    ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
    ['name' => 'text', 'label' => 'Text', 'type' => 'textarea'],
]];

$iconText = fn (string $text = 'text') => [
    ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
    ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
    ['name' => $text, 'label' => 'Text', 'type' => 'textarea'],
];

return [

    'icons' => [
        'target', 'cart', 'funnel', 'repeat', 'layers', 'pulse', 'search', 'spark', 'display', 'gauge', 'bid', 'tag',
        'compass', 'chart', 'meta', 'google', 'tiktok', 'mail', 'linkedin', 'phone', 'map-pin', 'globe', 'check',
        'users', 'briefcase', 'graduation', 'image', 'alert',
    ],

    'groups' => ['Pages', 'Content'],

    'sections' => [

        /* ===================== PAGES ===================== */

        'home' => [
            'label' => 'Home', 'group' => 'Pages', 'icon' => 'display', 'route' => 'home',
            'description' => 'Every heading, button and short list on the home page.',
            'parts' => [[
                'key' => 'content.home',
                'fields' => array_merge($seo, [
                    ['name' => 'hero', 'type' => 'group', 'label' => 'Hero', 'help' => 'The intro paragraph comes from Content → Profile.', 'fields' => [
                        ['name' => 'title', 'label' => 'Headline', 'type' => 'rich'],
                        ['name' => 'primary', 'label' => 'Main button', 'type' => 'text'],
                        ['name' => 'secondary', 'label' => 'Second button', 'type' => 'text'],
                        ['name' => 'chips', 'label' => 'Highlight chips', 'type' => 'repeater', 'item_label' => 'value', 'max' => 2, 'fields' => [
                            ['name' => 'value', 'label' => 'Figure', 'type' => 'text'],
                            ['name' => 'label', 'label' => 'Caption (line breaks allowed)', 'type' => 'richarea'],
                        ]],
                        ['name' => 'platforms_chip', 'label' => 'Platforms chip text', 'type' => 'text'],
                        ['name' => 'markets_label', 'label' => 'Markets strip label', 'type' => 'text'],
                        ['name' => 'markets', 'label' => 'Markets strip', 'type' => 'list'],
                    ]],
                    $heading('platforms', 'Meta / Google tabs section', true, [['name' => 'also_label', 'label' => '"Also running" label', 'type' => 'text']]),
                    $heading('intro', 'Introduction', false, [
                        ['name' => 'facts', 'label' => 'Facts on the portrait card', 'type' => 'repeater', 'item_label' => 'text', 'fields' => [
                            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                            ['name' => 'text', 'label' => 'Text', 'type' => 'text'],
                        ]],
                        ['name' => 'button', 'label' => 'Button', 'type' => 'text'],
                    ]),
                    $heading('services', 'Services section', false, [['name' => 'button', 'label' => 'Button', 'type' => 'text']]),
                    $heading('skills', 'Expertise section', false, [['name' => 'marquee', 'label' => 'Scrolling words', 'type' => 'list']]),
                    $heading('work', 'Selected work section', true),
                    ['name' => 'explore', 'type' => 'group', 'label' => '"Explore every campaign" card', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                        ['name' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                        ['name' => 'button', 'label' => 'Button', 'type' => 'text'],
                    ]],
                    ['name' => 'case_studies', 'type' => 'group', 'label' => 'Before & after section', 'fields' => [
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea'],
                    ]],
                    $heading('international', 'International section', true, [['name' => 'button', 'label' => 'Button', 'type' => 'text']]),
                    $heading('achievements', 'Achievements', false, [
                        ['name' => 'items', 'label' => 'Milestones', 'type' => 'repeater', 'item_label' => 'headline', 'fields' => [
                            ['name' => 'headline', 'label' => 'Headline', 'type' => 'text'],
                            ['name' => 'detail', 'label' => 'Detail', 'type' => 'textarea'],
                        ]],
                        ['name' => 'button', 'label' => 'Button', 'type' => 'text'],
                    ]),
                    $heading('education', 'Education heading'),
                    $cta,
                ]),
            ]],
        ],

        'about' => [
            'label' => 'About', 'group' => 'Pages', 'icon' => 'users', 'route' => 'about',
            'description' => 'Headings and facts on the About page. The career timeline is under Content → Experience.',
            'parts' => [[
                'key' => 'content.about',
                'fields' => array_merge($seo, [
                    $hero([
                        ['name' => 'primary', 'label' => 'Main button', 'type' => 'text'],
                        ['name' => 'secondary', 'label' => 'LinkedIn button', 'type' => 'text'],
                    ]),
                    $heading('background', 'Background', false, [
                        ['name' => 'facts', 'label' => 'Fact cards', 'type' => 'repeater', 'item_label' => 'label', 'fields' => [
                            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                            ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ['name' => 'value', 'label' => 'Value', 'type' => 'textarea'],
                        ]],
                    ]),
                    $heading('specialisation', 'Specialisation', false, [
                        ['name' => 'cards', 'label' => 'Cards', 'type' => 'repeater', 'item_label' => 'title', 'fields' => $iconText()],
                    ]),
                    $heading('approach', 'Approach heading', true),
                    $heading('experience', 'Experience heading'),
                    $heading('skills', 'Skills heading'),
                    $heading('education', 'Education heading'),
                    $cta,
                ]),
            ]],
        ],

        'services-page' => [
            'label' => 'Services', 'group' => 'Pages', 'icon' => 'layers', 'route' => 'services',
            'description' => 'Headings on the Services page. The service cards are under Content → Services.',
            'parts' => [[
                'key' => 'content.services',
                'fields' => array_merge($seo, [
                    $hero([['name' => 'button', 'label' => 'Button', 'type' => 'text']]),
                    $heading('platforms', 'By platform section', false, [['name' => 'also_label', 'label' => '"Also available" label', 'type' => 'text']]),
                    $heading('process', 'Process heading'),
                    $heading('industries', 'Industries', false, [
                        ['name' => 'items', 'label' => 'Industries', 'type' => 'repeater', 'item_label' => 'title', 'fields' => $iconText()],
                    ]),
                    $heading('faq', 'FAQ', false, [
                        ['name' => 'items', 'label' => 'Questions', 'type' => 'repeater', 'item_label' => 'q', 'help' => 'Shown on the Services page and given to Google as FAQ results. Keep answers factual.', 'fields' => [
                            ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                            ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                        ]],
                    ]),
                    $cta,
                ]),
            ]],
        ],

        'portfolio-page' => [
            'label' => 'Portfolio', 'group' => 'Pages', 'icon' => 'briefcase', 'route' => 'portfolio',
            'description' => 'Headings and filter labels on the Portfolio page. The projects are under Content → Projects.',
            'parts' => [[
                'key' => 'content.portfolio',
                'fields' => array_merge($seo, [
                    $hero(),
                    ['name' => 'filters', 'type' => 'group', 'label' => 'Filter panel', 'fields' => [
                        ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                        ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text'],
                        ['name' => 'showing', 'label' => '"Showing" label', 'type' => 'text'],
                        ['name' => 'all_projects', 'label' => '"All projects" label', 'type' => 'text'],
                        ['name' => 'reset', 'label' => 'Reset button', 'type' => 'text'],
                        ['name' => 'empty', 'label' => 'No-results message', 'type' => 'text'],
                    ]],
                    ['name' => 'case_studies', 'type' => 'group', 'label' => 'Before & after section', 'fields' => [
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea'],
                    ]],
                    $cta,
                ]),
            ]],
        ],

        'results-page' => [
            'label' => 'Results', 'group' => 'Pages', 'icon' => 'chart', 'route' => 'results',
            'description' => 'Headings, chart figures and lists on the Results page. The four headline figures are under Content → Key figures.',
            'parts' => [[
                'key' => 'content.results',
                'fields' => array_merge($seo, [
                    $hero([
                        ['name' => 'pin_average', 'label' => 'Graph pin: average', 'type' => 'text'],
                        ['name' => 'pin_peak', 'label' => 'Graph pin: peak', 'type' => 'text'],
                    ]),
                    ['name' => 'case_studies', 'type' => 'group', 'label' => 'Before & after section', 'fields' => [
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea'],
                    ]],
                    ['name' => 'roas', 'type' => 'group', 'label' => 'Return on ad spend chart', 'help' => 'The chart scale runs from 0x to 10x.', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea'],
                        ['name' => 'chart_title', 'label' => 'Chart title', 'type' => 'text'],
                        ['name' => 'average', 'label' => 'Average ROAS (number)', 'type' => 'number', 'step' => '0.1'],
                        ['name' => 'average_label', 'label' => 'Average label', 'type' => 'text'],
                        ['name' => 'average_caption', 'label' => 'Average caption', 'type' => 'text'],
                        ['name' => 'average_tip', 'label' => 'Average tooltip', 'type' => 'text'],
                        ['name' => 'peak_from', 'label' => 'Peak range from (number)', 'type' => 'number', 'step' => '0.1'],
                        ['name' => 'peak_to', 'label' => 'Peak range to (number)', 'type' => 'number', 'step' => '0.1'],
                        ['name' => 'peak_label', 'label' => 'Peak label', 'type' => 'text'],
                        ['name' => 'peak_caption', 'label' => 'Peak caption', 'type' => 'text'],
                        ['name' => 'peak_tip', 'label' => 'Peak tooltip', 'type' => 'text'],
                        ['name' => 'breakeven_caption', 'label' => 'Break-even caption', 'type' => 'text'],
                        ['name' => 'table_caption', 'label' => 'Screen-reader table caption', 'type' => 'text'],
                        ['name' => 'table_average', 'label' => 'Screen-reader average value', 'type' => 'text'],
                        ['name' => 'note', 'label' => 'Note under the chart', 'type' => 'textarea'],
                    ]],
                    ['name' => 'scale', 'type' => 'group', 'label' => 'Scale section', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'chart_title', 'label' => 'Bar chart title', 'type' => 'text'],
                        ['name' => 'chart_subtitle', 'label' => 'Bar chart subtitle', 'type' => 'text'],
                        ['name' => 'bars', 'label' => 'Bars', 'type' => 'repeater', 'item_label' => 'role', 'fields' => [
                            ['name' => 'role', 'label' => 'Name', 'type' => 'text'],
                            ['name' => 'detail', 'label' => 'Detail', 'type' => 'text'],
                            ['name' => 'value', 'label' => 'Value (number)', 'type' => 'number'],
                            ['name' => 'unit', 'label' => 'Unit', 'type' => 'text'],
                        ]],
                        ['name' => 'growth_label', 'label' => 'Growth card: label', 'type' => 'text'],
                        ['name' => 'growth_from', 'label' => 'Growth card: from', 'type' => 'text'],
                        ['name' => 'growth_from_unit', 'label' => 'Growth card: from unit', 'type' => 'text'],
                        ['name' => 'growth_to', 'label' => 'Growth card: to', 'type' => 'text'],
                        ['name' => 'growth_to_unit', 'label' => 'Growth card: to unit', 'type' => 'text'],
                        ['name' => 'spend_label', 'label' => 'Spend card: label', 'type' => 'text'],
                        ['name' => 'spend_value', 'label' => 'Spend card: value', 'type' => 'text'],
                        ['name' => 'spend_note', 'label' => 'Spend card: note', 'type' => 'text'],
                        ['name' => 'teams_label', 'label' => 'Teams card: label', 'type' => 'text'],
                        ['name' => 'teams_a', 'label' => 'Teams card: first figure', 'type' => 'text'],
                        ['name' => 'teams_a_unit', 'label' => 'Teams card: first unit', 'type' => 'text'],
                        ['name' => 'teams_b', 'label' => 'Teams card: second figure', 'type' => 'text'],
                        ['name' => 'teams_b_unit', 'label' => 'Teams card: second unit', 'type' => 'text'],
                    ]],
                    $heading('drivers', 'What drives the numbers', false, [
                        ['name' => 'items', 'label' => 'Cards', 'type' => 'repeater', 'item_label' => 'title', 'fields' => $iconText()],
                    ]),
                    $heading('markets', 'Markets table', false, [['name' => 'fine_print', 'label' => 'Note under the table', 'type' => 'textarea']]),
                    $cta,
                ]),
            ]],
        ],

        'international-page' => [
            'label' => 'International', 'group' => 'Pages', 'icon' => 'globe', 'route' => 'international',
            'description' => 'Headings and cards on the International page. The market list is under Content → Markets.',
            'parts' => [[
                'key' => 'content.international',
                'fields' => array_merge($seo, [
                    $hero(image: false),
                    $heading('markets', 'Markets section', true),
                    ['name' => 'cards', 'label' => 'Work-type cards', 'type' => 'repeater', 'item_label' => 'title', 'fields' => [
                        ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                        ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                        ['name' => 'where', 'label' => 'Markets line', 'type' => 'text'],
                        ['name' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                    ]],
                    $cta,
                ]),
            ]],
        ],

        'contact-page' => [
            'label' => 'Contact', 'group' => 'Pages', 'icon' => 'mail', 'route' => 'contact',
            'description' => 'Text on the Contact page. Email, phone and LinkedIn are under Settings.',
            'parts' => [[
                'key' => 'content.contact',
                'fields' => array_merge($seo, [
                    $hero(),
                    ['name' => 'form_title', 'label' => 'Form title', 'type' => 'text'],
                    ['name' => 'location_suffix', 'label' => 'Text after the location', 'type' => 'text'],
                    ['name' => 'tips_title', 'label' => 'Tips box title', 'type' => 'text'],
                    ['name' => 'tips', 'label' => 'Tips', 'type' => 'list'],
                ]),
            ]],
        ],

        'shared' => [
            'label' => 'Header & footer', 'group' => 'Pages', 'icon' => 'repeat',
            'description' => 'Parts shared by every page: header, footer, the call-to-action band and before & after labels.',
            'parts' => [[
                'key' => 'content.site',
                'fields' => [
                    ['name' => 'brand_tagline', 'label' => 'Header: text under the name', 'type' => 'text'],
                    ['name' => 'nav_cta', 'label' => 'Header: button', 'type' => 'text'],
                    ['name' => 'cta', 'type' => 'group', 'label' => 'Call-to-action band (default)', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Heading', 'type' => 'rich'],
                        ['name' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                        ['name' => 'primary', 'label' => 'Main button', 'type' => 'text'],
                        ['name' => 'secondary', 'label' => 'LinkedIn button', 'type' => 'text'],
                    ]],
                    ['name' => 'footer', 'type' => 'group', 'label' => 'Footer', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'headline', 'label' => 'Headline (line breaks allowed)', 'type' => 'richarea'],
                        ['name' => 'button', 'label' => 'Button', 'type' => 'text'],
                        ['name' => 'note', 'label' => 'Note under the name', 'type' => 'textarea'],
                        ['name' => 'disclaimer', 'label' => 'Bottom disclaimer', 'type' => 'text'],
                    ]],
                    ['name' => 'case_studies', 'type' => 'group', 'label' => 'Before & after labels', 'fields' => [
                        ['name' => 'eyebrow', 'label' => 'Small label', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Default heading', 'type' => 'rich'],
                        ['name' => 'all_button', 'label' => '"All case studies" button', 'type' => 'text'],
                        ['name' => 'did_label', 'label' => '"What … did" label', 'type' => 'text'],
                        ['name' => 'improved_label', 'label' => '"What improved" label', 'type' => 'text'],
                        ['name' => 'demo_notice', 'label' => 'Demonstration notice', 'type' => 'textarea'],
                    ]],
                ],
            ]],
        ],

        /* ===================== CONTENT ===================== */

        'profile' => [
            'label' => 'Profile', 'group' => 'Content', 'icon' => 'users',
            'description' => 'The introduction and biography used on Home and About. Name and contact details are under Settings.',
            'parts' => [[
                'key' => 'portfolio.person',
                'fields' => [
                    ['name' => 'intro', 'label' => 'Short intro (home hero)', 'type' => 'textarea'],
                    ['name' => 'summary', 'label' => 'Biography paragraphs', 'type' => 'list', 'help' => 'One paragraph per line. Home shows the first two.'],
                ],
            ]],
        ],

        'stats' => [
            'label' => 'Key figures', 'group' => 'Content', 'icon' => 'gauge',
            'description' => 'The headline figures shown on Home and Results. Numbers count up when they scroll into view.',
            'parts' => [[
                'key' => 'portfolio.stats',
                'item_label' => 'label',
                'item' => [
                    ['name' => 'prefix', 'label' => 'Before the number', 'type' => 'text', 'help' => 'e.g. ~'],
                    ['name' => 'value', 'label' => 'Number', 'type' => 'number'],
                    ['name' => 'suffix', 'label' => 'After the number', 'type' => 'text', 'help' => 'e.g. + or x'],
                    ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
                    ['name' => 'context', 'label' => 'Context line', 'type' => 'text'],
                ],
            ]],
        ],

        'platforms' => [
            'label' => 'Meta & Google tabs', 'group' => 'Content', 'icon' => 'meta',
            'description' => 'The two platform tabs on Home and the platform sheets on Services.',
            'parts' => [[
                'key' => 'portfolio.platforms',
                'fields' => array_map(fn ($key) => ['name' => $key, 'type' => 'group', 'label' => $key === 'meta' ? 'Meta Ads' : 'Google Ads', 'fields' => [
                    ['name' => 'name', 'label' => 'Tab name', 'type' => 'text'],
                    ['name' => 'tagline', 'label' => 'Tagline', 'type' => 'text'],
                    ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea'],
                    ['name' => 'services', 'label' => 'Services', 'type' => 'repeater', 'item_label' => 'title', 'fields' => [
                        ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                        ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                        ['name' => 'text', 'label' => 'Text', 'type' => 'textarea'],
                    ]],
                    ['name' => 'metrics', 'label' => 'Figures under the summary', 'type' => 'repeater', 'item_label' => 'value', 'max' => 2, 'fields' => [
                        ['name' => 'value', 'label' => 'Figure', 'type' => 'text'],
                        ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
                    ]],
                ]], ['meta', 'google']),
            ]],
        ],

        'services' => [
            'label' => 'Services', 'group' => 'Content', 'icon' => 'layers',
            'description' => 'Service cards (Home and Services), the working process and other platforms offered.',
            'parts' => [
                ['key' => 'portfolio.services', 'label' => 'Service cards', 'item_label' => 'title', 'item' => [
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['name' => 'slug', 'label' => 'Anchor (auto from title)', 'type' => 'text', 'slug_from' => 'title'],
                    ['name' => 'text', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'points', 'label' => 'Bullet points', 'type' => 'list'],
                ]],
                ['key' => 'portfolio.process', 'label' => 'How I work (process steps)', 'item_label' => 'title', 'item' => [
                    ['name' => 'title', 'label' => 'Step', 'type' => 'text'],
                    ['name' => 'text', 'label' => 'Description', 'type' => 'textarea'],
                ]],
                ['key' => 'portfolio.also_runs', 'label' => 'Other platforms', 'strings' => true, 'help' => 'One per line.'],
            ],
        ],

        'experience' => [
            'label' => 'Experience', 'group' => 'Content', 'icon' => 'briefcase',
            'description' => 'The career timeline on the About page, newest first.',
            'parts' => [[
                'key' => 'portfolio.experience',
                'item_label' => 'role',
                'item_sub' => 'company',
                'item' => [
                    ['name' => 'role', 'label' => 'Job title', 'type' => 'text'],
                    ['name' => 'company', 'label' => 'Company', 'type' => 'text'],
                    ['name' => 'mode', 'label' => 'Type / location', 'type' => 'text', 'help' => 'e.g. Remote, On-site'],
                    ['name' => 'period', 'label' => 'Period', 'type' => 'text', 'help' => 'e.g. Aug 2026 – Present'],
                    ['name' => 'highlights', 'label' => 'Highlights', 'type' => 'list'],
                ],
            ]],
        ],

        'skills' => [
            'label' => 'Skills', 'group' => 'Content', 'icon' => 'spark',
            'description' => 'Skill groups shown on Home and About.',
            'parts' => [[
                'key' => 'portfolio.skills',
                'map' => ['key' => 'group', 'value' => 'items'],
                'item_label' => 'group',
                'item' => [
                    ['name' => 'group', 'label' => 'Group name', 'type' => 'text'],
                    ['name' => 'items', 'label' => 'Skills', 'type' => 'list', 'help' => 'Use "Name — detail" to add a smaller detail line.'],
                ],
            ]],
        ],

        'education' => [
            'label' => 'Education', 'group' => 'Content', 'icon' => 'graduation',
            'description' => 'Degrees and certifications on Home and About.',
            'parts' => [
                ['key' => 'portfolio.education', 'label' => 'Education', 'item_label' => 'title', 'item' => [
                    ['name' => 'title', 'label' => 'Qualification', 'type' => 'text'],
                    ['name' => 'school', 'label' => 'Institution', 'type' => 'text'],
                    ['name' => 'period', 'label' => 'Period', 'type' => 'text'],
                ]],
                ['key' => 'portfolio.certifications', 'label' => 'Certifications', 'item_label' => 'title', 'item' => [
                    ['name' => 'title', 'label' => 'Certification', 'type' => 'text'],
                    ['name' => 'issuer', 'label' => 'Issuer', 'type' => 'text'],
                    ['name' => 'year', 'label' => 'Year', 'type' => 'text'],
                ]],
            ],
        ],

        'projects' => [
            'label' => 'Projects', 'group' => 'Content', 'icon' => 'target',
            'description' => 'Portfolio projects. Featured projects also appear in the home page slider.',
            'parts' => [[
                'key' => 'portfolio.projects',
                'item_label' => 'title',
                'item_sub' => 'industry',
                'item' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['name' => 'slug', 'label' => 'Anchor (auto from title)', 'type' => 'text', 'slug_from' => 'title'],
                    ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => 'categories'],
                    ['name' => 'industry', 'label' => 'Industry', 'type' => 'text'],
                    ['name' => 'markets', 'label' => 'Markets', 'type' => 'list'],
                    ['name' => 'platforms', 'label' => 'Platforms', 'type' => 'checkboxes', 'options' => 'platforms'],
                    ['name' => 'objective', 'label' => 'Objective', 'type' => 'textarea'],
                    ['name' => 'work', 'label' => 'Work performed', 'type' => 'list'],
                    ['name' => 'outcome', 'label' => 'Documented outcome', 'type' => 'textarea', 'help' => 'Only results you can back up. Leave empty to show the campaign scope instead.'],
                    ['name' => 'period', 'label' => 'Period', 'type' => 'text'],
                    ['name' => 'featured', 'label' => 'Show in the home page slider', 'type' => 'toggle'],
                    ['name' => 'image', 'label' => 'Cover image (optional)', 'type' => 'image', 'help' => 'Replaces the illustrative chart at the top of the card.'],
                ],
            ]],
        ],

        'markets' => [
            'label' => 'Markets', 'group' => 'Content', 'icon' => 'map-pin',
            'description' => 'Countries shown on the globe, the market cards and the Results table.',
            'parts' => [[
                'key' => 'portfolio.markets',
                'item_label' => 'name',
                'item_sub' => 'focus',
                'item' => [
                    ['name' => 'name', 'label' => 'Country / city', 'type' => 'text'],
                    ['name' => 'code', 'label' => 'Short code', 'type' => 'text', 'help' => 'e.g. UK, AE'],
                    ['name' => 'region', 'label' => 'Region', 'type' => 'text'],
                    ['name' => 'lat', 'label' => 'Latitude (globe)', 'type' => 'number', 'step' => 'any'],
                    ['name' => 'lng', 'label' => 'Longitude (globe)', 'type' => 'number', 'step' => 'any'],
                    ['name' => 'platforms', 'label' => 'Platforms', 'type' => 'checkboxes', 'options' => 'platforms'],
                    ['name' => 'focus', 'label' => 'Focus', 'type' => 'text'],
                    ['name' => 'work', 'label' => 'Work performed', 'type' => 'textarea'],
                ],
            ]],
        ],
    ],

    /* Settings page (identity, contact details, branding). */
    'settings' => [
        ['key' => 'portfolio.person', 'label' => 'Identity', 'fields' => [
            ['name' => 'name', 'label' => 'Full name', 'type' => 'text', 'required' => true],
            ['name' => 'first_name', 'label' => 'First name', 'type' => 'text', 'help' => 'Used in headings like "What {first_name} did".'],
            ['name' => 'role', 'label' => 'Job title', 'type' => 'text'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'photo', 'label' => 'Profile photo', 'type' => 'image', 'help' => 'Replaces the monogram card on the home page. A square or portrait photo works best.'],
        ]],
        ['key' => 'content.site', 'label' => 'Branding', 'fields' => [
            ['name' => 'monogram', 'label' => 'Monogram (logo letters)', 'type' => 'text', 'help' => 'Two or three letters, e.g. AH.'],
            ['name' => 'share_image', 'label' => 'Share image', 'type' => 'image', 'help' => 'Shown when the site link is shared on WhatsApp, LinkedIn, Facebook or X. Use 1200 × 630 px.'],
        ]],
        ['key' => 'portfolio.person', 'label' => 'Contact details', 'fields' => [
            ['name' => 'email', 'label' => 'Public email', 'type' => 'text'],
            ['name' => 'show_email', 'label' => 'Show email on the site', 'type' => 'toggle'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'show_phone', 'label' => 'Show phone on the site', 'type' => 'toggle'],
            ['name' => 'linkedin', 'label' => 'LinkedIn URL', 'type' => 'text'],
            ['name' => 'linkedin_label', 'label' => 'LinkedIn link text', 'type' => 'text'],
            ['name' => 'notify_email', 'label' => 'Send contact-form messages to', 'type' => 'text'],
        ]],
        ['key' => 'content.site', 'label' => 'Search engines (SEO)', 'fields' => [
            ['name' => 'seo', 'type' => 'group', 'label' => 'Search engines', 'fields' => [
                ['name' => 'keywords', 'label' => 'Keywords', 'type' => 'list', 'help' => 'One per line. Used in the page keywords tag. Rankings mostly come from page titles, descriptions and content, so use the same phrases there.'],
                ['name' => 'alternate_names', 'label' => 'Other names for the site', 'type' => 'list', 'help' => 'One per line, e.g. "Hassan Ads Expert". Tells Google other names the site is known by.'],
                ['name' => 'google_verification', 'label' => 'Google Search Console code', 'type' => 'text', 'help' => 'From Search Console → Add property → HTML tag: paste only the content="…" value.'],
                ['name' => 'bing_verification', 'label' => 'Bing Webmaster code', 'type' => 'text', 'help' => 'From Bing Webmaster Tools → HTML meta tag: paste only the content="…" value.'],
            ]],
        ]],
    ],

];
