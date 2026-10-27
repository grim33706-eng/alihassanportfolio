<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $edited = SiteSetting::where('key', 'not like', 'admin.%')->orderByDesc('updated_at')->get();

        return view('admin.dashboard', [
            'stats' => [
                'messages' => ContactMessage::count(),
                'unread' => ContactMessage::whereNull('read_at')->count(),
                'caseStudies' => CaseStudy::count(),
                'caseStudiesDemo' => CaseStudy::where('is_demo', true)->count(),
                'projects' => count(config('portfolio.projects')),
                'markets' => count(config('portfolio.markets')),
                'edited' => $edited->count(),
            ],
            'lastEdit' => $edited->first()?->updated_at,
            'recentMessages' => ContactMessage::latest()->limit(5)->get(),
            'sections' => config('admin.sections'),
        ]);
    }
}
