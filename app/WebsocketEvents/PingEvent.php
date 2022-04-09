<?php

namespace App\WebsocketEvents;

use App\Model\ChatUsers;
use App\Traits\ServicesTrait;
use App\WebsocketEvents\Impl\MessageImpl;

class PingEvent extends MessageImpl
{
    use ServicesTrait;

    public function execute()
    {
        $params = $this->getFrame()->data;

        $data = [
            'room_id'   => $params['room_id'] ?? 0,
            'user_type' => $params['user_type'] ?? '',
        ];

        $roomInfoService = $this->getServices()->roomInfoService()->setRoomId($data['room_id']);

        if ($data['room_id'] > 0 && ChatUsers::isStaff($data['user_type'])) {
            $roomInfoService->updateAttr('is_read', 1);
        } else if ($data['room_id'] > 0 && ChatUsers::isCustomer($data['user_type'])) {
            $roomInfoService->updateAttr('customer_is_read', 1);
        }

        return success([], 'pong');
    }
}
