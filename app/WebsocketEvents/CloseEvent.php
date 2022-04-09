<?php

namespace App\WebsocketEvents;

use App\Model\ChatUsers;
use App\Traits\ServicesTrait;
use App\WebsocketEvents\Impl\CloseImpl;

class CloseEvent extends CloseImpl
{
    use ServicesTrait;

    public function execute()
    {
        echo "CloseEvent::execute\r\n";

        $connectionMapService = $this->getServices()->connectionMapService();
        $loginService         = $this->getServices()->loginService();
        $onlineService        = $this->getServices()->onlineService();

        // 获取用户ID
        $userId = $connectionMapService->getUserId($this->getFd());
        // 解绑
        $connectionMapService->unBinding($this->getFd(), $userId);
        // 用户信息
        $userInfo = $loginService->setUserId($userId)->loginInfo();
        // 登出
        $loginService->logout();
        // 下线
        $onlineService->setUserId($userId)->down();
        // 退出房间
        if (ChatUsers::isCustomer($userInfo['user_type'])) {
            $this->storeRoomInfo($userInfo['room_id']);
        } else if (ChatUsers::isStaff($userInfo['user_type'])) {
            $room_ids = $userInfo['room_id'];
            $room_ids = explode(',', $room_ids);
            foreach ($room_ids as $rid) {
                $this->storeRoomInfo($rid);
            }
        }

        return success([], 'close');
    }

    // 保存房间信息
    public function storeRoomInfo($roomId)
    {
        $roomInfoService = $this->getServices()->roomInfoService();

        $ChatConversationsService = $this->getServices()->chatConversationsService();

        $roomInfo = $roomInfoService->setRoomId($roomId)->roomInfo();

        if ( !empty($roomInfo)) {
            $ChatConversationsService->updateConversation($roomId, $roomInfo);
        }
    }
}
