<?php

namespace HyperfTest\Cases\Chat;

use HyperfTest\HttpTestCase;

class ChatConversationsControllerTest extends HttpTestCase
{
    // 获取会话列表
    public function testIndex()
    {
        $uri  = '/mall/chat/chat_conversation';
        $data = [
            'deal_user_id' => 408734,
            'status'       => 0,
            'page_index'   => 1,
            'page_size'    => 3,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
        $this->assertIsArray($result['data']['result']);
    }

    // 获取会话总览信息
    public function testOverview()
    {
        $uri  = '/mall/chat/chat_conversation_overview';
        $data = [
            'staff_id'    => 408734,
            'platform_id' => 2,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
        $this->assertIsArray($result['data']['result']);
    }

    // 检查会话
    public function testCheckConversation()
    {
        $uri  = '/mall/chat/check_conversation';
        $data = [
            'user_id'    => 408734,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
        $this->assertIsArray($result['data']['result']);
    }
}
