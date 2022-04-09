<?php

namespace App\Services;

use App\Traits\RepositoriesTrait;

class ChatConversationContentsService
{
    use RepositoriesTrait;

    // 保存回话内容
    public function saveContent(array $data)
    {
        return $this->getRepositories()->chatConversationContentsRepository()->saveContent($data);
    }

    // 获取聊天内容管理列表
    public function contentsPaging(int $conversationId, int $id, int $limit = 20)
    {
        $list = $this->getRepositories()->chatConversationContentsRepository()->contentsPaging($conversationId, $id, $limit);

        return $list;
    }

    // 获取最近的聊天记录
    public function recentlyContents(int $conversationId, int $limit = 5)
    {
        $list = $this->getRepositories()->chatConversationContentsRepository()->recentlyContents($conversationId, $limit);

        return $list;
    }
}
