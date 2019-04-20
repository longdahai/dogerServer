<?php

namespace app\admin\model\lover;

use think\Model;

class Usercard extends Model
{
    // 表名
    protected $name = 'lover_user_card';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'type_text',
        'topic_text',
        'isshow_text',
        'isdefault_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getTypeList()
    {
        return ['text' => __('Type text'),'images' => __('Type images'),'question' => __('Type question')];
    }     

    public function getTopicList()
    {
        return ['about' => __('Topic about'),'edu' => __('Topic edu'),'family' => __('Topic family'),'half' => __('Topic half'),'love' => __('Topic love'),'hobby' => __('Topic hobby')];
    }     

    public function getIsshowList()
    {
        return ['0' => __('Isshow 0'),'1' => __('Isshow 1')];
    }     

    public function getIsdefaultList()
    {
        return ['0' => __('Isdefault 0'),'1' => __('Isdefault 1')];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTopicTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['topic']) ? $data['topic'] : '');
        $list = $this->getTopicList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsshowTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['isshow']) ? $data['isshow'] : '');
        $list = $this->getIsshowList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsdefaultTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['isdefault']) ? $data['isdefault'] : '');
        $list = $this->getIsdefaultList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
