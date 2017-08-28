<?php

namespace ctf0\Blazar\Traits;

use Log;
use Event;

trait Helpers
{
    /**
     * helpers.
     *
     * @param [type] $url [description]
     *
     * @return [type] [description]
     */
    protected function debugLog($url)
    {
        Log::debug($url);
    }

    /**
     * cache.
     *
     * @return [type] [description]
     */
    protected function preCacheStore()
    {
        return app('cache');
    }

    protected function cachePrefix()
    {
        return 'blazar-';
    }

    protected function cacheName($url)
    {
        return $this->cachePrefix() . $url;
    }

    /**
     * dont/clear cache.
     *
     * @param mixed $response
     *
     * @return [type] [description]
     */
    protected function dontCache($response)
    {
        return $response->headers->get('dont-cache');
    }

    protected function clearPreRenderCache()
    {
        Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
            $id = $event->user->id;

            return $this->preCacheStore()->tags($this->cacheName($id))->flush();
        });
    }
}
