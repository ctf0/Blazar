<?php

namespace ctf0\Blazar;

use ctf0\Blazar\Traits\Helpers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class BlazarEventServiceProvider extends EventServiceProvider
{
    use Helpers;

    protected $listen = [
        'ctf0\Blazar\Events\PreRendEvent'  => ['ctf0\Blazar\Listeners\PreRendListener'],
        'ctf0\Blazar\Events\PreRendEventQ' => ['ctf0\Blazar\Listeners\PreRendListenerQ'],
    ];

    public function boot()
    {
        parent::boot();

        $this->clearPreRenderCache();
    }
}
