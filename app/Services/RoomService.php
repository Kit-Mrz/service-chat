<?php

namespace App\Services;

use App\Traits\Integration;
use App\Traits\ServicesTrait;

class RoomService
{
    use Integration;
    use ServicesTrait;

    // 房间号
    protected $room_id;
    // 集合的key
    protected $roomKey;
    // 跳过的FD
    protected $skipId = [];

    //const CACHE_TIME = 3600 * 24;

    // 设置房间号
    public function setRoomId($room_id)
    {
        $this->room_id = $room_id;
        $this->roomKey = 'room:' . $room_id;

        return $this;
    }

    public function getRoomId()
    {
        return $this->room_id;
    }

    public function getRoomKey()
    {
        return $this->roomKey;
    }

    // 删除房间
    public function removeRoom()
    {
        $this->getRedis()->del($this->getRedis()->del($this->getRoomKey()));
    }

    // 加入房间
    public function addMember($id)
    {
        $this->getRedis()->sAdd($this->getRoomKey(), $id);

        //$this->getRedis()->expire($this->getRoomKey(), self::CACHE_TIME);

        return $this;
    }

    // 成员退出房间
    public function delMember($id)
    {
        return $this->getRedis()->sRem($this->getRoomKey(), $id);
    }

    // 获取所有成员
    public function getMembers()
    {
        return $this->getRedis()->sMembers($this->getRoomKey());
    }

    // 跳过的FD
    public function setSkip($skipId)
    {
        if (is_array($skipId)) {
            $this->skipId = $skipId;
        } else {
            $this->skipId = [$skipId];
        }

        return $this;
    }

    public function getSkip()
    {
        return $this->skipId;
    }

    public function releaseSkip()
    {
        $this->skipId = [];

        return $this;
    }

    // 广播消息
    public function broadcast($server, $message)
    {
        $members = $this->getMembers();

        $skipId = $this->getSkip();

        $this->releaseSkip();

        $message = is_array($message) ? json_encode($message) : $message;

        foreach ($members as $userId) {
            if (in_array($userId, $skipId)) {
                continue;
            }

            // 拿FD
            $fd = $this->getServices()->connectionMapService()->getFd($userId);

            // 连接是否有效
            if ($server->isEstablished($fd)) {
                $server->push($fd, $message);
            } else {
                $this->delMember($userId);
            }
        }

        return $this;
    }
}
