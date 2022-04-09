<?php

namespace App\WebsocketEvents\Impl;

use App\WebsocketEvents\Contract\ActStrategyContract;
use App\WebsocketEvents\Contract\OnCloseContract;
use App\WebsocketEvents\Contract\ServerContract;
use Swoole\WebSocket\Server;

abstract class CloseImpl implements ServerContract, OnCloseContract, ActStrategyContract
{
    private $server;
    private $fd;
    private $reactorId;

    public function __construct($server, int $fd, int $reactorId)
    {
        $this->server    = $server;
        $this->fd        = $fd;
        $this->reactorId = $reactorId;
    }

    public function getServer() : ?Server
    {
        return $this->server;
    }

    public function getFd() : int
    {
        return $this->fd;
    }

    public function getReactorId() : int
    {
        return $this->reactorId;
    }
}
