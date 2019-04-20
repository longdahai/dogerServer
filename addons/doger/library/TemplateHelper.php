<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/4/14
 * Time: 3:30
 */

namespace addons\doger\library;


use addons\doger\model\Formid;
use addons\doger\model\User;
use EasyWeChat\Factory;

class TemplateHelper
{
    protected $app;
    protected $request;
    public function __construct()
    {
        $config = get_addon_config('doger');
        $params = [
            'app_id' => $config['wxappid'],
            'secret' => $config['wxappsecret'],
            'response_type' => 'array',
        ];
        $this->app = Factory::miniProgram($params);
    }

    public function getlist($offset=0,$count=20) {
        $t = $this->app->template_message->list($offset,$count);
        return $t;
    }
    public function getbyid($id) {
        $t = $this->app->template_message->get($id);
        return $t;
    }
    public function gettps($offset=0,$count=20) {
        $t = $this->app->template_message->getTemplates($offset,$count);
        return $t;
    }
    public function add($id, $keywordIdList) {
        $t = $this->app->template_message->add($id, $keywordIdList);
        return $t;
    }
    public function del($tpid) {
        $t = $this->app->template_message->delete($tpid);
        return $t;
    }

    public function sendMessage($uid, $category="cert"){
        $user = User::get($uid);
        $tpid = "0L1utVwKLmKKVtPMh8-L_HJn0ZBxBcvvTCd73gTcfBg";
        $f = Formid::get(['user_id' => $uid]);
        $formid = $f['form_id'];
        $data = [
            "keyword1"=> [
                "value"=>"身份及资料认证"
            ],
            "keyword2"=> [
                "value"=>"未通过"
            ],
            "keyword3"=> [
                "value"=>"图像模糊不清"
            ],
            "keyword4"=> [
                "value"=>"请置于光线充足的地方，相机聚焦拍摄！"
            ]
        ];
        $message_to_be_send = [
            "touser" => $user['openid'],
            "template_id" => $tpid,
            "form_id" => $formid,
            "data" => $data
        ];
        $r = $this->app->template_message->send($message_to_be_send);
    }
}