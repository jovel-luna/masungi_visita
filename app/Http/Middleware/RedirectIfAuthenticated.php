<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case 'web':
                    $destination = session('destination');
                    $route = $destination ? $destination->renderRequestVisitUrl() : route('web.destinations');
                break;
            
            case 'admin':
                    $route = '/admin';
                break;
        }

        if (Auth::guard($guard)->check()) {
            return redirect($route);
        }

        return $next($request);
    }

}
