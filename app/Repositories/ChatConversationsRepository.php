<?php

namespace App\Repositories;

use App\Model\ChatConversations;

class ChatConversationsRepository extends Repository
{
    protected $model;

    public function __construct(ChatConversations $model)
    {
        $this->model = $model;
    }

    /**
     * @desc 创建房间
     * @param $data
     * @return int
     */
    public function createConversation($data)
    {
        return $this->model->newQuery()->insertGetId($data);
    }

    /**
     * @desc 更新房间信息
     * @param int $conversationId 房间ID
     * @param array $data 更新的内容
     * @return int
     */
    public function updateConversation($conversationId, $data)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update($data);
    }

    /**
     * @desc 获取接待中但未结束的房间
     * @param int $dealUserId 处理者ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function getDealingConversations(int $dealUserId, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        $list = $query->select($columns)->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_TRUE,
                'is_end'       => ChatConversations::IS_END_FALSE,
            ]
        )->get();

        return $list;
    }

    /**
     * @desc 获取客户未完成的房间
     * @param int $userId 用户ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getNotEndConversation($userId, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->select($columns)->where(['user_id' => $userId, 'is_end' => 0])->first();
    }

    /**
     * @desc 获取房间信息
     * @param int $conversationId 房间ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getConversation(int $conversationId, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->select($columns)->where(
            [
                'id' => $conversationId,
            ]
        )->first();
    }

    /**
     * @desc 根据用户获取房间信息
     * @param int $userId 用户ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getConversationByUser(int $userId, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->select($columns)->where(
            [
                'user_id' => $userId,
            ]
        )->first();
    }

    /**
     * @desc 绑定处理者
     * @param int $conversationId 房间ID
     * @param int $dealUserId 处理者ID
     * @return int
     */
    public function bindingDealUser(int $conversationId, int $dealUserId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_TRUE,
            ]
        );
    }

    /**
     * @desc 处理会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function closeConversation(int $conversationId)
    {
        return $this->model->newQuery()->where(
            [
                'id' => $conversationId,
            ]
        )->update(['is_end' => ChatConversations::IS_END_TRUE]);
    }

    /**
     * @desc 设置为已读会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function readConversation(int $conversationId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(['is_read' => ChatConversations::IS_READ_TRUE]);
    }

    /**
     * @desc 设置客户为已读会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function customerReadConversation(int $conversationId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(['customer_is_read' => ChatConversations::IS_READ_TRUE]);
    }

    /**
     * @desc 设置为未读会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function unReadConversation(int $conversationId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(['is_read' => ChatConversations::IS_READ_FALSE]);
    }

    /**
     * @desc 回复会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function replayConversation(int $conversationId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(['is_reply' => ChatConversations::IS_REPLY_TRUE]);
    }

    /**
     * @desc 设置为未回复会话
     * @param int $conversationId 房间ID
     * @return int
     */
    public function unReplayConversation(int $conversationId)
    {
        $query = $this->model->newQuery();

        return $query->where(
            [
                'id' => $conversationId,
            ]
        )->update(['is_reply' => ChatConversations::IS_REPLY_FALSE]);
    }

    /**
     * @desc 接待中数量
     * @param $dealUserId 处理者ID
     * @return int
     */
    public function dealingCount($dealUserId)
    {
        $query = $this->model->newQuery();

        $count = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_TRUE,
                'is_end'       => ChatConversations::IS_END_FALSE,
            ]
        )->count();

        return $count;
    }

    /**
     * @desc 排队中数量
     * @param int $dealUserId 处理者ID
     * @return int
     */
    public function waitCount($dealUserId)
    {
        $query = $this->model->newQuery();

        $count = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_FALSE,
                'is_end'       => ChatConversations::IS_END_FALSE,
            ]
        )->count();

        return $count;
    }

    /**
     * @desc 已接待数量
     * @param int $dealUserId 处理者ID
     * @return int
     */
    public function dealCount($dealUserId)
    {
        $query = $this->model->newQuery();

        $count = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal' => ChatConversations::IS_DEAL_TRUE,
                'is_end' => ChatConversations::IS_END_TRUE,
            ]
        )->count();

        return $count;
    }

    /**
     * @desc 已回复列表
     * @param int $dealUserId 处理者ID
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusAlreadyReply(int $dealUserId)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_reply'     => ChatConversations::IS_REPLY_TRUE,
            ]
        )->get();

        return $list;
    }

    /**
     * @desc 未回复列表
     * @param int $dealUserId 处理者ID
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusNotReply(int $dealUserId)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_reply'     => ChatConversations::IS_REPLY_FALSE,
            ]
        )->get();

        return $list;
    }

    /**
     * @desc 未读列表
     * @param int $dealUserId 处理者ID
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusNotRead(int $dealUserId)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_read'      => ChatConversations::IS_READ_FALSE,
            ]
        )->get();

        return $list;
    }

    /**
     * @desc 已接待
     * @param int $dealUserId 处理者ID
     * @param int $pageIndex 页码
     * @param int $pageSize 页大小
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusAlreadyReception(int $dealUserId, int $pageIndex, int $pageSize)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_TRUE,
                'is_end'       => ChatConversations::IS_END_TRUE,
            ]
        )->offset(($pageIndex - 1) * $pageSize)->limit($pageSize)->get();

        return $list;
    }

    /**
     * @desc 接待中列表
     * @param int $dealUserId 处理者ID
     * @param int $pageIndex 页码
     * @param int $pageSize 页大小
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusReception(int $dealUserId, int $pageIndex, int $pageSize)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'deal_user_id' => $dealUserId,
                'is_deal'      => ChatConversations::IS_DEAL_TRUE,
                'is_end'       => ChatConversations::IS_END_FALSE,
            ]
        )->offset(($pageIndex - 1) * $pageSize)->limit($pageSize)->get();

        return $list;
    }

    /**
     * @desc 排队中
     * @param int $pageIndex 页码
     * @param int $pageSize 页大小
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function retrieveStatusQueueing(int $pageIndex, int $pageSize)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'is_deal'      => ChatConversations::IS_DEAL_FALSE,
                'is_end'       => ChatConversations::IS_END_FALSE,
            ]
        )->offset(($pageIndex - 1) * $pageSize)->limit($pageSize)->get();

        return $list;
    }

    /**
     * @desc 检查是否有未结束的会话
     * @param int $userId 用户ID
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function checkConversation(int $userId)
    {
        $query = $this->model->newQuery();

        $list = $query->where(
            [
                'user_id' => $userId,
                'is_end'  => ChatConversations::IS_END_FALSE,
            ]
        )->orderByDesc('created_at')->first();

        return $list;
    }
}
