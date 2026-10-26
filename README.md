# Ali Hassan — Performance Marketing Portfolio

Laravel 12 portfolio site: Home, About, Services, Portfolio, Results, International Experience and Contact.

## Setup

```bash
composer install
cp .env.example .env && php artisan key:generate
# set DB_* (MySQL) and PORTFOLIO_* values in .env
php artisan migrate
php artisan serve
```

No Node build step: Bootstrap 5.3, GSAP 3.12 (+ ScrollTrigger, Flip) and Three.js 0.160 load from jsDelivr.

## Editing content

All site content lives in **`config/portfolio.php`** — profile, stats, services, the Meta/Google tabs,
experience, projects, markets, education and certifications. Templates read from it, so adding a
project or a certification is a config edit only. Only add facts that are supported by a source document.

Contact visibility is controlled in `.env`:

| Key | Purpose |
|---|---|
| `PORTFOLIO_CONTACT_EMAIL` / `PORTFOLIO_SHOW_EMAIL` | Public email on Contact page & footer |
| `PORTFOLIO_SHOW_PHONE` | Phone number (hidden by default until approved) |
| `PORTFOLIO_NOTIFY_EMAIL` | Where contact-form notifications are sent |

Set `MAIL_MAILER` (default `log`) to SMTP to actually deliver notifications. Messages are always stored in
the `contact_messages` table.

## Structure

- `app/Http/Controllers/PageController.php`, `ContactController.php`
- `app/Http/Requests/ContactRequest.php` — validation, sanitising, honeypot + timing check
- Rate limit `contact` (5 per 10 min per IP) in `AppServiceProvider`
- `resources/views/components/` — nav, footer, page-hero, button, section-heading, stat, service-card, project-card, platform-badges, contact-form, cta, icon
- `public/assets/css/app.css` — design system (tokens in `:root`)
- `public/assets/js/app.js` — shared motion (reveals, counters, tilt, tooltips, page transitions, teardown)
- `public/assets/js/pages/` — home tabs & slider, portfolio filters, contact form
- `public/assets/js/three/` — home hero scene, international globe (WebGL-checked, paused off-screen, reduced-motion aware)

## Before & after case studies

Case studies live in MySQL (`case_studies`, `case_study_images`) and are managed at **`/admin/login`**
using the password in `ADMIN_PASSWORD` (`.env`; leave empty to disable the admin).

1. **New case study** → fill in title, industry, market, platforms and what Ali did.
2. Upload **before** and **after** screenshots (JPG/PNG/WebP, max 8 MB). The lowest "order" image of each
   stage is used in the comparison slider, so matching views and sizes compare best.
3. Add **verified metrics** only if the screenshots show them (e.g. CPL `12.5 → 7.25`, unit `$`, "Lower is better").
4. Untick **Demonstration** once real data is in, keep **Published** on, tick **Featured** to show it on Home/Portfolio.

The three seeded entries (`php artisan db:seed`) are demonstrations: real project descriptions with placeholder
graphics and no figures. Edit them with real screenshots or delete them.
Screenshots are stored in `storage/app/public/case-studies/{id}`; run `php artisan storage:link` on a new install.
