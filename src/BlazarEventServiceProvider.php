<?php

namespace ctf0\Blazar;

use Event;
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

        if (config('blazar.clear_user_cache')) {
            Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
                $id = $event->user->id;

                return $this->preCacheStore()->tags($this->cacheName($id, true))->flush();
            });
        }
    }
}
