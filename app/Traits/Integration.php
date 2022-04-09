<?php

namespace App\Traits;

use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;

trait Integration
{
    public function getRedis() : Redis
    {
        return ApplicationContext::getContainer()->get(Redis::class);
    }

}
