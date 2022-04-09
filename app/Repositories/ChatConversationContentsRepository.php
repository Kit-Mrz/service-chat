<?php

namespace App\Repositories;

use App\Model\ChatConversationContents;
use Hyperf\DbConnection\Db;

class ChatConversationContentsRepository extends Repository
{
    protected $model;

    public function __construct(ChatConversationContents $model)
    {
        $this->model = $model;
    }

    /**
     * @desc 保存聊天记录
     * @param array $data
     * @return bool
     */
    public function saveContent(array $data)
    {
        return $this->multiCreate($data);
    }

    /**
     * @desc 会话内容分页
     * @param int $conversationId 房间ID
     * @param int $id 分页主键ID
     * @param int $pageSize 页大小
     * @return array
     */
    public function contentsPaging(int $conversationId, int $id, int $pageSize = 20)
    {
        $and = '';
        // 第一页
        if ($id > 0) {
            $and = " AND chat_conversation_contents.id < {$id} ";
        }

        $sql = "SELECT chat_users.nickname, chat_users.avatar, chat_users.platform_user_id, chat_users.user_type,
                        chat_conversation_contents.id, chat_conversation_contents.content_type, chat_conversation_contents.content, chat_conversation_contents.created_at
                    FROM `chat_conversation_contents`
                    LEFT JOIN `chat_users` ON chat_conversation_contents.user_id = chat_users.id
                    WHERE chat_conversation_contents.conversation_id = {$conversationId}
                    {$and}
                    ORDER BY chat_conversation_contents.id DESC
                    LIMIT {$pageSize}";

        $list = Db::connection('default')->select($sql);

        return $list;
    }

    /**
     * @desc 获取最近的聊天记录，前端打开会话时查看近N条聊天内容
     * @param int $conversationId 房间ID
     * @param int $limit 条数
     * @return array
     */
    public function recentlyContents(int $conversationId, int $limit = 3)
    {
        $sql = '
            SELECT 
	        ccc.id, ccc.conversation_id, ccc.user_id, ccc.content_type, ccc.content, ccc.created_at,
	        cu.real_name, cu.nickname, cu.avatar, cu.user_type, cu.platform
	        FROM chat_conversation_contents AS `ccc` LEFT JOIN `chat_users` AS `cu` ON cu.id = ccc.user_id
	        WHERE ccc.conversation_id = ' . $conversationId . ' AND ccc.content_type IN(1, 2, 3, 4) ORDER BY ccc.id DESC LIMIT ' . $limit;

        $list = Db::connection('default')->select($sql);

        return $list;
    }
}
