<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/6
 * Time: 3:57
 */

namespace addons\lover\model;


use think\Model;

class UserInfo extends Model
{
    protected $name = "lover_user_info";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $type = [
        'gender' => 'integer'
    ];

}