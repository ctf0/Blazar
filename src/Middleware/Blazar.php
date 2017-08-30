<?php

namespace ctf0\Blazar\Middleware;

use Auth;
use Closure;
use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Traits\Middleware;
use ctf0\Blazar\Events\PreRendEvent;
use ctf0\Blazar\Events\PreRendEventQ;

class Blazar
{
    use Helpers, Middleware;

    public function handle($request, Closure $next)
    {
        // persist user login
        if ($id = $request->header('user-id')) {
            Auth::loginUsingId($id);
        }

        $response = $next($request);

        if ($this->dontPre($response)) {
            return $response;
        }

        if ($this->isPreRendable($request, $response)) {
            $url         = $this->formatUrlQuery($request);
            $cache_store = $this->preCacheStore();
            $cache_name  = $this->cacheName($url);

            if (config('blazar.bots_only') && str_contains($url, '_escaped_fragment')) {
                $this->preBots($response, $url, $cache_store, $cache_name);
            }

            $this->preAll($response, $url, $cache_store, $cache_name);
        }

        return $response;
    }

    /**
     * Bots.
     *
     * @param [type] $response    [description]
     * @param [type] $url         [description]
     * @param [type] $cache_store [description]
     * @param [type] $cache_name  [description]
     *
     * @return [type] [description]
     */
    protected function preBots($response, $url, $cache_store, $cache_name)
    {
        if ($cache_store->has($cache_name)) {
            $response->setContent($cache_store->get($cache_name));
        } else {
            $this->preRenderedResponse($url, null, true);
            $response->setContent($cache_store->get($cache_name));
        }
    }

    /**
     * All.
     *
     * @param [type] $response    [description]
     * @param [type] $url         [description]
     * @param [type] $cache_store [description]
     * @param [type] $cache_name  [description]
     *
     * @return [type] [description]
     */
    protected function preAll($response, $url, $cache_store, $cache_name)
    {
        $userId = auth()->check() ? auth()->user()->id : null;

        if (is_null($userId)) {
            if ($cache_store->has($cache_name)) {
                $response->setContent($cache_store->get($cache_name));
            } else {
                $this->preRenderedResponse($url);
            }
        } else {
            $tags = $cache_store->tags($this->cacheName($userId, true));

            if ($tags->has($cache_name)) {
                $response->setContent($tags->get($cache_name));
            } else {
                $this->preRenderedResponse($url, $userId);
            }
        }
    }

    /**
     * main op.
     *
     * @param [type] $url    [description]
     * @param [type] $userId [description]
     * @param [type] $bots   [description]
     *
     * @return [type] [description]
     */
    protected function preRenderedResponse($url, $userId = null, $bots = null)
    {
        $bots ? event(new PreRendEvent($url)) : event(new PreRendEventQ($url, csrf_token(), $userId));
    }
}
