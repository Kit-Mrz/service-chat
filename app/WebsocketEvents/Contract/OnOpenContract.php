<?php

namespace App\WebsocketEvents\Contract;

use Swoole\Http\Request;

interface OnOpenContract
{
    public function getRequest() : Request;
}
