<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/2
 * Time: 1:21
 */

namespace addons\doger\library;

use think\App;
use think\Config;
use think\Log;

class Token
{
    public static $instance = [];
    public static $handler;

    public static function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type'])? $options['type']:'File';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type,'\\')?
                '\\addons\\doger\\library\\token\\driver\\'.ucwords($type) :
                $type;

            App::$debug && Log::record('[ TOKEN ] INIT' . $type, 'info');

            if (true === $name){
                return new $class($options);
            }

            self::$instance['name'] = new $class($options);
        }

        return self::$instance['name'];
    }
    
    public static function init(array $options =[])
    {
        if (is_null(self::$handler)) {
            if (empty($options) && 'complex' == Config::get('token.type')) {
                $default = Config::get('token.default');

                $options = Config::get('token.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = Config::get('token');
            }
            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    public static function has($token,$user_id)
    {
        return self::check($token,$user_id);
    }

    public static function check($token,$user_id)
    {
        return self::init()->check($token,$user_id);
    }

    public static function get($token,$default = false)
    {
        return self::init()->get($token,$default);
    }

    public static function set($token, $user_id,$expire = null)
    {
        return self::init()->set($token,$user_id,$expire);
    }

    public static function rm($token)
    {
        return self::delete($token);
    }

    public static function delete($token)
    {
        return self::init()->delete($token);
    }

    public static function clear($user_id = null)
    {
        return self::init()->clear($user_id);
    }

}