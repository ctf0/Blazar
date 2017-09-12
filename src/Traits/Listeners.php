<?php

namespace ctf0\Blazar\Traits;

trait Listeners
{
    protected $phantom;
    protected $script;
    protected $options;
    protected $debug;

    public function __construct()
    {
        $this->phantom  = config('blazar.phantom_path');
        $this->script   = config('blazar.script_path');
        $this->options  = config('blazar.options');
        $this->debug    = config('blazar.debug');
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
     * process with phantom.
     *
     * @param [type] $url     [description]
     * @param [type] $token   [description]
     * @param [type] $user_id [description]
     *
     * @return [type] [description]
     */
    protected function runPhantom($url, $token = null, $user_id = null)
    {
        $phantom = $this->phantom;
        $script  = $this->script !== '' ? $this->script : __DIR__ . '/../exec-phantom.js';
        $options = $this->options;

        // $this->debugLog("$phantom $script $url \"$token\" \"$user_id\" $options");

        return $token
        ? shell_exec("$phantom $script $url \"$token\" \"$user_id\" $options")
        : shell_exec("$phantom $script $url $options");
    }
}
