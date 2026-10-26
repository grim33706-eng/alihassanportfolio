<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        abort_unless(filled(config('portfolio.admin_password')), 404);

        return $request->session()->get('is_admin')
            ? redirect()->route('admin.case-studies.index')
            : view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $expected = (string) config('portfolio.admin_password');
        abort_unless(filled($expected), 404);

        $request->validate(['password' => ['required', 'string', 'max:200']]);

        if (! hash_equals($expected, (string) $request->input('password'))) {
            return back()->withErrors(['password' => 'That password is not correct.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->intended(route('admin.case-studies.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
