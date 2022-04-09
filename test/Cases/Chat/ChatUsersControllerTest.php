<?php

namespace HyperfTest\Cases\Chat;

use HyperfTest\HttpTestCase;

class ChatUsersControllerTest extends HttpTestCase
{

    public function testSend()
    {
        $uri  = '/test/testSend';
        $data = [];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(true);
    }

    // 创建入驻人员信息
    public function testStore()
    {
        $uri  = '/mall/chat/chat_user';
        $data = [
            'platform'         => 2,
            'user_type'        => 'staff',
            'platform_user_id' => 4087342,
            'user_role'        => 1,
            'status'           => 1,
            'describe'         => 'describe 01',
        ];

        $result = $this->post($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
        $this->assertIsInt($result['data']['result']);
    }

    // 更新入驻人员信息
    public function testUpdate()
    {
        $uri  = '/mall/chat/chat_user/2';
        $data = [
            'platform'         => 2,
            'user_type'        => 'staff',
            'platform_user_id' => 4087342,
            'user_role'        => 1,
            'status'           => 1,
            'describe'         => 'describe 03',
        ];

        $result = $this->put($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
    }

    // 获取入驻人员列表
    public function testIndex()
    {
        $uri  = '/mall/chat/chat_user';
        $data = [
            'platform'   => 1,
            'user_type'  => 'staff',
            'page_size'  => 2,
            'page_index' => 1,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertTrue(is_array($result));
    }

    // 后台获取入驻信息
    public function testStaffInfo()
    {
        $uri  = '/mall/chat/get_staff_info';
        $data = [
            'platform_user_id' => 408735,
        ];

        $result = $this->get($uri, $data);

        var_dump($result);

        $this->assertIsArray($result);
        $this->assertIsArray(($result['data']['staff_info']));
        $this->assertTrue(($result['data']['staff_info']['platform_user_id'] == 408735));
    }

    // 更改后台导师头衔
    public function testRefreshDescribe()
    {
        $uri  = '/mall/chat/refresh_describe';
        $data = [
            'platform_user_id' => 408735,
            'describe'         => 'describe test 01',
        ];

        $result = $this->post($uri, $data);

        var_dump($result);

        $this->assertIsArray($result);
        $this->assertIsInt(($result['data']['result']));
    }

    public function testRun()
    {
        $this->assertTrue(true);
    }
}
