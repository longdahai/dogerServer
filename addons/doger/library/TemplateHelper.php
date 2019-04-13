<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/4/14
 * Time: 3:30
 */

namespace addons\doger\library;


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

    protected function send($openid, $tpid, $page, $formid, $data)
    {

    }

    public function certFailed($user){

    }
}