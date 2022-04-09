<?php

namespace App\WebsocketEvents\Impl;

use App\WebsocketEvents\Contract\ActStrategyContract;
use App\WebsocketEvents\Contract\OnOpenContract;
use App\WebsocketEvents\Contract\ServerContract;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;

abstract class OpenImpl implements ServerContract, OnOpenContract, ActStrategyContract
{
    private $server;

    private $request;

    public function __construct($server, Request $request)
    {
        $this->server  = $server;
        $this->request = $request;
    }

    public function getServer() : ?Server
    {
        return $this->server;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }
}
