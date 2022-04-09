<?php

namespace App\Services;

use App\Traits\Integration;

class LoginService
{
    use Integration;

    // 用户ID
    protected $id;
    // 写入redis的key
    protected $loginKey;
//    protected $timeout = 60 * 60 * 24;

    public function setUserId($userId)
    {
        $this->id       = $userId;
        $this->loginKey = "userInfo:{$userId}";

        return $this;
    }

    // 登出
    public function logout()
    {
        return $this->getRedis()->del($this->loginKey);
    }

    // 登录
    public function login($userInfo)
    {
        return $this->getRedis()->hMSet($this->loginKey, $userInfo);
    }

    // 登陆信息
    public function loginInfo()
    {
        $keys = $this->getRedis()->hKeys($this->loginKey);

        return $this->getRedis()->hMGet($this->loginKey, $keys);
    }
}
