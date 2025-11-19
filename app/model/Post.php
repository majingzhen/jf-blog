<?php
// app/model/Post.php

namespace app\model;

use think\Model;

class Post extends Model
{
    protected $pk = 'id';
    protected $table = 'posts';

    public $timestamps = true;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 定义关联的分类 (一篇文章属于一个分类)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // 定义关联的标签 (多对多关系)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'tag_id', 'post_id');
    }

    // 定义状态获取器，返回中文描述
    public function getStatusTextAttr($value, $data)
    {
        $status = ['draft' => '草稿', 'published' => '已发布'];
        return $status[$data['status']] ?? '未知';
    }

    // 定义发布时间获取器，格式化输出
    public function getPublishedAtTextAttr($value, $data)
    {
        $publishedAt = $data['published_at'];
        return $publishedAt ? date('Y-m-d H:i:s', strtotime($publishedAt)) : '未发布';
    }
}