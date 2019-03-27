<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/1
 * Time: 23:11
 */

namespace addons\doger\library;


use addons\doger\model\User;
use addons\doger\model\Userbasicinfo;
use fast\Http;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Log;
use think\Request;

class Auth
{
    protected static $instance = null;
    protected $_error = '';
    protected $_logined = false;
    protected $_user = null;
    protected $_token = '';

    protected $keeptime = 2592000;
    protected $requestUri = '';
    protected $rules = [];

    protected $config = [];
    protected $options = [];
    protected $allowFields = ['nickname','avatar','session_key','is_new','status','gender','cert_status','applytimes'];

    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    public function init($token)
    {
        if ($this->_logined)
            return true;

        if ($this->_error)
            return false;

        $data = Token::get($token);

        if (!$data) {
            //token不存在，尝试code登录
            Log::write('token登录失败,尝试code登录，token:'.$token,'info');
            if($this->login()){
                return true;
            } else {
                return false;
            }
        }

        $user_id = intval($data['user_id']);
        if ($user_id > 0) {
            $user = User::get($user_id);

            if (!$user) {
                $this->setError('User not exist');
                return false;
            }

            $this->_user = $user;
            $this->_logined = true;
            $this->_token = $token;

            Hook::listen("user_init_successed", $this->_user);

            return true;
        } else {
            $this->setError('no right');
            return false;
        }
    }

    public function login()
    {
        $config = get_addon_config('doger');
        $code = Request::instance()->param('code');

        $rawData = Request::instance()->param('rawData');
        //记录请求参数
        Log::write('换取微信openid,code is:'.$code.'  rawData is：'.$rawData,'info');

        if (!$code || !$rawData) {
            $this->setError("参数不正确");
        }

        $userInfo = (array)json_decode($rawData, true);

        $params = [
            'appid' => $config['wxappid'],
            'secret' => $config['wxappsecret'],
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET');
        //记录微信登录接口返回
        Log::write('微信登录返回： '.$result['msg'],'info');

        if (isset($result['ret'])) {
            $json = (array)json_decode($result['msg'], true);
            if (isset($json['openid'])) {
                $data = [
                    'openid' => $json['openid'],
                    'unionid' => isset($json['unionid'])? $json['unionid']:'',
                    'session_key' => $json['session_key'],
                    'gender' => $userInfo['gender'],
                    'nickname' => $userInfo['nickName'],
                    'avatar' => $userInfo['avatarUrl'],
                    'country' => $userInfo['country'],
                    'province' => $userInfo['province'],
                    'city' => $userInfo['city']
                ];
                //如果存在用户，则更新
                $user = User::get(['openid' => $json['openid']]);
                if ($user) {
                    $extend = [
                        'id' => $user['id']
                        ];
                    $data = array_merge($extend, $data);
                }

                Db::startTrans();
                try {
                    if ($user) {
                        $user = User::update($data);
                    } else {
                        //新增用户
                        $user = User::create($data);
                    }
                    Db::commit();
                    $this->_user = User::get($user->id);

                    $this->_token = Random::uuid();
                    Token::set($this->_token, $user->id, $this->keeptime);

                    //注册成功的事件
                    $this->init($this->_token);
                    return true;
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    Db::rollback();
                    return false;
                }
            }
        } else {
            $this->setError($result);
            return false;
        }
    }


    /**
     * 获取允许输出的字段
     * @return array
     */
    public function getAllowFields()
    {
        return $this->allowFields;
    }

    /**
     * 设置允许输出的字段
     * @param array $fields
     */
    public function setAllowFields($fields)
    {
        $this->allowFields = $fields;
    }
    public function getUser()
    {
        $data = $this->_user->toArray();
        $allowFields = $this->getAllowFields();
        $userinfo = array_intersect_key($data, array_flip($allowFields));
        $userinfo = array_merge($userinfo, Token::get($this->_token));
        return $userinfo;
    }

    public function __get($name)
    {
        return $this->_user ? $this->_user->$name : null;
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return FALSE;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr))
        {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }

    /**
     * 设置会话有效时间
     * @param int $keeptime 默认为永久
     */
    public function keeptime($keeptime = 0)
    {
        $this->keeptime = $keeptime;
    }

    /**
     * 设置错误信息
     *
     * @param $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }
    /**
     * 判断是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->_logined)
        {
            return true;
        }
        return false;
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

}