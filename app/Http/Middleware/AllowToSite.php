<?php

namespace App\Http\Middleware;

use Closure;

class AllowToSite
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
        if ($request->age < 18) {
            return redirect()->route('site.denied', ['age'=>$request->age]);
        }

        return $next($request);
    }
}
