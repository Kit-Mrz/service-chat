<?php

namespace App\WebsocketEvents;

use App\Model\ChatUsers;
use App\Traits\ServicesTrait;
use App\WebsocketEvents\Impl\MessageImpl;

class SecretEvent extends MessageImpl
{
    use ServicesTrait;

    public function execute()
    {
        $params = $this->getFrame()->data;

        $data = [
            // 发送者
            'chat_user_id' => $params['chat_user_id'],
            // 房间号
            'room_id'      => $params['room_id'],
            // 平台类型
            'platform'     => $params['platform'],
            // 发送内容
            'content'      => $params['content'],
            // 发送类型
            'content_type' => $params['content_type'],
        ];

        // 登陆检测
        if (empty($userInfo = $this->getServices()->loginService()->setUserId($data['chat_user_id'])->loginInfo())) {
            return fail(['type' => 'secret'], '请先登陆');
        }

        // 获取房间所有的用户
        $roomMembers = $this->getServices()->roomService()->setRoomId($data['room_id'])->getMembers();
        foreach ($roomMembers as $uid) {
            if ($uid == $data['chat_user_id']) {
                continue;
            }
            // 如果是离线用户
            if ( !$this->getServices()->onlineService()->setUserId($uid)->checkOnline()) {
                // 标记该用户将会有一条消息推送
                $this->getServices()->notificationService()->setUserId($uid)->mark();
                // 投递延迟队列
                $this->getServices()->queueService()->push(['user_id' => $uid], 300);
            }
        }

        // 初始化房间 -> 广播消息(跳过自己)
        $this->getServices()->roomService()->setRoomId($data['room_id'])->setSkip($data['chat_user_id'])->broadcast($this->getServer(), success(
            [
                'type'             => 'reply',
                'room_id'          => $data['room_id'],
                'tips'             => "[{$userInfo['nickname']}]: 发送消息",
                'chat_user_id'     => $userInfo['id'],
                'platform'         => $userInfo['platform'],
                'platform_user_id' => $userInfo['platform_user_id'],
                'nickname'         => $userInfo['nickname'],
                'avatar'           => $userInfo['avatar'],
                'user_type'        => $userInfo['user_type'],
                'mobile'           => $userInfo['mobile'],
                'content'          => $data['content'],
                'content_type'     => $data['content_type'],
            ], 'reply'));

        // 保存会话内容
        $this->getServices()->chatConversationContentsService()->saveContent(
            [
                'conversation_id' => $data['room_id'],
                'user_id'         => $data['chat_user_id'],
                'content'         => $data['content'],
                'content_type'    => $data['content_type'],
            ]
        );

        /**
         * ($data['content_type'] == 99) 用户断开连接时都会发送一条 $data['content_type'] == 99，$data['content'] 为时间戳 + 4 位随机数的内容
         *
         * 由于小程序前端在发送图片，调起相册库选择图片时，会造成 socket 连接断开，
         * 那么在断开过程中客服可能会发送消息给用户，前端无法感知到消息是否有新的消息。
         * 所以，前端每次断开时，都会发送一条 content_type=99 的消息用于标识，
         * 表示我是在这条内容断开的连接，用户再次连接时，就可以识别到 content_type=99 之后发送的消息都是最新的消息。
         *
         * 并且，因为每次断开时都有一条 99 类型的消息，这条消息不用处理它的已读未读状态了，否则永远都是未读。
         */

        if ($data['content_type'] != 99) {
            $roomInfoService = $this->getServices()->roomInfoService()->setRoomId($data['room_id']);

            if (ChatUsers::isCustomer($userInfo['user_type'])) { // 客户回复
                // 设置 is_reply = false, 表示客服未回复
                $roomInfoService->updateAttr('is_reply', 0);
                // 客户已读
                $roomInfoService->updateAttr('customer_is_read', 1);
                // 客服未读
                $roomInfoService->updateAttr('is_read', 0);
            } else if (ChatUsers::isStaff($userInfo['user_type'])) { // 员工回复
                // 设置 is_reply = true, 表示客服已回复
                $roomInfoService->updateAttr('is_reply', 1);
                // 客户未读
                $roomInfoService->updateAttr('customer_is_read', 0);
                // 客服已读
                $roomInfoService->updateAttr('is_read', 1);
            }
        }

        return success(
            [
                'type'         => 'secret',
                'room_id'      => $data['room_id'],
                'mine'         => 1,
                'nickname'     => $userInfo['nickname'],
                'avatar'       => $userInfo['avatar'],
                'user_type'    => $userInfo['user_type'],
                'mobile'       => $userInfo['mobile'],
                'talk_time_at' => date('Y-m-d H:i:s'),
                'chat_user_id' => $data['chat_user_id'],
                'content'      => $data['content'],
                'content_type' => $data['content_type'],
            ], 'secret');
    }
}
