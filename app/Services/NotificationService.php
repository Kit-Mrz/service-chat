<?php

namespace App\Services;

use App\Traits\Integration;

class NotificationService
{
    use Integration;

    protected $userId;

    protected $key = 'notice:task';

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getKey()
    {
        return $this->key;
    }

    // 标记该用户将会有一条消息推送
    public function mark()
    {
        return $this->getRedis()->sAdd($this->getKey(), $this->getUserId());
    }

    // 取消该用户的消息推送
    public function cancel()
    {
        return $this->getRedis()->sRem($this->getKey(), $this->getUserId());
    }
}
