<?php

namespace App\Repositories;

use App\Model\ChatUsers;

class ChatUsersRepository extends Repository
{
    protected $model;

    public function __construct(ChatUsers $model)
    {
        $this->model = $model;
    }

    /**
     * @desc 根据用户ID获取用户
     * @param $id 用户ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model|null
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->model->newQuery()->find($id, $columns);
    }

    /**
     * @desc 创建用户信息
     * @param array $data
     * @return int
     */
    public function create(array $data)
    {
        return $this->model->newQuery()->insertGetId($data);
    }

    /**
     * @desc 删除用户信息
     * @param int $id
     * @return false|int|mixed
     */
    public function delete(int $id)
    {
        return $this->model->newQuery()->where('id', $id)->delete();
    }

    /**
     * @desc 获取用户
     * @param int $platform 平台类型
     * @param int $platformUserId 平台用户ID
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUserViaPlatformUserId(int $platform, int $platformUserId, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        $where = [
            'platform'         => $platform,
            'platform_user_id' => $platformUserId,
            'status'           => 1,
        ];

        $list = $query->select($columns)->where($where)->first();

        return $list;
    }

    /**
     * @desc 获取平台用户类型
     * @param int $platform 平台类型
     * @param string $userType 用户类型
     * @param int $status 禁启状态
     * @param int $pageIndex 页码
     * @param int $pageSize 页大小
     * @param array|string[] $columns
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function getPlatformUserType(int $platform, string $userType, int $status, int $pageIndex, int $pageSize, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        $users = $query->select($columns)
            ->where('platform', $platform)
            ->where('status', $status)
            ->where('user_type', $userType)
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->orderByDesc('id')
            ->get();

        return $users;
    }

    /**
     * @desc 获取平台用户类型总数
     * @param int $platform 平台
     * @param string $userType 用户类型
     * @param int $status 禁启状态
     * @return int
     */
    public function getPlatformUserTypeCount(int $platform, string $userType, int $status)
    {
        $query = $this->model->newQuery();

        $count = $query->select('*')
            ->where('platform', $platform)
            ->where('status', $status)
            ->where('user_type', $userType)
            ->count();

        return $count;
    }
}
