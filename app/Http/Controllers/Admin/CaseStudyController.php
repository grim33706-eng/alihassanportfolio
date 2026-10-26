<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CaseStudyRequest;
use App\Models\CaseStudy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CaseStudyController extends Controller
{
    public function index(): View
    {
        return view('admin.case-studies.index', [
            'caseStudies' => CaseStudy::ordered()->withCount([
                'images as before_count' => fn ($q) => $q->where('stage', 'before'),
                'images as after_count' => fn ($q) => $q->where('stage', 'after'),
            ])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.case-studies.form', ['caseStudy' => new CaseStudy([
            'is_demo' => false,
            'is_published' => false,
            'platforms' => [],
        ])]);
    }

    public function store(CaseStudyRequest $request): RedirectResponse
    {
        $caseStudy = DB::transaction(function () use ($request) {
            $caseStudy = CaseStudy::create($this->attributes($request));
            $this->storeUploads($request, $caseStudy);

            return $caseStudy;
        });

        return redirect()->route('admin.case-studies.edit', $caseStudy)->with('status', 'Case study created.');
    }

    public function edit(CaseStudy $caseStudy): View
    {
        return view('admin.case-studies.form', ['caseStudy' => $caseStudy->load('images')]);
    }

    public function update(CaseStudyRequest $request, CaseStudy $caseStudy): RedirectResponse
    {
        DB::transaction(function () use ($request, $caseStudy) {
            $caseStudy->update($this->attributes($request));

            // Existing screenshots: captions, order, removal.
            foreach ($request->validated('images', []) as $id => $data) {
                $image = $caseStudy->images()->find($id);
                if (! $image) {
                    continue;
                }
                if (! empty($data['delete'])) {
                    $image->delete();
                    continue;
                }
                $image->update([
                    'caption' => $data['caption'] ?? null,
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                ]);
            }

            $this->storeUploads($request, $caseStudy);
        });

        return redirect()->route('admin.case-studies.edit', $caseStudy)->with('status', 'Changes saved.');
    }

    public function destroy(CaseStudy $caseStudy): RedirectResponse
    {
        // Delete images one by one so their files are removed too.
        $caseStudy->images->each->delete();
        Storage::disk('public')->deleteDirectory("case-studies/{$caseStudy->id}");
        $caseStudy->delete();

        return redirect()->route('admin.case-studies.index')->with('status', 'Case study deleted.');
    }

    private function attributes(CaseStudyRequest $request): array
    {
        $data = $request->safe()->only([
            'title', 'slug', 'brand', 'industry', 'country', 'category', 'platforms', 'period',
            'strategy', 'improvements', 'metrics', 'is_demo', 'is_featured', 'is_published', 'sort_order',
        ]);
        $data['metrics'] = ! empty($data['metrics']) ? array_values($data['metrics']) : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function storeUploads(CaseStudyRequest $request, CaseStudy $caseStudy): void
    {
        foreach (['before', 'after'] as $stage) {
            $next = (int) $caseStudy->images()->where('stage', $stage)->max('sort_order') + 1;
            $caption = $request->input("new_captions.$stage");

            /** @var UploadedFile $file */
            foreach ($request->file("{$stage}_images", []) as $file) {
                [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

                $caseStudy->images()->create([
                    'stage' => $stage,
                    'path' => $file->store("case-studies/{$caseStudy->id}", 'public'),
                    'caption' => $caption,
                    'width' => $width,
                    'height' => $height,
                    'sort_order' => $next++,
                ]);
            }
        }
    }
}
