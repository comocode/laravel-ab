<?php

namespace ComoCode\LaravelAb\App\Http\Middleware;

use ComoCode\LaravelAb\App\Ab;
use Closure;

class LaravelAbMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $response = $next($request);

        $cookie = Ab::saveSession();

        return $response->withCookie(cookie()->forever('laravel_ab_user', $cookie));

    }
}
