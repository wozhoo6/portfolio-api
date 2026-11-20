<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\VisitorHasher;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('impressions', function (Request $request) {
            $hash = app(VisitorHasher::class)->make($request);

            return [
                Limit::perMinute(2)->by($hash),
                Limit::perDay(10)->by($hash)
            ];
        });
    }
}
