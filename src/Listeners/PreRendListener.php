<?php

namespace ctf0\Blazar\Listeners;

use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Events\PreRendEvent;

class PreRendListener
{
    use Helpers;

    protected $phantom;
    protected $script;
    protected $options;

    public function __construct()
    {
        $this->phantom  = config('blazar.phantom_path');
        $this->script   = config('blazar.script_path');
        $this->options  = config('blazar.options');
        $this->debug    = config('blazar.debug');
    }

    public function handle(PreRendEvent $event)
    {
        $url         = $event->url;
        $cache_name  = $this->cacheName($url);

        if ($this->debug) {
            $this->debugLog("$url : Bot-Processed By Phantomjs");
        }

        $this->cacheResult(
            $cache_name,
            $this->runPhantom($url)
        );
    }

    /**
     * ops.
     *
     * @param [type] $cache_name [description]
     * @param [type] $output     [description]
     *
     * @return [type] [description]
     */
    protected function cacheResult($cache_name, $output)
    {
        return $this->preCacheStore()->rememberForever($cache_name, function () use ($output) {
            return $output;
        });
    }

    protected function runPhantom($url)
    {
        $phantom = $this->phantom;
        $script  = $this->script !== '' ? $this->script : __DIR__ . '/../exec-phantom.js';
        $options = $this->options;

        return shell_exec("$phantom $script $url $options");
    }
}
