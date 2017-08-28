<?php

namespace ctf0\Blazar\Traits;

trait Helpers
{
    protected function preCacheStore()
    {
        return app('cache');
    }

    protected function cachePrefix()
    {
        return 'blazar-';
    }

    protected function cacheName($item, $tag = null)
    {
        return $tag ? $this->cachePrefix() . $item : $this->cachePrefix() . md5($item);
    }
}
