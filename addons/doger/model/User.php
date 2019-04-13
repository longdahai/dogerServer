<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class User extends Model
{
    protected $name = "lover_user";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $hidden = ['openid','unionid','updatetime'];
    //用户基本资料
    public function basicinfo(){
        return $this->hasOne('Userbasicinfo','lover_user_id','id');
    }

    //用户资料卡片
    public function card(){
        return $this->hasMany('Usercard','lover_user_id','id')->order('weigh','desc');
    }

    //用户认证信息
    public function certification(){
        return $this->hasMany('Usercertification','lover_user_id','id');
    }

    //用户标签
    public function tag(){
        return $this->hasOne('Usertag','lover_user_id','id');
    }

    //用户匹配偏好
    public function prefer(){
        return $this->hasOne('Prefer','lover_user_id','id');
    }

    //用户匹配结果
    public function match(){
        return $this->hasMany('Usermatch','lover_user_id','id');
    }

    //用户匹配历史
    public function matchhistory(){
        return $this->hasOne('Usermatchhistory','lover_user_id','id');
    }
    //用户特权表
    public function right(){
        return $this->hasOne('Userright','lover_user_id','id');
    }
}