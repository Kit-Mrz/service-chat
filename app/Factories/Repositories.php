<?php

namespace App\Factories;

use App\Repositories\ChatConversationContentsRepository;
use App\Repositories\ChatConversationsRepository;
use App\Repositories\ChatUsersRepository;
use Hyperf\Utils\ApplicationContext;

class Repositories
{
    public function chatConversationContentsRepository() : ChatConversationContentsRepository
    {
        return ApplicationContext::getContainer()->get(ChatConversationContentsRepository::class);
    }

    public function chatConversationsRepository() : ChatConversationsRepository
    {
        return ApplicationContext::getContainer()->get(ChatConversationsRepository::class);
    }

    public function chatUsersRepository() : ChatUsersRepository
    {
        return ApplicationContext::getContainer()->get(ChatUsersRepository::class);
    }
}
