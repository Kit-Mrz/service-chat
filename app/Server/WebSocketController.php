<?php

namespace App\Server;

use App\WebsocketEvents\Impl\SocketDispatch;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Logger\LoggerFactory;
use Swoole\Http\Request;
use Swoole\Websocket\Frame;

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('log', 'default');
    }

    public function onMessage($server, Frame $frame) : void
    {
        try {
            SocketDispatch::messageDispatch($server, $frame);
        } catch (\Throwable $exception) {
            $err = sprintf('Message: %s, Code: %d, File: %s, Line: %d', $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine());
            echo $err . "\r\n";
            $this->logger->error($err);
        }
    }

    public function onClose($server, int $fd, int $reactorId) : void
    {
        try {
            SocketDispatch::closeDispatch($server, $fd, $reactorId);
        } catch (\Throwable $exception) {
            $err = sprintf('Message: %s, Code: %d, File: %s, Line: %d', $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine());
            echo $err . "\r\n";
            $this->logger->error($err);
        }
    }

    public function onOpen($server, Request $request) : void
    {
        $this->logger->info("onOpen");

        try {
            SocketDispatch::openDispatch($server, $request);
        } catch (\Throwable $exception) {
            $err = sprintf('Message: %s, Code: %d, File: %s, Line: %d', $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine());
            echo $err . "\r\n";
            $this->logger->error($err);
        }
    }
}
