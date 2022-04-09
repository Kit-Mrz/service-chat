<?php

namespace App\Job;

use App\Traits\Integration;
use App\Traits\ServicesTrait;
use Hyperf\AsyncQueue\Job;
use Hyperf\Guzzle\ClientFactory;

class WechatPushJob extends Job
{
    use ServicesTrait;
    use Integration;

    public $params;

    /**
     * 任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次
     *
     * @var int
     */
    protected $maxAttempts = 2;

    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }

    public function handle()
    {
        // 根据参数处理具体逻辑
        // 通过具体参数获取模型等
        // 这里的逻辑会在 ConsumerProcess 进程中执行

        if ( !isset($this->params['user_id'])) {
            return false;
        }

        $userInfo = $this->getServices()->chatUsersService()->find($this->params['user_id'], ['id', 'platform_user_id']);
        if (empty($userInfo)) {
            return false;
        }

        // 标记任务已经被执行
        $this->getServices()->notificationService()->setUserId($this->params['user_id'])->cancel();

        $customer_id = $userInfo['platform_user_id'];

        $tpl = [
            'tag'         => 'chat_notice',
            'customer_id' => $customer_id,
            'url'         => '/packageCustomer/pages/customerService/index',
            'datas'       => [
                ['name' => '服务类型', 'value' => '人工客服咨询'],
                ['name' => '处理状态', 'value' => '客服回复了你的提问'],
                ['name' => '咨询内容', 'value' => '进行小程序查看'],
                ['name' => '温馨提示', 'value' => '愿所有美好都在这一天发生'],
            ],
        ];

        if (env('APP_ENV') == 'prod') {
            $base_uri = 'http://api-mall.yidejia.com';
        } else {
            $base_uri = 'http://test-mall.yidejia.com';
        }

        /** @var ClientFactory $clientFactory */
        $clientFactory = app(ClientFactory::class);

        $client = $clientFactory->create(['base_uri' => $base_uri, 'verify' => false]);

        $resp = $client->request('post', '/api/public/send-tplnotice?sign=kK6FVdy9JPAfqAJaQQn7puGjXr9QTHM8', ['form_params' => $tpl]);

        $content = $resp->getBody()->getContents();

        $content = json_decode($content, true);

        return $content['code'] == 200;
    }
}
