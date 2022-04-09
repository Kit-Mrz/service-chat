<?php

namespace App\WebsocketEvents\Impl;

use App\WebsocketEvents\Contract\ActStrategyContract;
use App\WebsocketEvents\Contract\OnMessageContract;
use App\WebsocketEvents\Contract\ServerContract;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server;

abstract class MessageImpl implements ServerContract, OnMessageContract, ActStrategyContract
{
    private $server;
    private $frame;

    public function __construct($server, Frame $frame)
    {
        $this->server = $server;
        $this->frame  = $frame;
    }

    public function getServer() : ?Server
    {
        return $this->server;
    }

    public function getFrame() : Frame
    {
        return $this->frame;
    }
}
