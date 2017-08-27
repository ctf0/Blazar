<?php

namespace ctf0\Blazar\Middleware;

use Closure;

class DontHttpCache
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

        return $response->withHeaders([
            'Cache-Control'=> 'nocache',
            'dont-cache'   => true,
            'Expires'      => 'Fri, 01 Jan 1990 00:00:00 GMT',
        ]);
    }
}
