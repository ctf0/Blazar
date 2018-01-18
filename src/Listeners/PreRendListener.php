<?php

namespace ctf0\Blazar\Listeners;

use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Traits\Listeners;
use ctf0\Blazar\Events\PreRendEvent;

class PreRendListener
{
    use Helpers, Listeners;

    public function handle(PreRendEvent $event)
    {
        $url         = $event->url;
        $cache_name  = $this->cacheName($url);

        $this->cacheResult(
            $url,
            $cache_name,
            $this->runChrome($this->prepareUrlForShell($url))
        );
    }

    /**
     * cache result.
     *
     * @param [type] $url        [description]
     * @param [type] $cache_name [description]
     * @param [type] $output     [description]
     *
     * @return [type] [description]
     */
    protected function cacheResult($url, $cache_name, $output)
    {
        // couldnt open url
        if (str_contains($output, 'Something Went Wrong')) {
            $this->debugLog("Bot-$url : $output");

            return;
        }

        // log result
        if ($this->debug) {
            $this->debugLog("Bot-$url : Processed By Puppeteer");
        }

        // save to cache
        return $this->preCacheStore()->rememberForever($cache_name, function () use ($output) {
            return $output;
        });
    }
}
