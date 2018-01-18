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
            $str = str_contains($query, '_escaped_fragment')
                ? preg_replace('/(\?|\&)_escaped_fragment_?/', '', $query)
                : $query;

            ksort($str);
            $rebuild_query = http_build_query($str);

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
        return !str_contains($request->header('User-Agent'), 'HeadlessChrome') &&
            !$request->ajax() &&
            !$request->pjax() &&
            $request->isMethodCacheable() &&
            $response->isSuccessful();
    }
}
