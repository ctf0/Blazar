<?php

namespace ctf0\Blazar\Middleware;

use Auth;
use Closure;
use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Events\PreRendEvent;
use ctf0\Blazar\Events\PreRendEventQ;

class Blazar
{
    use Helpers;

    public function handle($request, Closure $next)
    {
        // persist user login
        if ($id = $request->header('user-id')) {
            Auth::loginUsingId($id);
        }

        $response = $next($request);

        // dont cache request
        if ($this->dontCache($response)) {
            return $response;
        }

        $url         = $request->url();
        $query       = $request->getQueryString();
        $cache_store = $this->preCacheStore();
        $cache_name  = $this->cacheName($url);

        // for bots only
        if (config('blazar.bots_only') && $query == '_escaped_fragment') {
            return $this->preBots($request, $response, $url, $cache_store, $cache_name);
        }

        // for all pages
        if (!$query) {
            return $this->preAll($request, $response, $url, $cache_store, $cache_name);
        }

        return $response;
    }

    /**
     * All.
     *
     * @param [type] $request     [description]
     * @param [type] $response    [description]
     * @param [type] $url         [description]
     * @param [type] $cache_store [description]
     * @param [type] $cache_name  [description]
     *
     * @return [type] [description]
     */
    protected function preAll($request, $response, $url, $cache_store, $cache_name)
    {
        $userId = auth()->check() ? $request->user()->id : null;

        if (is_null($userId)) {
            if ($cache_store->has($cache_name)) {
                $response->setContent($cache_store->get($cache_name));
            } else {
                $this->preRenderedResponse($request, $response);
            }
        } else {
            $tags = $cache_store->tags($this->cacheName($userId));

            if ($tags->has($cache_name)) {
                $response->setContent($tags->get($cache_name));
            } else {
                $this->preRenderedResponse($request, $response, $userId);
            }
        }

        return $response;
    }

    /**
     * Bots.
     *
     * @param [type] $request     [description]
     * @param [type] $response    [description]
     * @param [type] $url         [description]
     * @param [type] $cache_store [description]
     * @param [type] $cache_name  [description]
     *
     * @return [type] [description]
     */
    protected function preBots($request, $response, $url, $cache_store, $cache_name)
    {
        if ($cache_store->has($cache_name)) {
            $response->setContent($cache_store->get($cache_name));
        } else {
            $this->preRenderedResponse($request, $response, null, true);
            $response->setContent($cache_store->get($cache_name));
        }

        return $response;
    }

    /**
     * main op.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $userId   [description]
     * @param [type] $bots     [description]
     *
     * @return [type] [description]
     */
    protected function preRenderedResponse($request, $response, $userId = null, $bots = null)
    {
        if (
            !$request->ajax() && !$request->pjax() &&
            !str_contains($request->header('User-Agent'), 'PhantomJS') &&
            $request->isMethodCacheable() &&
            $response->isSuccessful()
        ) {
            $url = $request->url();

            $bots ? event(new PreRendEvent($url)) : event(new PreRendEventQ($url, $request->session()->token(), $userId));
        }
    }
}
