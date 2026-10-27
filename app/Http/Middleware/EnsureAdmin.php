<?php

namespace App\Http\Middleware;

use App\Support\AdminPassword;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the admin. Access is a single password (ADMIN_PASSWORD, or one set
 * in Settings); with neither configured the admin does not exist.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(AdminPassword::configured(), 404);

        if (! $request->session()->get('is_admin')) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
