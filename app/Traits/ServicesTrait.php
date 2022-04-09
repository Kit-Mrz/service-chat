<?php

namespace App\Traits;

use App\Factories\Services;
use Hyperf\Utils\ApplicationContext;

trait ServicesTrait
{
    public function getServices() : Services
    {
        return ApplicationContext::getContainer()->get(Services::class);
    }

}
