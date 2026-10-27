<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        abort_unless(AdminPassword::configured(), 404);

        return $request->session()->get('is_admin')
            ? redirect()->route('admin.dashboard')
            : view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        abort_unless(AdminPassword::configured(), 404);

        $request->validate(['password' => ['required', 'string', 'max:200']]);

        if (! AdminPassword::check((string) $request->input('password'))) {
            return back()->withErrors(['password' => 'That password is not correct.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been logged out.');
    }
}
