<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class Usertag extends Model
{
    protected $name = "lover_user_tag";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $updateTime = 'updatetime';
    protected $hidden = ['lover_user_id','id'];
}