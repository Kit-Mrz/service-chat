<?php

namespace App\WebsocketEvents;

use App\Traits\ServicesTrait;
use App\WebsocketEvents\Impl\MessageImpl;

class FinishEvent extends MessageImpl
{
    use ServicesTrait;

    public function execute()
    {
        $data = $this->getFrame()->data;

        $params = [
            'platform' => $data['platform'],
            'room_id'  => $data['room_id'],
        ];

        // 关闭会话
        $roomInfoService = $this->getServices()->roomInfoService()->setRoomId($params['room_id']);
        /**
         * 重新初始化这个房间，作用是同一个用户沟通的数据放到同一个会话里面，不再重新初始化新的房间
         * 1. 删除原有绑定者
         * 2. 将该房间设置为 未接待和未结束
         *
         */
//        $roomInfoService->updateAttr('deal_user_id', 0);
//        $roomInfoService->updateAttr('is_deal', 0);
        $result = $roomInfoService->updateAttr('is_end', 1);

        // 持久化信息
        $this->getServices()->chatConversationsService()->updateConversation($params['room_id'], $roomInfoService->roomInfo());

        // 通知对方，finish，并 删除房间
        $this->getServices()->roomService()->setRoomId($params['room_id'])->broadcast($this->getServer(), success(
        [
            'type'     => 'finish',
            'platform' => $data['platform'],
            'room_id'  => $data['room_id'],
            'finish'   => (int) $result,
        ], 'finish'))
        ->removeRoom();

        $roomInfoService->forget();

        return success(
            [
                'type'     => 'finish',
                'platform' => $data['platform'],
                'room_id'  => $data['room_id'],
                'finish'   => (int) $result,
            ], 'finish');
    }
}
