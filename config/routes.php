<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function (){
    return '';
});

// test
Router::addGroup('/test', function (){
    /** @see \App\Controller\TestController::index() */
    Router::get('/index', 'App\Controller\TestController@index');
    /** @see \App\Controller\TestController::testSend() */
    Router::get('/testSend', 'App\Controller\TestController@testSend');
});

// mall
Router::addGroup('/mall', function (){
    // 创建入驻人员信息
    /** @see ChatUsersController::create() */
    Router::post('/chat/chat-user', 'App\Controller\Chat\ChatUsersController@create');

    // 获取入驻人员列表
    /** @see ChatUsersController::retrieve() */
    Router::get('/chat/chat-user', 'App\Controller\Chat\ChatUsersController@retrieve');

    // 更新入驻人员信息
    /** @see ChatUsersController::update() */
    Router::put('/chat/chat-user/{id}', 'App\Controller\Chat\ChatUsersController@update');

    // 获取入驻用户信息
    /** @see ChatUsersController::delete() */
    Router::delete('/chat/chat-user/{id}', 'App\Controller\Chat\ChatUsersController@delete');

    // 获取入驻用户信息
    /** @see ChatUsersController::info() */
    Router::get('/chat/chat-user-info', 'App\Controller\Chat\ChatUsersController@info');


    // ==================================================

    // 获取会话列表
    /** @see ChatConversationsController::retrieve() */
    Router::get('/chat/chat-conversations', 'App\Controller\Chat\ChatConversationsController@retrieve');

    // 获取会话总览信息
    /** @see ChatConversationsController::overview() */
    Router::get('/chat/chat-conversations-overview', 'App\Controller\Chat\ChatConversationsController@overview');

    // 检查会话
    /** @see ChatConversationsController::checkConversation() */
    Router::get('/chat/check-conversation', 'App\Controller\Chat\ChatConversationsController@checkConversation');


    // ==================================================

    // 获取聊天内容管理列表
    /** @see ChatConversationContentsController::conversationContents() */
    Router::get('/chat/chat-conversation-contents', 'App\Controller\Chat\ChatConversationContentsController@conversationContents');


});

// websocket
Router::addServer('ws', function (){
    Router::get('/ws', 'App\Server\WebSocketController');
});
