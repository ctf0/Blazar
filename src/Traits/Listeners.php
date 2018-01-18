<?php

namespace ctf0\Blazar\Traits;

trait Listeners
{
    protected $puppet;
    protected $script;
    protected $debug;

    public function __construct()
    {
        $this->puppet = config('blazar.puppeteer_path');
        $this->script = config('blazar.script_path');
        $this->debug  = config('blazar.debug');
    }

    /**
     * helpers.
     *
     * @param [type] $url [description]
     *
     * @return [type] [description]
     */
    protected function debugLog($url)
    {
        logger($url);
    }

    /**
     * escape special chars for shell.
     *
     * @param [type] $url [description]
     *
     * @return [type] [description]
     */
    protected function prepareUrlForShell($url)
    {
        $pattern = [
            '/\?/' => "\?",
            '/\=/' => "\=",
            '/\&/' => "\&",
        ];

        return preg_replace(array_keys($pattern), array_values($pattern), $url);
    }

    /**
     * process with puppeteer.
     *
     * @param [type] $url     [description]
     * @param [type] $token   [description]
     * @param [type] $user_id [description]
     *
     * @return [type] [description]
     */
    protected function runChrome($url, $token = null, $user_id = null)
    {
        $puppet = $this->puppet;
        $script = $this->script ?: dirname(__DIR__) . '/exec-chrome.js';
        $cmnd   = "node '$script' '$puppet' '$url'";

        // $this->debugLog("node '$script' '$puppet' '$url' '$token' '$user_id'");

        return $token
            ? shell_exec($cmnd . " '$token' '$user_id'")
            : shell_exec($cmnd);
    }
}
