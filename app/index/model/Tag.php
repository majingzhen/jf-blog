<?php
// app/model/Tag.php

namespace app\model;

use think\Model;

class Tag extends Model
{
    protected $pk = 'id';
    protected $table = 'tags';

    public $timestamps = true;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 定义关联的文章 (多对多关系)
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'post_id', 'tag_id');
    }
}