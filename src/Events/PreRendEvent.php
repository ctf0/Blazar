<?php

namespace ctf0\Blazar\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PreRendEvent
{
    use Dispatchable;

    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
}
