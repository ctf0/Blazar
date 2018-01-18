<?php

namespace ctf0\Blazar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BlazarFlush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blazar:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear Blazar package cache';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redis = Cache::getRedis();
        $keys  = $redis->keys('*blazar*');
        foreach ($keys as $key) {
            $redis->del($key);
        }

        $this->info('Blazar cache was cleared');
    }
}
