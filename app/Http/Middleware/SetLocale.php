<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set default locale to Bangla if not set in session
        if (!session()->has('locale')) {
            session(['locale' => 'bn']);
        }
        
        $locale = session('locale', 'bn');
        app()->setLocale($locale);

        return $next($request);
    }
}
