<?php

namespace App\Controller\Chat;

use App\Controller\AbstractController;
use App\Traits\ServicesTrait;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class ChatConversationContentsController extends AbstractController
{
    use ServicesTrait;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    // 没有用的
    protected $inject = Inject::class;

    /**
     * 获取聊天内容管理列表
     *
     * api      {get} /mall/chat/chat-conversation-contents
     * apiName  获取聊天内容管理列表
     *
     * apiParam {integer} conversation_id 会话Id
     * apiParam {integer} id 根据Id分页
     * apiParam {integer} page_size 分页大小
     *
     * apiSuccess {object[]} data
     * apiSuccess {string} data.nickname 用户昵称
     * apiSuccess {string} data.avatar 用户头像
     * apiSuccess {integer} data.platform_user_id 用户平台ID
     * apiSuccess {string} data.user_type 用户类型，客服 staff 或用户 customer
     * apiSuccess {integer} data.chat_conversation_contents 聊天内容主键
     * apiSuccess {integer} data.content_type 聊天类型
     * apiSuccess {string} data.content 聊天内容
     * apiSuccess {object} data.created_at 聊天内容时间
     */
    public function conversationContents(RequestInterface $request)
    {
        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'conversation_id' => 'required|int|min:1',
                'id'              => 'int|min:0',
                'page_size'       => 'int|min:1',
            ],
            [
                'conversation_id.required' => 'conversation_id is required',
                'conversation_id.int'      => 'conversation_id is int',
                'conversation_id.min'      => 'conversation_id is min 1',

                'id.int' => 'id is int',
                'id.min' => 'id is min 1',

                'page_size.int'  => 'page_size is int',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $conversationId = $params['conversation_id'];
        $id             = $params['id'] ?? 0; // id = 0 即从第一页开始
        $pageSize       = $params['page_size'] ?? 20;

        $list = $this->getServices()->chatConversationContentsService()->contentsPaging($conversationId, $id, $pageSize);

        return success(
            [
                'conversation_contents' => $list,
                'count'                 => count($list),
                'page_size'             => $pageSize,
            ], 'success');
    }
}
