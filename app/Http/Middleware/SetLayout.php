<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SetLayout
{
    public function handle($request, Closure $next)
    {
        // Set layout based on authentication status
        $layout = Auth::check() ? 'layouts.sidebar_admin' : 'layouts.sidebar_user';
        view()->share('layout', $layout);
        return $next($request);
    }
}
