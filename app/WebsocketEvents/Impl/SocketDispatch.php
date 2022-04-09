<?php

namespace App\WebsocketEvents\Impl;

use App\WebsocketEvents\BindEvent;
use App\WebsocketEvents\CloseEvent;
use App\WebsocketEvents\Exceptions\BusinessException;
use App\WebsocketEvents\FinishEvent;
use App\WebsocketEvents\LoginEvent;
use App\WebsocketEvents\OpenEvent;
use App\WebsocketEvents\PingEvent;
use App\WebsocketEvents\SecretEvent;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class SocketDispatch
{
    // 事件绑定
    protected static $bindingMap = [
        'open'  => OpenEvent::class,
        // 关闭连接
        'close' => CloseEvent::class,
        // 登陆
        'login' => LoginEvent::class,
        // 绑定（客服接入用户房间）
        'bind' => BindEvent::class,
        // 私聊
        'secret' => SecretEvent::class,
        // 心跳
        'ping'  => PingEvent::class,
        // 结束会话
        'finish'  => FinishEvent::class,
    ];

    /**
     * @param
     * @param Response|Server $server
     * @param Frame $frame
     */
    public static function messageDispatch($server, Frame $frame)
    {
        $frame->data = json_decode($frame->data, true);

        if (json_last_error() > 0) {
            $error = json_last_error();
            throw new BusinessException("Json解析错误: {$error}");
        }

        $type = $frame->data['type'] ?? '';

        if ( !isset(self::$bindingMap[$type])) {
            throw new BusinessException('尚未支持的通信类型');
        }

        $event = self::$bindingMap[$type];

        $obj = new $event($server, $frame);

        $result = $obj->execute();

        if ($server->isEstablished($frame->fd)) {
            $server->push($frame->fd, json_encode($result));
        }
    }

    /**
     * @param Response|\Swoole\Server $server
     */
    public static function closeDispatch($server, int $fd, int $reactorId)
    {
        /** @var CloseEvent $event */
        $event = self::$bindingMap['close'];

        $obj = new $event($server, $fd, $reactorId);

        $obj->execute();
    }

    /**
     * @param Response|Server $server
     */
    public static function openDispatch($server, Request $request)
    {
        /** @var OpenEvent $event */
        $event = self::$bindingMap['open'];

        $obj = new $event($server, $request);

        $result = $obj->execute();

        if ($server->isEstablished($request->fd)) {
            $server->push($request->fd, json_encode($result));
        }
    }
}
