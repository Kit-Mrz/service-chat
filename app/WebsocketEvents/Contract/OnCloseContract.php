<?php

namespace App\WebsocketEvents\Contract;

interface OnCloseContract
{
    public function getFd() : int;

    public function getReactorId() : int;
}

