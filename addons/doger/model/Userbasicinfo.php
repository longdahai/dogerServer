<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/6
 * Time: 3:57
 */

namespace addons\doger\model;


use think\Model;

class Userbasicinfo extends Model
{
    protected $name = "lover_user_basicinfo";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $type = [
        'gender' => 'integer'
    ];
    protected $hidden = ['lover_user_id','id','createtime','updatetime'];

    // 年收入读取
    protected function getAnnualincomeAttr($value)
    {
        $data = [0 => '10-30W',1 => '30-50W',2 => '50-100W',3 => '100-500W',4 => '500W以上'];
        return $data[$value];
    }

    // 年收入设置
    protected function setAnnualincomeAttr($value)
    {
        $data = [0 => '10-30W',1 => '30-50W',2 => '50-100W',3 => '100-500W',4 => '500W以上'];
        if(array_search($value,$data)){
            return array_search($value,$data);
        } else {
            return 0;
        }
    }

    //最高学历
    protected  function getHighestdegreeAttr($value)
    {
        $data = [0 => '本科',1 => '硕士',2 => '博士'];
        return $data[$value];
    }

    //设置最高学历
    protected  function setHighestdegreeAttr($value)
    {
        $data = [0 => '本科',1 => '硕士',2 => '博士'];
        return array_search($value,$data);
    }
    //设置星座属性
    protected  function setConstellationAttr($value,$data)
    {
        return get_constellation($data['birthday']);
    }

//    //获取星座属性
//    protected  function getConstellationAttr($value,$data)
//    {
//        $data = get_constellation($data['birthday']);
//        return $data;
//    }
}