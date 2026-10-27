<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ContentEditor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function edit(string $section): View
    {
        $config = $this->section($section);

        return view('admin.content.edit', [
            'id' => $section,
            'section' => $config,
            'values' => array_map(fn ($part) => ContentEditor::formValue($part), $config['parts']),
            'edited' => SiteSetting::whereIn('key', array_column($config['parts'], 'key'))->max('updated_at'),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $config = $this->section($section);
        ContentEditor::saveParts($config['parts'], $request->input('parts', []), $request->file('files', []));

        return redirect()->route('admin.content.edit', $section)->with('status', $config['label'].' saved. The changes are live.');
    }

    public function reset(string $section): RedirectResponse
    {
        $config = $this->section($section);
        ContentEditor::resetParts($config['parts']);

        return redirect()->route('admin.content.edit', $section)->with('status', $config['label'].' reset to the original content.');
    }

    private function section(string $section): array
    {
        $config = config("admin.sections.$section");
        abort_unless(is_array($config), 404);

        return $config;
    }
}
