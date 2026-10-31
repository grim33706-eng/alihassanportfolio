<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $portfolio = config('portfolio');

        return view('home', [
            'person' => $portfolio['person'],
            'stats' => $portfolio['stats'],
            'platforms' => $portfolio['platforms'],
            'alsoRuns' => $portfolio['also_runs'],
            'services' => $portfolio['services'],
            'skills' => $portfolio['skills'],
            'featuredProjects' => collect($portfolio['projects'])->where('featured', true)->values(),
            'portfolioTotals' => [
                'projects' => count($portfolio['projects']),
                'markets' => count($portfolio['markets']),
                'platforms' => count($portfolio['platform_labels']),
            ],
            'markets' => $portfolio['markets'],
            'globeMarkets' => $this->globeMarkets($portfolio['markets']),
            'education' => $portfolio['education'],
            'certifications' => $portfolio['certifications'],
            'caseStudies' => $this->caseStudies(featuredOnly: true, limit: 2),
        ]);
    }

    public function about(): View
    {
        $portfolio = config('portfolio');

        return view('about', [
            'person' => $portfolio['person'],
            'experience' => $portfolio['experience'],
            'skills' => $portfolio['skills'],
            'process' => $portfolio['process'],
            'education' => $portfolio['education'],
            'certifications' => $portfolio['certifications'],
        ]);
    }

    public function services(): View
    {
        $portfolio = config('portfolio');

        return view('services', [
            'services' => $portfolio['services'],
            'platforms' => $portfolio['platforms'],
            'process' => $portfolio['process'],
            'alsoRuns' => $portfolio['also_runs'],
        ]);
    }

    public function portfolio(): View
    {
        $portfolio = config('portfolio');
        $projects = collect($portfolio['projects']);

        return view('portfolio', [
            'projects' => $projects,
            'categories' => $portfolio['categories'],
            'platformLabels' => $portfolio['platform_labels'],
            // Only offer filters that match at least one project.
            'platformFilters' => collect($portfolio['platform_labels'])
                ->filter(fn ($label, $key) => $projects->contains(fn ($p) => in_array($key, $p['platforms'], true))),
            'caseStudies' => $this->caseStudies(featuredOnly: true, limit: 3),
        ]);
    }

    public function results(): View
    {
        $portfolio = config('portfolio');

        return view('results', [
            'stats' => $portfolio['stats'],
            'experience' => $portfolio['experience'],
            'markets' => $portfolio['markets'],
            'caseStudies' => $this->caseStudies(),
        ]);
    }

    public function international(): View
    {
        $portfolio = config('portfolio');

        return view('international', [
            'markets' => $portfolio['markets'],
            'globeMarkets' => $this->globeMarkets($portfolio['markets']),
            'platformLabels' => $portfolio['platform_labels'],
        ]);
    }

    /** Just what the 3D globe needs for each market. */
    private function globeMarkets(array $markets)
    {
        return collect($markets)
            ->map(fn ($m) => collect($m)->only(['name', 'code', 'lat', 'lng']))
            ->values();
    }

    /**
     * Published before-and-after case studies. If the database is unavailable the
     * section is simply left out rather than taking the whole page down.
     */
    private function caseStudies(bool $featuredOnly = false, ?int $limit = null): Collection
    {
        return rescue(fn () => CaseStudy::query()
            ->published()
            ->when($featuredOnly, fn ($q) => $q->where('is_featured', true))
            ->ordered()
            ->with('images')
            ->when($limit, fn ($q) => $q->limit($limit))
            ->get(), collect(), report: true);
    }

    public function sitemap(): Response
    {
        // Priority by how central the page is to "who Ali is and what he offers".
        $routes = ['home' => '1.0', 'about' => '0.9', 'services' => '0.9', 'results' => '0.8', 'portfolio' => '0.8', 'international' => '0.7', 'contact' => '0.7'];

        // Last change: the latest admin edit, else when the site's content was last deployed.
        $edited = rescue(fn () => SiteSetting::max('updated_at'), null, false);
        $lastmod = ($edited ? Carbon::parse($edited) : Carbon::createFromTimestamp(filemtime(config_path('content.php'))))->toAtomString();

        $urls = collect($routes)->map(fn ($priority, $name) => [
            'loc' => $name === 'home' ? url('/').'/' : route($name),
            'priority' => $priority,
            'lastmod' => $lastmod,
        ])->values();

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
