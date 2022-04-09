<?php

namespace App\Repositories;

use App\Model\Model;

abstract class Repository
{

    /** @var Model */
    protected $model;

    // 获取模型
    public function getModel() : Model
    {
        return $this->model;
    }

    public function multiCreate(array $data)
    {
        return $this->model->newQuery()->insert($data);
    }

    // 创建
    public function create(array $data)
    {
        return $this->model->newQuery()->insertGetId($data);
    }

    // 主键更新
    public function update(int $id, array $data)
    {
        return $this->model->newQuery()->where($this->model->getKeyName(), $id)->update($data);
    }
}
