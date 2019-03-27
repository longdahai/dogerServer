<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class Usercertification extends Model
{
    protected $name = "lover_user_certification";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $hidden = ['lover_user_id','id','createtime','updatetime'];

    //返回title
    protected  function getUserTitleAttr($value,$data)
    {
        $lists = [0 => '未认证',1 => '认证中',2 => '已通过',3 => '未通过'];
        return '1111';
    }
}