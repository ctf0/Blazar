<?php

return [
    /*
     * puppeteer path
     *
     * npm -g install puppeteer
     * which puppeteer
     */
    'puppeteer_path' => '/usr/local/lib/node_modules/puppeteer',

    /*
     * puppeteer script path
     * leave it empty to the use the one from the package
     */
    'script_path' => '',

    /*
     * prerender the page only if the url is being visited from a bot/crawler
     * https://github.com/JayBizzle/Laravel-Crawler-Detect
     */
    'bots_only' => false,

    /*
     * log the url when its processed by puppeteer
     */
    'debug' => false,

    /*
     *  clear user cache on logout
     */
    'clear_user_cache' => false,
];
