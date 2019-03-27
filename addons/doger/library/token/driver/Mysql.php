<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/1
 * Time: 23:10
 */

namespace addons\doger\library\token\driver;


use addons\doger\library\token\Driver;

class Mysql extends Driver
{
    protected $options = [
      'table' => 'lover_user_token',
      'expire' => 2592000,
      'connection' => [],
    ];

    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options,$options);
        }

        if ($this->options['connection']) {
            $this->handler = \think\Db::connect($this->options['connection'])->name($this->options['table']);
        } else {
            $this->handler = \think\Db::name($this->options['table']);
        }
    }

    public function set($token, $user_id, $expire = null)
    {
        $expiretime = !is_null($expire) && $expire !==0 ? time()+$expire: 0;
        $token = $this->getEncryptedToken($token);
        $this->handler->insert(
            [
                'token' => $token,
                'user_id' => $user_id,
                'createtime' => time(),
                'expiretime' => $expiretime
            ],true);
        return true;
    }

    public function get($token)
    {
        $data = $this->handler->where('token',$this->getEncryptedToken($token))->find();
        if($data){
            if(!$data['expiretime'] || $data['expiretime'] > time()){
                $data['token'] = $token;
                $data['expires_in'] = $this->getExpiredIn($data['expiretime']);
                return $data;
            } else {
                self::delete($token);
            }
        }
        return [];
    }

    public function check($token,$user_id)
    {
        $data = $this->get($token);
        return $data && $data['user_id'] == $user_id ? true:false;
    }

    public function delete($token)
    {
        $this->handler->where('token',$this->getEncryptedToken($token))->delete();
        return true;
    }

    public function clear($user_id)
    {
        $this->handler->where('user_id',$user_id)->delete();
        return true;
    }
}