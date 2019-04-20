<?php

namespace app\admin\model\lover;

use think\Model;

class Formid extends Model
{
    // 表名
    protected $name = 'lover_user_formid';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'expiretime_text'
    ];
    

    



    public function getExpiretimeTextAttr($value, $data)
    {
        $cur_time = time();
        return $cur_time>=$data['expiretime']? 'expired':'canuse';
    }

    protected function setExpiretimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

}
