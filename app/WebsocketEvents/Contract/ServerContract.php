<?php

namespace App\WebsocketEvents\Contract;

use Swoole\WebSocket\Server;

interface ServerContract
{
    public function getServer() : ?Server;
}
