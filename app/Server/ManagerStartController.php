<?php

namespace App\Server;

use Hyperf\Framework\Bootstrap\ManagerStartCallback;
use Swoole\Server as SwooleServer;

class ManagerStartController extends ManagerStartCallback
{
    public function onManagerStart(SwooleServer $server)
    {
        parent::onManagerStart($server);
    }
}
