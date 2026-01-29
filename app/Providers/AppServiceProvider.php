<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set locale from session or default to Bangla
        $locale = session('locale', config('app.locale', 'bn'));
        if (!session()->has('locale')) {
            session(['locale' => $locale]);
        }
        app()->setLocale($locale);

        if ($this->app->runningInConsole()) {
            return;
        }

        $request = request();

        $forwardedProto = $request->server('HTTP_X_FORWARDED_PROTO');
        $host = $request->header('X-Forwarded-Host', $request->getHost());

        // GitHub Codespaces forwards HTTPS traffic over HTTP without this header sometimes
        if (!$forwardedProto && str_ends_with($host, '.app.github.dev')) {
            $forwardedProto = 'https';
        }

        if ($forwardedProto) {
            URL::forceScheme($forwardedProto);
        }

        URL::forceRootUrl(sprintf('%s://%s', $forwardedProto ?: $request->getScheme(), $host));
    }
}
