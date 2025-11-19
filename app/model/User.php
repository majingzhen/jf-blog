<?php
// app/model/User.php

namespace app\model;

use think\Model;

class User extends Model
{
    protected $pk = 'id';
    protected $table = 'users'; // 确保表名与数据库一致

    // 可以添加自动时间戳等设置
    public $timestamps = true;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 定义加密密码的修改器
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    // 定义关联的文章 (一个用户可以有多个文章 - 如果有作者概念的话，这里简化为只有一个管理员)
    // public function posts()
    // {
    //     return $this->hasMany(Post::class, 'author_id', 'id');
    // }
}