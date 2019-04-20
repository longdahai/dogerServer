<?php

namespace app\admin\model\lover;

use think\Model;

class User extends Model
{
    // 表名
    protected $name = 'lover_user';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'gender_text',
        'status_text',
        'is_new_text',
        'cert_status_text'
    ];
    

    
    public function getGenderList()
    {
        return ['1' => __('Gender 1'),'2' => __('Gender 2')];
    }     

    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'2' => __('Status 2'),'3' => __('Status 3')];
    }     

    public function getIsNewList()
    {
        return ['0' => __('Is_new 0'),'1' => __('Is_new 1')];
    }     

    public function getCertStatusList()
    {
        return ['0' => __('Cert_status 0'),'1' => __('Cert_status 1')];
    }     


    public function getGenderTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['gender']) ? $data['gender'] : '');
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsNewTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['is_new']) ? $data['is_new'] : '');
        $list = $this->getIsNewList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCertStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['cert_status']) ? $data['cert_status'] : '');
        $list = $this->getCertStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function basicinfo()
    {
        return $this->hasOne('Basicinfo', 'lover_user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
