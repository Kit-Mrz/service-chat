<?php

namespace App\Server;

use Hyperf\Framework\Bootstrap\WorkerStartCallback;
use Hyperf\Redis\Redis;
use Swoole\Server as SwooleServer;
use Swoole\Coroutine;

class WorkerStartController extends WorkerStartCallback
{
    public function onWorkerStart(SwooleServer $server, int $workerId)
    {
        parent::onWorkerStart($server, $workerId);
    }
}
