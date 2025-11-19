<?php
// app/model/Category.php

namespace app\model;

use think\Model;

class Category extends Model
{
    protected $pk = 'id';
    protected $table = 'categories';

    public $timestamps = true;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 定义关联的文章 (一个分类可以有多个文章)
    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }
}