<?php

namespace App\Services;

use App\Traits\Integration;

class OnlineService
{
    use Integration;

    protected $key = 'online';

    protected $userId;

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    protected function getKey()
    {
        return $this->key;
    }

    // 上线
    public function up()
    {
        $this->getRedis()->sAdd($this->getKey(), $this->userId);

        return $this;
    }

    // 下线
    public function down()
    {
        $this->getRedis()->sRem($this->getKey(), $this->userId);

        return $this;
    }

    // true:在线, false:离线
    public function checkOnline() : bool
    {
        return (bool) $this->getRedis()->sIsMember($this->getKey(), $this->userId);
    }
}
