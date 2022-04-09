<?php

namespace App\Services;

use App\Traits\Integration;

class ConnectionMapService
{
    use Integration;

    public function binding($fd, $userId)
    {
        // 以fd作为key存储 用户id
        $this->getRedis()->set($this->getFdKey($fd), $userId, 3600);

        // 以用户id作为key存储 fd
        $this->getRedis()->set($this->getUserIdKey($userId), $fd, 3600);
    }

    public function getFd($userId)
    {
        return $this->getRedis()->get($this->getUserIdKey($userId));
    }

    public function getUserId($fd)
    {
        return $this->getRedis()->get($this->getFdKey($fd));
    }

    public function unBinding($fd, $userId)
    {
        $this->getRedis()->del($this->getFdKey($fd));

        $this->getRedis()->del($this->getUserIdKey($userId));
    }

    protected function getFdKey($fd)
    {
        return gethostname() . ':fd:' . $fd;
    }

    protected function getUserIdKey($userId)
    {
        return 'uid:' . $userId;
    }
}
