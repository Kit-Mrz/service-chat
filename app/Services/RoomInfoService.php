<?php

namespace App\Services;

use App\Traits\Integration;

class RoomInfoService
{
    use Integration;

    protected $roomId;
    protected $roomKey;

    //const CACHE_TIME = 3600 * 24;

    public function setRoomId($room_id)
    {
        $this->roomId = $room_id;

        $this->roomKey = 'roomInfo:' . $room_id;

        return $this;
    }

    public function getRoomKey()
    {
        return $this->roomKey;
    }

    public function getRoomId()
    {
        return $this->roomId;
    }

    public function remember(array $roomInfo)
    {
        $this->getRedis()->hMSet($this->getRoomKey(), $roomInfo);
        //$this->getRedis()->expire($this->getRoomKey(), self::CACHE_TIME);

        return $this;
    }

    public function forget()
    {
        return $this->getRedis()->del($this->getRoomKey());
    }

    public function roomInfo()
    {
        $keys = $this->getRedis()->hKeys($this->getRoomKey());

        return $this->getRedis()->hMGet($this->getRoomKey(), $keys);
    }

    public function getAttr($field)
    {
        return $this->getRedis()->hGet($this->getRoomKey(), $field);
    }

    public function updateAttr($field, $value)
    {
        return $this->getRedis()->hSet($this->getRoomKey(), $field, $value);
    }
}
