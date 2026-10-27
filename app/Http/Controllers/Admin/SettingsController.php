<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminPassword;
use App\Support\ContentEditor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $parts = config('admin.settings');

        return view('admin.settings', [
            'parts' => $parts,
            'values' => array_map(fn ($part) => ContentEditor::formValue($part), $parts),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate(
            ['parts.0.name' => ['required', 'string', 'max:120']],
            ['parts.0.name.required' => 'The name cannot be empty.'],
        );
        ContentEditor::saveParts(config('admin.settings'), $request->input('parts', []), $request->file('files', []));

        return redirect()->route('admin.settings')->with('status', 'Settings saved. The changes are live.');
    }

    public function password(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:10', 'max:200', 'confirmed'],
        ], [
            'password.min' => 'Use at least 10 characters for the new password.',
            'password.confirmed' => 'The two new passwords do not match.',
        ]);

        if (! AdminPassword::check((string) $request->input('current_password'))) {
            return back()->withErrors(['current_password' => 'The current password is not correct.']);
        }

        AdminPassword::set((string) $request->input('password'));

        return redirect()->route('admin.settings')->with('status', 'Password changed. Use the new password next time you log in.');
    }
}
