<?php

namespace ctf0\Blazar\Listeners;

use ctf0\Blazar\Traits\Helpers;
use ctf0\Blazar\Traits\Listeners;
use ctf0\Blazar\Events\PreRendEventQ;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreRendListenerQ implements ShouldQueue
{
    use Helpers, Listeners;

    public function handle(PreRendEventQ $event)
    {
        $url         = $event->url;
        $token       = $event->token;
        $userId      = $event->userId;
        $cache_name  = $this->cacheName($url);

        $this->cacheResult(
            $url,
            $cache_name,
            $userId,
            $this->replaceToken(
                $token,
                $this->runChrome($this->prepareUrlForShell($url), $token, $userId)
            )
        );
    }

    /**
     * cache result.
     *
     * @param [type] $url        [description]
     * @param [type] $cache_name [description]
     * @param [type] $userId     [description]
     * @param [type] $output     [description]
     *
     * @return [type] [description]
     */
    protected function cacheResult($url, $cache_name, $userId, $output)
    {
        // couldnt open url
        if (str_contains($output, 'Something Went Wrong')) {
            $this->debugLog("$url : $output");

            return;
        }

        // log result
        if ($this->debug) {
            $this->debugLog("$url : Processed By Puppeteer");
        }

        // save to cache
        if ($userId) {
            return $this->preCacheStore()
                ->tags($this->cacheName($userId, true))
                ->rememberForever($cache_name, function () use ($output) {
                    return $output;
                });
        }

        return $this->preCacheStore()
            ->rememberForever($cache_name, function () use ($output) {
                return $output;
            });
    }

    /**
     * replace "Puppeteer" csrf_token with "current user".
     *
     * @param [type] $token  [description]
     * @param [type] $output [description]
     *
     * @return [type] [description]
     */
    protected function replaceToken($token, $output)
    {
        $pattern = [
            '/<meta name="csrf-token"(.*?)>/' => "<meta name=\"csrf-token\" content=\"$token\">",
            '/<input name="_token"(.*?)>/'    => "<input name=\"_token\" type=\"hidden\" value=\"$token\">",
        ];

        return preg_replace(array_keys($pattern), array_values($pattern), $output);
    }
}
