<?php

namespace App\WebsocketEvents\Contract;

use Swoole\Websocket\Frame;

interface OnMessageContract
{
    public function getFrame() : Frame;
}
