<?php

namespace App\Factories;

use App\Services\ChatConversationContentsService;
use App\Services\ChatConversationsService;
use App\Services\ChatUsersService;
use App\Services\ConnectionMapService;
use App\Services\ContentQueueService;
use App\Services\LoginService;
use App\Services\NotificationService;
use App\Services\OnlineService;
use App\Services\QueueService;
use App\Services\RoomInfoService;
use App\Services\RoomService;
use Hyperf\Utils\ApplicationContext;

class Services
{
    public function chatUsersService() : ChatUsersService
    {
        return ApplicationContext::getContainer()->get(ChatUsersService::class);
    }

    public function chatConversationsService() : ChatConversationsService
    {
        return ApplicationContext::getContainer()->get(ChatConversationsService::class);
    }

    public function chatConversationContentsService() : ChatConversationContentsService
    {
        return ApplicationContext::getContainer()->get(ChatConversationContentsService::class);
    }

    public function loginService() : LoginService
    {
        return ApplicationContext::getContainer()->get(LoginService::class);
    }

    public function roomService() : RoomService
    {
        return ApplicationContext::getContainer()->get(RoomService::class);
    }

    public function roomInfoService() : RoomInfoService
    {
        return ApplicationContext::getContainer()->get(RoomInfoService::class);
    }

    public function onlineService() : OnlineService
    {
        return ApplicationContext::getContainer()->get(OnlineService::class);
    }

    public function queueService() : QueueService
    {
        return ApplicationContext::getContainer()->get(QueueService::class);
    }

    public function connectionMapService() : ConnectionMapService
    {
        return ApplicationContext::getContainer()->get(ConnectionMapService::class);
    }

    public function contentQueueService() : ContentQueueService
    {
        return ApplicationContext::getContainer()->get(ContentQueueService::class);
    }

    public function notificationService() : NotificationService
    {
        return ApplicationContext::getContainer()->get(NotificationService::class);
    }

}
