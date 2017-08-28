<?php

namespace ctf0\Blazar\Traits;

trait Middleware
{
    /**
     * dont/clear cache.
     *
     * @param mixed $response
     *
     * @return [type] [description]
     */
    protected function dontPre($response)
    {
        return $response->headers->get('dont-pre-render');
    }

    /**
     * https://laravel-news.com/cache-query-params.
     *
     * @param [type] $request [description]
     *
     * @return [type] [description]
     */
    protected function formatUrlQuery($request)
    {
        $url = $request->url();

        if ($query = $request->query()) {
            ksort($query);
            $rebuild_query = http_build_query($query);

            return "{$url}?{$rebuild_query}";
        }

        return $url;
    }

    /**
     * check if we can pre-render.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     *
     * @return bool [description]
     */
    protected function isPreRendable($request, $response)
    {
        return !$request->ajax() &&
       !$request->pjax() &&
       !str_contains($request->header('User-Agent'), 'PhantomJS') &&
       $request->isMethodCacheable() &&
       $response->isSuccessful();
    }
}
