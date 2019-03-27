<?php

namespace app\admin\model;

use think\Model;

class Tag extends Model
{
    // 表名
    protected $name = 'lover_tag';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return ['personality' => __('Type personality'),'hobby' => __('Type hobby'),'exercise' => __('Type exercise'),'food' => __('Type food'),'time' => __('Type time'),'skill' => __('Type skill'),'other' => __('Type other')];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
