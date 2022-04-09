<?php

namespace App\Services;

use App\Traits\Integration;

class ContentQueueService
{
    use Integration;

    const CONTENT_QUEUE = 'content:queue';

    public function getKey()
    {
        return self::CONTENT_QUEUE;
    }

    public function push(array $msg)
    {
        return $this->getRedis()->lPush($this->getKey(), json_encode($msg));
    }

    public function pop()
    {
        $content = $this->getRedis()->rPop($this->getKey());

        return $content ? json_decode($content, true) : [];
    }

    public function range(int $start = 0, int $stop = 1000)
    {
        return $this->getRedis()->lRange($this->getKey(), $start, $stop);
    }
}
