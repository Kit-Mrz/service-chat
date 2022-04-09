<?php

namespace App\Services;

use App\Traits\RepositoriesTrait;
use App\WebsocketEvents\Exceptions\BusinessException;

class ChatConversationsService
{
    use RepositoriesTrait;

    // 更新房间信息
    public function updateConversation($conversationId, $data)
    {
        return $this->getRepositories()->chatConversationsRepository()->updateConversation($conversationId, $data);
    }

    // 获取客户活跃的房间信息
    public function getActiveConversation($userId)
    {
        $columns = ['id', 'user_id', 'deal_user_id', 'is_deal', 'is_end', 'is_reply', 'is_read', 'customer_is_read'];

        $roomInfo = $this->getRepositories()->chatConversationsRepository()->getConversationByUser($userId, $columns);

        // 客服正在接待的会话，直接返回
        if ( !empty($roomInfo) && $roomInfo['is_end'] == 0) {
            return $roomInfo;
        }

        // 重新激活房间 is_end == 1 表示客服已经接待过，并 finish 的会话
        if ( !empty($roomInfo) && $roomInfo['is_end'] == 1) {
            $this->getRepositories()->chatConversationsRepository()->updateConversation($roomInfo['id'], [
                'deal_user_id'     => 0,
                'is_deal'          => 0,
                'is_end'           => 0,
                'is_reply'         => 0,
                'is_read'          => 0,
                'customer_is_read' => 0,
            ]);
            $roomInfo = $this->getRepositories()->chatConversationsRepository()->getConversation($roomInfo['id'], $columns);

            return $roomInfo;
        }

        if (empty($roomInfo)) {
            // 新建房间
            $roomId = $this->getRepositories()->chatConversationsRepository()->createConversation(['user_id' => $userId]);

            $roomInfo = $this->getRepositories()->chatConversationsRepository()->getConversation($roomId, $columns);

            if (empty($info)) {
                throw new BusinessException('创建房间失败');
            }

            return $roomInfo;
        }

        throw new BusinessException('getActiveConversation 的未知情况！');
    }

    // 获取接待中的所有房间号
    public function getDealingConversationsIds(int $dealUserId)
    {
        return $this->getRepositories()->chatConversationsRepository()->getDealingConversations($dealUserId, ['id']);
    }

    // 检查处理者
    public function checkBindingDealUser(int $conversationId)
    {
        $conversation = $this->getRepositories()->chatConversationsRepository()->getConversation($conversationId, ['id', 'deal_user_id']);

        return $conversation->deal_user_id > 0; // > 0 有客服处理
    }

    // 绑定处理者
    public function bindingDealUser(int $conversationId, int $dealUserId)
    {
        return $this->getRepositories()->chatConversationsRepository()->bindingDealUser($conversationId, $dealUserId);
    }

    // 关闭会话
    public function closeConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->closeConversation($conversationId);
    }

    // 已读会话
    public function readConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->readConversation($conversationId);
    }

    // 设置客户为已读会话
    public function customerReadConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->customerReadConversation($conversationId);
    }

    // 未读会话
    public function unReadConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->unReadConversation($conversationId);
    }

    // 回复会话
    public function replayConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->replayConversation($conversationId);
    }

    // 设置为未回复会话
    public function unReplayConversation(int $conversationId)
    {
        return $this->getRepositories()->chatConversationsRepository()->unReplayConversation($conversationId);
    }

    //(1=已回复;2=未回复;3=未读;4=已接待;5=接待中;6=排队中)

    // 1=已回复
    public function retrieveStatusAlreadyReply(int $dealUserId)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusAlreadyReply($dealUserId);

        return $list ? $list->toArray() : [];
    }

    // 2=未回复
    public function retrieveStatusNotReply(int $dealUserId)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusNotReply($dealUserId);

        return $list ? $list->toArray() : [];
    }

    // 3=未读
    public function retrieveStatusNotRead(int $dealUserId)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusNotRead($dealUserId);

        return $list ? $list->toArray() : [];
    }

    // 4=已接待
    public function retrieveStatusAlreadyReception(int $dealUserId, int $pageIndex, int $pageSize)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusAlreadyReception($dealUserId, $pageIndex, $pageSize);

        if ($list->isEmpty()) {
            return [];
        }

        $list = $this->assemble($list);

        return $list;
    }

    // 5=接待中
    public function retrieveStatusReception(int $dealUserId, int $pageIndex, int $pageSize)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusReception($dealUserId, $pageIndex, $pageSize);

        if ($list->isEmpty()) {
            return [];
        }

        $list = $this->assemble($list);

        return $list;
    }

    // 6=排队中
    public function retrieveStatusQueueing(int $pageIndex, int $pageSize)
    {
        $list = $this->getRepositories()->chatConversationsRepository()->retrieveStatusQueueing($pageIndex, $pageSize);

        if ($list->isEmpty()) {
            return [];
        }

        $list = $this->assemble($list);

        return $list;
    }

    // 数据聚合
    protected function assemble($conversationList)
    {


        foreach ($conversationList as $item) {
            $customerInfo = $this->getRepositories()->chatUsersRepository()->find($item->user_id);

            $item->customer_avatar   = $customerInfo['avatar'] ?? '';
            $item->mobile            = $customerInfo['mobile'] ?? '';
            $item->customer_nickname = $customerInfo['nickname'] ?? '';
            $item->platform          = $customerInfo['platform'] ?? '';
            $item->platform_user_id  = $customerInfo['platform_user_id'] ?? '';

            $item->contents = $this->getRepositories()->chatConversationContentsRepository()->recentlyContents($item->id);
        }

        return $conversationList;
    }

    // 接待中数量
    public function dealingCount($dealUserId)
    {
        return $this->getRepositories()->chatConversationsRepository()->dealingCount($dealUserId);
    }

    // 已接待数量
    public function dealCount($dealUserId)
    {
        return $this->getRepositories()->chatConversationsRepository()->dealCount($dealUserId);
    }

    // 排队中数量
    public function waitCount($dealUserId)
    {
        return $this->getRepositories()->chatConversationsRepository()->waitCount($dealUserId);
    }

    // 会话信息总览
    public function overview(int $userId, int $platform)
    {
        $userInfo = $this->getRepositories()->chatUsersRepository()->getUserViaPlatformUserId($userId, $platform, $columns = ['*']);

        $result = [
            'user_info'        => [],
            'is_dealing_count' => 0,
            'is_ranking_count' => 0,
        ];

        if (empty($userInfo)) {
            return $result;
        }

        // 接待中数量
        $result['is_dealing_count'] = $this->dealingCount($userInfo['id']);

        // 排队中数量
        $result['is_ranking_count'] = $this->waitCount($userInfo['id']);

        return $result;
    }

    // 检查是否有未结束的会话
    public function checkConversation(int $platform, int $platformUserId)
    {
        $userInfo = $this->getRepositories()->chatUsersRepository()->getUserViaPlatformUserId($platform, $platformUserId, [
            'id', 'platform', 'platform_user_id', 'nickname', 'avatar'
        ]);

        if ( !isset($userInfo->id)) {
            return [];
        }

        $hasConversation = $this->getRepositories()->chatConversationsRepository()->checkConversation($userInfo->id);

        return $hasConversation ? $hasConversation->toArray() : [];
    }
}
