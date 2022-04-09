<?php

use Hyperf\Utils\ApplicationContext;

function app(string $id)
{
    return ApplicationContext::getContainer()->get($id);
}

function success(array $data, string $msg = '')
{
    $ret = [
        'code' => 200,
        'msg'  => empty($msg) ? 'success' : $msg,
        'data' => $data,
    ];

    return $ret;
}

function fail(array $data, string $msg = '')
{
    $ret = [
        'code' => 400,
        'msg'  => empty($msg) ? 'fail' : $msg,
        'data' => $data,
    ];

    return $ret;
}

// 获取服务器IP
function getServerIp() : string
{
    $hostName = gethostname();

    return gethostbyname($hostName);
}
