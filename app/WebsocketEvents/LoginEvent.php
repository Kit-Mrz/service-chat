<?php

namespace App\WebsocketEvents;

use App\Model\ChatUsers;
use App\Traits\ServicesTrait;
use App\WebsocketEvents\Exceptions\BusinessException;
use App\WebsocketEvents\Impl\MessageImpl;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class LoginEvent extends MessageImpl
{
    use ServicesTrait;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    protected $inject = Inject::class;

    public function execute()
    {

        try {
            // 数据校验
            $data = $this->validation();
            $fd   = $this->getFrame()->fd;

            /*
            $data = [
                'platform'         => $params['platform'],
                'platform_user_id' => $params['platform_user_id'],
                'mobile'           => $params['mobile'],
                'user_type'        => $params['user_type'],
                'nickname'         => $params['nickname'],
                'avatar'           => $params['avatar'],
            ];
             */

            // 获取用户信息
            $userInfo = $this->getServices()->chatUsersService()->enterStation($data);

            // 取消消息推送
            $this->getServices()->notificationService()->setUserId($userInfo['id'])->cancel();

            // 双向绑定
            $this->getServices()->connectionMapService()->binding($fd, $userInfo['id']);

            // 上线
            $this->getServices()->onlineService()->setUserId($userInfo['id'])->up();

            // 员工不用分配房间
            if (ChatUsers::isCustomer($userInfo['user_type'])) {
                // 获取房间
                $roomInfo = $this->getServices()->chatConversationsService()->getActiveConversation($userInfo['id']);

                $userInfo['room_id'] = $roomInfo['id'];
                // 记录房间信息
                $this->getServices()->roomInfoService()->setRoomId($roomInfo['id'])->remember($roomInfo->toArray());
                // 加入房间
                $this->getServices()->roomService()->setRoomId($roomInfo['id'])->addMember($userInfo['id']);
            } else if (ChatUsers::isStaff($userInfo['user_type'])) {
                // 获取客服所有的房间号
                $rooms = $this->getServices()->chatConversationsService()->getDealingConversationsIds($userInfo['id']);

                $room_ids = [];

                foreach ($rooms as $item) {
                    $room_ids[] = $item->id;
                    // 加入房间
                    $this->getServices()->roomService()->setRoomId($item->id)->addMember($userInfo['id']);
                }

                $userInfo['room_id'] = join(',', $room_ids);
            }

            // 登陆
            $this->getServices()->loginService()->setUserId($userInfo['id'])->login($userInfo);

            $pushMessage = [
                'type'         => 'login',
                'room_id'      => $userInfo['room_id'] ?? 0,
                'talk_time_at' => date('Y-m-d H:i:s'),
                'userInfo'     => $userInfo,
            ];

            return success($pushMessage, 'login');
        } catch (BusinessException $e) {
            $msg = sprintf("Code: %d, Message: %s, File: %s, Line: %d",
                           $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine()
            );

            return fail([], $msg);
        }
    }

    // 验证数据
    protected function validation() : array
    {
        $params = $this->getFrame()->data;

        $validator = $this->validationFactory->make(
            $params,
            [
                'platform'         => 'required|int',
                'platform_user_id' => 'required|int',
                'user_type'        => 'required|string',
                'mobile'           => 'string',
                'nickname'         => 'string',
                'avatar'           => 'string',
            ],
            [
                'platform.required'         => 'platform is required',
                'platform_user_id.required' => 'platform_user_id is required',
                'user_type.required'        => 'user_type is required',
                'mobile.string'             => 'mobile is string',
                'nickname.string'           => 'nickname is string',
                'avatar.string'             => 'avatar is string',
            ]
        );

        if ($validator->fails()) {
            throw new BusinessException($validator->errors()->first());
        }

        $data = [
            'platform'         => $params['platform'],
            'platform_user_id' => $params['platform_user_id'],
            'mobile'           => $params['mobile'],
            'user_type'        => $params['user_type'],
            'nickname'         => $params['nickname'],
            'avatar'           => $params['avatar'],
        ];

        return $data;
    }
}
