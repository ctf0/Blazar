<?php

namespace ctf0\Blazar\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PreRendEventQ
{
    use Dispatchable;

    public $url;
    public $token;
    public $userId;

    public function __construct($url, $token, $userId)
    {
        $this->url     = $url;
        $this->token   = $token;
        $this->userId  = $userId;
    }
}
