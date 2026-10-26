<?php

namespace App\Providers;

use App\Support\AutoSetup;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create the database tables on the first request after a deploy (AUTO_SETUP=true).
        AutoSetup::run();

        // Contact form: 5 messages per 10 minutes per visitor.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinutes(10, 5)
                ->by($request->ip())
                ->response(function (Request $request) {
                    $text = 'You\'ve sent several messages in a short time. Please wait a few minutes and try again.';

                    return $request->expectsJson()
                        ? response()->json(['ok' => false, 'message' => $text], 429)
                        : back()->withInput()->with('contact_error', $text);
                });
        });
    }
}
