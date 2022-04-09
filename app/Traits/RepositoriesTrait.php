<?php

namespace App\Traits;

use App\Factories\Repositories;
use Hyperf\Utils\ApplicationContext;

trait RepositoriesTrait
{
    public function getRepositories() : Repositories
    {
        return ApplicationContext::getContainer()->get(Repositories::class);
    }
}
