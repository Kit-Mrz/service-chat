<?php

namespace App\Services;

use App\Model\ChatUsers;
use App\Traits\RepositoriesTrait;
use App\WebsocketEvents\Exceptions\BusinessException;

class ChatUsersService
{
    use RepositoriesTrait;

    public function find($id, array $columns = ['*'])
    {
        return $this->getRepositories()->chatUsersRepository()->find($id, $columns);
    }

    // 入驻流程
    public function enterStation(array $params)
    {
        $data = [
            'platform'         => (string) $params['platform'],
            'user_type'        => (string) $params['user_type'],
            'mobile'           => (string) $params['mobile'],
            'platform_user_id' => (int) $params['platform_user_id'],
            'nickname'         => (string) $params['nickname'],
            'avatar'           => (string) $params['avatar'],
            'login_at'         => date('Y-m-d H:i:s'),
        ];

        $columns = [
            'id', 'platform', 'platform_user_id', 'user_type', 'nickname', 'avatar', 'mobile'
        ];

        // 获取用户
        $userInfo = $this->getUserViaPlatformUserId($data['platform'], $data['platform_user_id'], $columns);

        // 更新用户信息
        if ( !empty($userInfo)) {
            $this->getRepositories()->chatUsersRepository()->update($userInfo['id'], [
                'nickname' => $data['nickname'],
                'avatar'   => $data['avatar'],
                'mobile'   => $data['mobile'],
                'login_at' => date('Y-m-d H:i:s'),
            ]);

            return $userInfo->toArray();
        }

        // 如果是客服
        if (empty($userInfo) && ChatUsers::isStaff($data['user_type'])) {
            throw new BusinessException('客服请注册!');
        }

        // 创建客户信息
        if ( !$this->getRepositories()->chatUsersRepository()->create($data)) {
            throw new BusinessException('创建用户信息失败!');
        }

        // 获取用户
        $userInfo = $this->getUserViaPlatformUserId($data['platform'], $data['platform_user_id'], $columns);

        return $userInfo->toArray();
    }

    // 获取用户
    public function getUserViaPlatformUserId(int $platform, int $platformUserId, array $columns = ['*'])
    {
        return $this->getRepositories()->chatUsersRepository()->getUserViaPlatformUserId($platform, $platformUserId, $columns);
    }

    // 检索入驻人员列表
    public function retrieve(array $params)
    {
        $platform   = $params['platform'];
        $user_type  = $params['user_type'];
        $status     = $params['status'];
        $page_index = $params['page_index'];
        $page_size  = $params['page_size'];

        // 索引用户列表
        $users = $this->getRepositories()->chatUsersRepository()->getPlatformUserType($platform, $user_type, $status, $page_index, $page_size);
        // 总条数
        $countUser = $this->getRepositories()->chatUsersRepository()->getPlatformUserTypeCount($platform, $user_type, $status);
        // 总页码
        $countPage = ceil($countUser / $page_size);

        foreach ($users as $item) {
            $item->dealing_count = $this->getRepositories()->chatConversationsRepository()->dealingCount($item->id);
            $item->deal_count    = $this->getRepositories()->chatConversationsRepository()->dealCount($item->id);
            $item->wait_count    = $this->getRepositories()->chatConversationsRepository()->waitCount($item->id);
        }

        return [
            'data'      => $users->toArray(),
            'countUser' => $countUser,
            'countPage' => $countPage,
        ];
    }

    // 更新入驻人员
    public function updateChatUser(int $id, array $data)
    {
        return $this->getRepositories()->chatUsersRepository()->update($id, $data);
    }

    // 创建入驻人员
    public function createChatUser(array $data)
    {
        return $this->getRepositories()->chatUsersRepository()->create($data);
    }

    // 删除入驻人员
    public function deleteChatUser(int $id)
    {
        return $this->getRepositories()->chatUsersRepository()->delete($id);
    }
}
