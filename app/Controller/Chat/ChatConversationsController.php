<?php

namespace App\Controller\Chat;

use App\Controller\AbstractController;
use App\Model\ChatConversations;
use App\Traits\ServicesTrait;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Rule;

class ChatConversationsController extends AbstractController
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
     * 获取会话列表
     *
     * api      {get} /mall/chat/chat-conversations
     * apiName  获取会话列表
     *
     * apiParam {integer} deal_user_id 员工Id
     * apiParam {integer} status 会话状态(0=全部;1=已回复;2=未回复;3=未读;4=已接待;5=接待中;6=排队中)
     * apiParam {integer} page_index 分页页码
     * apiParam {integer} page_size 分页大小
     *
     * apiSuccess {object} data={App\Models\Chat\ChatConversation}
     * apiSuccess {object} data.customer_user={App\Models\Chat\ChatUser}
     * apiSuccess {object} data.staff_user={App\Models\Chat\ChatUser}
     * apiSuccess {object} data.last_content={App\Models\Chat\ChatConversationContent}
     * apiSuccess {object} data.last_content.format_created_at 最后一句会话时间(m-d H:i)
     */
    public function retrieve(RequestInterface $request)
    {
        $statusTranslate = array_keys(ChatConversations::statusTranslate());

        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'deal_user_id' => 'required|int|min:1',
                'status'       => ['nullable', Rule::in($statusTranslate)],
                'page_index'   => 'int|min:1',
                'page_size'    => 'int|min:1',
            ],
            [
                'deal_user_id.required' => 'deal_user_id is required',
                'deal_user_id.int'      => 'deal_user_id is int',
                'type.nullable'         => 'type is nullable',
                'status.int'            => 'status is int',
                'page_index.int'        => 'page_index is int',
                'page_size.int'         => 'page_size is int',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $status     = $params['status'];
        $dealUserId = $params['deal_user_id'];
        $pageIndex  = $params['page_index'];
        $pageSize   = $params['page_size'];

        //会话状态
        switch ($status) {
            //0=全部;
            case 0:
                $list = [
                    //4=已接待;
                    4 => $this->getServices()->chatConversationsService()->retrieveStatusAlreadyReception($dealUserId, $pageIndex, $pageSize),
                    //5=接待中;
                    5 => $this->getServices()->chatConversationsService()->retrieveStatusReception($dealUserId, $pageIndex, $pageSize),
                    //6=排队中
                    6 => $this->getServices()->chatConversationsService()->retrieveStatusQueueing($pageIndex, $pageSize),
                ];
                break;
            //1=已回复;
            case 1:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusAlreadyReply($dealUserId);
                break;
            //2=未回复;
            case 2:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusNotReply($dealUserId);
                break;
            //3=未读;
            case 3:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusNotRead($dealUserId);
                break;
            //4=已接待;
            case 4:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusAlreadyReception($dealUserId, $pageIndex, $pageSize);
                break;
            //5=接待中;
            case 5:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusReception($dealUserId, $pageIndex, $pageSize);
                break;
            //6=排队中
            case 6:
                $list = $this->getServices()->chatConversationsService()->retrieveStatusQueueing($pageIndex, $pageSize);
                break;
        }

        return success(
            [
                'data' => $list,
                'status' => $status,
                'deal_user_id' => $dealUserId,
            ]
            , 'success');
    }

    /**
     * 获取会话总览信息
     *
     * api      {get} /mall/chat/chat-conversations-overview
     * apiName  获取会话总览信息
     *
     * apiParam {integer} user_id 用户ID
     * apiParam {integer} platform 平台类型
     *
     * apiSuccess {object} data.user_info={App\Models\Chat\ChatUser}
     * apiSuccess {integer} is_dealing_count 接待中的人数
     * apiSuccess {integer} is_ranking_count 排队中的人数
     */
    public function overview(RequestInterface $request)
    {
        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'user_id'  => 'required|int|min:1',
                'platform' => 'required|int|min:1',
            ],
            [
                'user_id.required' => 'user_id is required',
                'user_id.int'      => 'user_id is int',
                'user_id.min'      => 'user_id is min 1',

                'platform.required' => 'platform is required',
                'platform.int'      => 'platform is int',
                'platform.min'      => 'platform is min 1',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $user_id  = $params['user_id'];
        $platform = $params['platform'];

        $overview = $this->getServices()->chatConversationsService()->overview($user_id, $platform);

        return success($overview, 'overview');
    }

    /**
     * 检查会话
     *
     * api      {get} /mall/chat/check-conversation
     *
     * apiName  检查会话
     * apiSuccess {bool} has_conversation 是否有未结束的会话(false=无;true=有)
     * apiSuccess {integer} conversation_id 会话Id
     */
    public function checkConversation(RequestInterface $request)
    {
        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'platform' => 'required|int',
                'platform_user_id' => 'required|int',
            ],
            [
                'user_id.required' => 'user_id is required',
                'user_id.int'      => 'user_id is int',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $platform = $params['platform'] ?? 0;
        $platform_user_id = $params['platform_user_id'] ?? 0;

        $hasConversation = $this->getServices()->chatConversationsService()->checkConversation($platform, $platform_user_id);

        return success(
            [
                'has_conversation' => $hasConversation ? true : false,
                'conversation_id'  => $hasConversation->id ?? 0,
            ], 'success');
    }
}
