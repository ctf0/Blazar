<?php

namespace ctf0\Blazar\Listeners;

use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Events\PreRendEventQ;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreRendListenerQ implements ShouldQueue
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

    public function handle(PreRendEventQ $event)
    {
        $url         = $event->url;
        $token       = $event->token;
        $userId      = $event->userId;
        $cache_name  = $this->cacheName($url);

        if ($this->debug) {
            $this->debugLog("$url : Processed By Phantomjs");
        }

        $this->cacheResult(
            $cache_name,
            $userId,
            $this->replaceToken(
                $token,
                $this->runPhantom($url, $token, $userId)
            )
        );
    }

    /**
     * ops.
     *
     * @param [type] $url        [description]
     * @param mixed  $render
     * @param mixed  $cache_name
     * @param mixed  $userId
     * @param mixed  $output
     *
     * @return [type] [description]
     */
    protected function cacheResult($cache_name, $userId, $output)
    {
        if ($userId) {
            return $this->preCacheStore()->tags($this->cacheName($userId))->rememberForever($cache_name, function () use ($output) {
                return $output;
            });
        }

        return $this->preCacheStore()->rememberForever($cache_name, function () use ($output) {
            return $output;
        });
    }

    protected function replaceToken($token, $output)
    {
        $pattern = [
            '/<meta name="csrf-token"(.*?)>/' => "<meta name=\"csrf-token\" content=\"$token\">",
            '/<input name="_token"(.*?)>/'    => "<input name=\"_token\" type=\"hidden\" value=\"$token\">",
        ];

        return preg_replace(array_keys($pattern), array_values($pattern), $output);
    }

    protected function runPhantom($url, $token, $user_id)
    {
        $phantom = $this->phantom;
        $script  = $this->script !== '' ? $this->script : __DIR__ . '/../exec-phantom.js';
        $options = $this->options;

        return shell_exec("$phantom $script $url \"$token\" \"$user_id\" $options");
    }
}
