<?php

namespace HyperfTest\Cases\Chat;

use HyperfTest\HttpTestCase;

class ChatConversationContentsControllerTest extends HttpTestCase
{
    // 获取聊天内容管理列表
    public function testConversationContents()
    {
        $uri  = '/mall/chat/chat_content';
        $data = [
            'conversation_id' => 2,
            'id'              => 1,
            'page_size'       => 2,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
        $this->assertIsArray($result['data']['result']);
    }
}
