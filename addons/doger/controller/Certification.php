<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/9
 * Time: 19:44
 */

namespace addons\doger\controller;
use addons\doger\model\Usercertification as UsercertificationModel;
use fast\Random;

class Certification extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 用户认证信息
     */
    public function index(){
        $user_id = $this->auth->__get('id');
        $type = $this->request->param('type');
        if(isset($type)){
            $data = UsercertificationModel::get(function($query) use ($user_id,$type){
                $query->where(['lover_user_id' => $user_id,'type' => $type]);
            });
            $u = new UsercertificationModel();
        } else {
            $data = UsercertificationModel::all(['lover_user_id' => $user_id]);
        }

        if($data) {
            $this->success('success',$data);
        } else {
            $this->error('failed',$data);
        }
    }

    /**
     * 用户认证信息
     */
    public function get(){
        $this->index();
    }

    /**
     * 添加
     */
    public function add(){

    }

    /**
     * 编辑
     */
    public function edit($content='',$type='idcard'){
        $user_id = $this->auth->__get('id');
        $data = $this->request->param();
        $type = isset($data['type'])? $data['type']:$type;
        $content = isset($data['content'])? $data['content']:$content;
        if(isset($type)){
//            $data['content'] = json_encode($content,JSON_UNESCAPED_UNICODE);
            $u = new UsercertificationModel();
            $res = $u->where(['lover_user_id'=>$user_id,'type'=>$type])->select();
            if($res) {
                $res = $u->allowField(true)->save($data,['lover_user_id'=>$user_id,'type'=>$type]);
            } else {
                $res = $u->allowField(true)->save($data);
            }
            if($res) {
                $this->success('success');
            }
        } else {
            $this->error('params error');
        }
    }

    public function sendCode(){
        $phone = $this->request->param('phone');
//        $code = Random::numeric();
//        $res = $this->sendSms($phone,$code);
        $code = 8888;
        $res = true;
        if($res){
            $this->success('success',$code);
        } else {
            $this->error('faild',$res);
        }
    }


    public function verifyIdcard(){
        $user_id = $this->auth->__get('id');
        $idNo = $this->request->param('idno');
        $name = $this->request->param('name');

        $res = $this->idenAuthentication($idNo,$name);

        if($res['respCode'] == "0000"){
            $this->edit($res,'idcard');
        } else {
            $this->error('not match');
        }
    }


    /**
     * 删除
     */
    public function del(){

    }

    /**
     * 调用第三方身份认证接口
     * @param $id
     * @param $name
     * @return mixed
     */
    protected function idenAuthentication($id,$name){
        $host = "https://idenauthen.market.alicloudapi.com";
        $path = "/idenAuthentication";
        $method = "POST";
        $appcode = "f32b77339164491b9ba86a65e93535f8";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys = "idNo=".$id."&name=".urlencode($name);
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200){
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }
        $res = json_decode($body,true);
        return $res;
    }

    /**
     * 发送短信验证码
     */
    protected function sendSms($phone,$code) {

        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIVfRHhIUJw787";
        $accessKeySecret = "86p6QC4Zij5INXvXDwNaUg6AKc3KDV";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phone;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "短信签名";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_155370091";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => $code
        );

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );

        return $content;
    }
}