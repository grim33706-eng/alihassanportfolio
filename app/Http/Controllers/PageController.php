<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\Http\Response;
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
        $routes = ['home', 'about', 'services', 'portfolio', 'results', 'international', 'contact'];

        return response()
            ->view('sitemap', ['urls' => array_map(fn ($name) => route($name), $routes)])
            ->header('Content-Type', 'application/xml');
    }
}
