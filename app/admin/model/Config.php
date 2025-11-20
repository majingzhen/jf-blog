<?php
// app/model/Config.php

namespace app\model;

use think\Model;

class Config extends Model
{
    protected $pk = 'id';
    protected $table = 'configs';

    public $timestamps = true;
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 通常配置值可能需要序列化为 JSON 存储，这里暂时直接存储字符串
    // 如果需要处理复杂数据结构，可以在获取/设置时进行序列化
    // protected $json = ['value']; // 如果 value 字段是 JSON 格式
    // protected $type = ['value' => 'array']; // 如果 value 需要作为数组处理
}