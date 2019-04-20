<?php

namespace app\admin\model\lover;

use think\Model;

class Basicinfo extends Model
{
    // 表名
    protected $name = 'lover_user_basicinfo';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'gender_text',
        'isabroad_text',
        'highestdegree_text',
        'annualincome_text',
        'marital_text'
    ];

    protected static function init()
    {
        Basicinfo::event('before_write', function($basic) {
            $homeland = $basic->data['homeland'];
            $living = $basic->data['living'];
            $homearray = explode("/",$homeland);
            $livingarray = explode("/",$living);
            $test = $homearray[0];
            $basic->homeland_province = $homearray[0];
            $basic->homeland_city = $homearray[1];
            $basic->living_province = $livingarray[0];
            $basic->living_city = $livingarray[1];
        });
    }

    public function getGenderList()
    {
        return ['0' => __('Gender 0'),'1' => __('Gender 1'),'2' => __('Gender 2')];
    }     

    public function getIsabroadList()
    {
        return ['0' => __('Isabroad 0'),'1' => __('Isabroad 1')];
    }
    //设置星座属性
    protected  function setConstellationAttr($value,$data)
    {
        return get_constellation($data['birthday']);
    }
    public function getHighestdegreeList()
    {
        return ['0' => __('Highestdegree 0'),'1' => __('Highestdegree 1'),'2' => __('Highestdegree 2')];
    }     

    public function getAnnualincomeList()
    {
        return ['0' => __('Annualincome 0'),'1' => __('Annualincome 1'),'2' => __('Annualincome 2'),'3' => __('Annualincome 3'),'4' => __('Annualincome 4')];
    }     

    public function getMaritalList()
    {
        return ['0' => __('Marital 0'),'1' => __('Marital 1')];
    }     


    public function getGenderTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['gender']) ? $data['gender'] : '');
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getHomelandAttr($value, $data)
    {
        $value = $data['homeland_province'] . '/' . $data['homeland_city'] .'/'.'无';
        return $value;
    }

    public function getLivingAttr($value, $data)
    {
        $value = $data['living_province'] . '/' . $data['living_city'] .'/'.'无';
        return $value;
    }

    public function getIsabroadTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['isabroad']) ? $data['isabroad'] : '');
        $list = $this->getIsabroadList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getHighestdegreeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['highestdegree']) ? $data['highestdegree'] : '');
        $list = $this->getHighestdegreeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAnnualincomeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['annualincome']) ? $data['annualincome'] : '');
        $list = $this->getAnnualincomeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getMaritalTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['marital']) ? $data['marital'] : '');
        $list = $this->getMaritalList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
