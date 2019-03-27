<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class Userfriend extends Model
{
    protected $name = "lover_user_friend";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $hidden = ['id'];
//    // 状态读取
//    protected function getStatusAttr($value)
//    {
//        $data = [0 => '待处理',1 => '已查看',2 => '已拒绝',3 => '已通过',4 => '互相喜欢'];
//        return $data[$value];
//    }


}