<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the small content admin. Access is a single password set in
 * ADMIN_PASSWORD; with no password configured the admin does not exist.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(filled(config('portfolio.admin_password')), 404);

        if (! $request->session()->get('is_admin')) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
