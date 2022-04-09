<?php

namespace App\Controller\Chat;

use App\Controller\AbstractController;
use App\Traits\ServicesTrait;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class ChatUsersController extends AbstractController
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
     * 创建入驻人员信息
     *
     * api      {post} /mall/chat/chat-user
     * apiName  创建入驻人员信息
     * apiParam {integer} platform 用户平台(0=未知;1=伊密心选;2=小程序商城)
     * apiParam {integer} platform_user_id 平台用户Id
     * apiParam {string} user_type 用户类型(staff=员工;customer=用户)
     * apiParam {string} user_role 用户角色(0=未知;1=业务导师;2=专业组)
     * apiParam {integer} status 用户状态(0=禁用;1=启用)
     * apiParam {string} nickname 用户昵称
     * apiParam {string} avatar 头像
     * apiParam {string} mobile 手机号
     * apiParam {string} describe 头衔
     * apiParam {string} real_name 真实名称
     * apiSuccess {object} data={App\Models\Chat\ChatUser}
     */
    public function create(RequestInterface $request)
    {
        $params    = $request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'platform'         => 'required|int|min:1',
                'platform_user_id' => 'required|int|min:1',
                'user_type'        => 'required|string',
                'user_role'        => 'required|int',
                'status'           => 'required|int',
                'nickname'         => 'required|string',
                'mobile'           => 'string',
                'avatar'           => 'required|string',
                'describe'         => 'string',
                'real_name'        => 'string',
            ],
            [
                'platform'         => 'platform is required',
                'platform_user_id' => 'platform_user_id is required',
                'user_type'        => 'user_type is required',
                'user_role'        => 'user_role is required',
                'status'           => 'status is required',
                'nickname'         => 'nickname is required',
                'avatar'           => 'avatar is required',
                'real_name'        => 'string',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $data = [
            'platform'         => $params['platform'],
            'platform_user_id' => $params['platform_user_id'],
            'user_type'        => $params['user_type'],
            'user_role'        => $params['user_role'],
            'status'           => $params['status'],
            'nickname'         => $params['nickname'],
            'avatar'           => $params['avatar'],
            'mobile'           => $params['mobile'] ?? '',
            'describe'         => $params['describe'] ?? '',
            'real_name'        => $params['real_name'] ?? '',
        ];

        $chatUserId = $this->getServices()->chatUsersService()->createChatUser($data);

        return success(['chat_user_id' => $chatUserId], 'success');
    }

    /**
     * 获取入驻人员列表
     *
     * api      {get} /mall/chat/chat-user
     * apiName  获取入驻人员列表
     * apiParam {string} platform 用户平台(0=未知;1=伊密心选;2=小程序商城)
     * apiParam {string} user_type 用户类型(staff=员工Id;customer=用户Id)
     * apiParam {integer} status 分页页码
     * apiParam {integer} page_index 分页页码
     * apiParam {integer} page_size 分页大小
     * apiSuccess {string} platform 用户平台(0=未知;1=伊密心选)
     * apiSuccess {string} user_type 用户类型(staff=员工;customer=用户;wx=微信用户;)
     * apiSuccess {integer} platform_user_id 员工Id
     * apiSuccess {integer} user_role 用户角色(0=未知;1=业务导师;2=专业组)
     * apiSuccess {integer} status 用户状态(0=禁用;1=启用)
     * apiSuccess {string} nickname 用户昵称
     * apiSuccess {string} avatar 用户昵称
     * apiSuccess {string} mobile 手机号
     * apiSuccess {string} real_name 员工名称
     * apiSuccess {integer} dealing_count 接待中数量
     * apiSuccess {integer} deal_count 已接待
     * apiSuccess {integer} wait_count 排队中数量
     */
    public function retrieve(RequestInterface $request)
    {
        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'platform'   => 'required|int',
                'user_type'  => 'required|string',
                'status'     => 'int',
                'page_index' => 'required|int',
                'page_size'  => 'required|int',
            ],
            [
                'platform.required'  => 'platform is required',
                'user_type.required' => 'user_type is required',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $data = [
            'platform'   => $params['platform'],
            'user_type'  => $params['user_type'],
            'status'     => $params['status'] ?? 1,
            'page_index' => $params['page_index'],
            'page_size'  => $params['page_size'],
        ];

        $result = $this->getServices()->chatUsersService()->retrieve($data);

        return success($result, 'success');
    }

    /**
     * 更新入驻人员信息
     *
     * api      {put} /mall/chat/chat-user/{$id}
     * apiName  更新入驻人员信息
     * apiParam {integer} user_role 用户角色(0=未知;1=客服;2=业务导师;3=专业组)
     * apiParam {integer} status 用户状态(0=禁用;1=启用)
     * apiParam {string} nickname 昵称
     * apiParam {string} avatar 头像
     * apiParam {string} describe 头衔
     * apiParam {string} real_name 员工名称
     * apiSuccess {object} data={App\Models\Chat\ChatUser}
     */
    public function update(RequestInterface $request, int $id)
    {
        $params = $request->all();

        $validator = $this->validationFactory->make(
            $params,
            [
                'user_role'  => 'int',
                'status'     => 'int',
                'nickname'   => 'string',
                'avatar'     => 'string',
                'describe'   => 'string',
                'real_name' => 'string',
            ],
            [
                'user_role.int'     => 'user_role is int',
                'status.int'        => 'status is int',
                'nickname.string'   => 'nickname is string',
                'avatar.string'     => 'avatar is string',
                'describe.string'   => 'describe is string',
                'real_name.string' => 'real_name is string',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        if (isset($params['user_role'])) {
            $data['user_role'] = $params['user_role'];
        }

        if (isset($params['status'])) {
            $data['status'] = $params['status'];
        }

        if (isset($params['nickname'])) {
            $data['nickname'] = $params['nickname'];
        }

        if (isset($params['avatar'])) {
            $data['avatar'] = $params['avatar'];
        }
        if (isset($params['describe'])) {
            $data['describe'] = $params['describe'];
        }
        if (isset($params['real_name'])) {
            $data['real_name'] = $params['real_name'];
        }

        if (empty($data)) {
            return fail([], '数据不能为空');
        }

        $result = $this->getServices()->chatUsersService()->updateChatUser($id, $data);

        return success(['data' => $result], 'success');
    }

    /**
     * 删除入驻人员信息
     *
     * api      {delete} /mall/chat/chat-user/{$id}
     * apiName  删除入驻人员信息
     */
    public function delete(int $id)
    {
        $result = $this->getServices()->chatUsersService()->deleteChatUser($id);

        return success(['delete' => (int) $result], 'success');
    }

    /**
     * 获取入驻用户信息
     *
     * api      {get} /mall/chat/chat-user-info
     * apiName  获取员工入驻信息
     * apiParam {integer} platform  用户平台(0=未知;1=伊密心选;2=小程序商城)
     * apiParam {integer} platform_user_id 平台用户Id
     *
     * apiSuccess {string} platform 用户平台(0=未知;1=伊密心选;2=小程序商城)
     * apiSuccess {string} user_type 用户类型(staff=员工;customer=用户;wx=微信用户;)
     * apiSuccess {integer} platform_user_id 平台用户Id
     * apiSuccess {integer} user_role 用户角色(0=未知;1=业务导师;2=专业组)
     * apiSuccess {integer} status 用户状态(0=禁用;1=启用)
     * apiSuccess {string} nickname 用户昵称
     * apiSuccess {string} avatar 用户昵称
     * apiSuccess {string} real_name 员工名称
     * apiSuccess {string} last_login_time 上一次登录时间
     * apiSuccess {string} describe 头衔
     */
    public function info(RequestInterface $request)
    {
        $params    = $request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'platform'         => 'required|int',
                'platform_user_id' => 'required|int',
            ],
            [
                'platform_user_id.required' => 'platform_user_id is required',
                'platform_user_id.int'      => 'platform_user_id is int',

                'platform.required' => 'platform is required',
                'platform.int'      => 'platform is int',
            ]
        );

        if ($validator->fails()) {
            // Handle exception
            $errorMessage = $validator->errors()->first();

            return fail([], $errorMessage);
        }

        $platformUserId = $params['platform_user_id'];
        $platform       = $params['platform'];

        $info = $this->getServices()->chatUsersService()->getUserViaPlatformUserId($platform, $platformUserId);

        return success(
            [
                'data' => $info,
            ],
            'success'
        );
    }

}
