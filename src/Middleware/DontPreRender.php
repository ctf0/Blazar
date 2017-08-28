<?php

namespace ctf0\Blazar\Middleware;

use Closure;

class DontPreRender
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return $response->withHeaders(['dont-pre-render' => true]);
    }
}
