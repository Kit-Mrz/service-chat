<?php

namespace App\WebsocketEvents;

use App\Traits\ServicesTrait;
use App\WebsocketEvents\Impl\MessageImpl;

// 客服主动接入客户
class BindEvent extends MessageImpl
{
    use ServicesTrait;

    public function execute()
    {
        $params = $this->getFrame()->data;

        $data            = [
            'room_id'      => $params['room_id'],
            'platform'     => $params['platform'],
            'chat_user_id' => $params['chat_user_id'],
            'nickname'     => $params['nickname'],
            'avatar'       => $params['avatar'],
        ];

        $roomInfoService = $this->getServices()->roomInfoService()->setRoomId($data['room_id']);

        // 检测是否已经绑定
        $deal_user_id = $roomInfoService->getAttr('deal_user_id');
        if (! empty($deal_user_id)) {
            return fail(['type' => 'bind', 'room_id' => $data['room_id'], 'status' => 0], '接入失败！其他客服正在处理当前会话');
        }

        // 绑定处理者
        $roomInfoService->updateAttr('deal_user_id', $data['chat_user_id']);
        $roomInfoService->updateAttr('is_deal', 1);

        // 持久化信息
        $this->getServices()->chatConversationsService()->updateConversation($data['room_id'], $roomInfoService->roomInfo());

        // 加入房间
        $this->getServices()->roomService()->setRoomId($data['room_id'])->addMember($data['chat_user_id']);

        return success(['type' => 'bind', 'room_id' => $data['room_id'], 'status' => 1], '绑定成功');
    }

}
