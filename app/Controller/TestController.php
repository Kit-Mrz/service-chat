<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Factories\Services;
use App\Traits\ServicesTrait;

class TestController extends AbstractController
{
    use ServicesTrait;

    public function index()
    {
        $user   = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        $queueService = Services::queueService();

        $queueService->push(
            [
                "id"      => uniqid(),
                "content" => "Hello !",
            ], 3);

        return [
            'method'  => $method,
            'message' => "Hello {$user}.",
        ];
    }

    public function testSend()
    {
        $user_id = $this->request->input('user_id', 18);

        $rs = $this->getServices()->queueService()->push(['user_id' => $user_id], 5);

        return [
            'method'  => 'testSend',
            'user_id' => $user_id,
            'message' => $rs,
            'APP_ENV' => env('APP_ENV'),
        ];
    }
}
