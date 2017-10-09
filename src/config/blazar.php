<?php

return [
    /*
     * phantomjs bin path
     */
    'phantom_path' => '',

    /*
     * phantomjs script path
     * leave it empty to the use the one from the package
     */
    'script_path' => '',

    /*
     * phantomjs options
     */
    'options' => '--ignore-ssl-errors=true --ssl-protocol=any --disk-cache=false --debug=true 2>&1',

    /*
     * prerender the page only if the url is being visited from a bot/crawler
     * https://github.com/JayBizzle/Laravel-Crawler-Detect
     */
    'bots_only' => false,

    /*
     * log the url when its processed by phantomjs
     */
    'debug' => true,

    /*
     *  clear user cache on logout
     */
    'clear_user_cache' => true,
];
