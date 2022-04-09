<?php

namespace App\WebsocketEvents;

use App\WebsocketEvents\Impl\OpenImpl;

class OpenEvent extends OpenImpl
{
    public function execute()
    {
        echo "OpenEvent::execute\r\n";
        return success([], 'open');
    }
}
