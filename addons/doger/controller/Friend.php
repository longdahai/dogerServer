<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/9
 * Time: 19:44
 */

namespace addons\doger\controller;
use addons\doger\model\Userbasicinfo;
use addons\doger\model\Userfriend;
use think\Db;
use think\Exception;

class Friend extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 朋友列表
     */
    public function index(){

    }

    /**
     * 编辑
     */
    public function edit($from_id,$status){
        $user_id = $this->auth->__get('id');
        $f = new Userfriend();
        $r = $f->where(['lover_user_id'=>$from_id,'target_user_id'=>$user_id])->find();
        if($r){
            $res = $r->save(['status'=>$status]);
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 申请-想认识
     * @param $user_id
     */
    public function apply(){
        $target_id = $this->request->param('userid');
        $user_id = $this->auth->__get('id');
        if(!isset($target_id)) {
            $this->error("params error");
        }
        $data = $this->request->param();

        if(isset($data['message'])) {
            $data['messagecontent'] = $data['message'];
        } else {
            $data['messagecontent'] = "你好，我想认识你！";
        }

        $data['lover_user_id'] = $user_id;
        $data['target_user_id'] = $target_id;
        $f = new Userfriend();
        $r = $f->allowField(true)->save($data);

        if($r) {
            \addons\doger\model\User::get($user_id)->setDec('applytimes',1);
            $this->success('success');
        } else {
            $this->error('failed!');
        }
    }

    /**
     * 接受
     * @param $user_id
     */
    public function accept(){
        $from_id = $this->request->param('userid');
        $r = $this->edit($from_id,3);
        // 模板消息推送
        if($r){
            $this->success('success');
        } else {
            $this->success('friend not found');
        }
    }

    /**
     * 拒绝
     * @param $user_id
     */
    public function reject(){
        $from_id = $this->request->param('userid');
        $r = $this->edit($from_id,2);
        // 模板消息推送
        if($r){
            $this->success('success');
        } else {
            $this->success('friend not found');
        }
    }

    /**
     * 查看
     * @param $user_id
     */
    public function watch(){
        $from_id = $this->request->param('userid');
        $r = $this->edit($from_id,1);
        // 模板消息推送
        if($r){
            $this->success('success');
        } else {
            $this->success('friend not found');
        }
    }
    /**
     * 获取wechatid
     */
    public function getWechatid() {
        $user_id = $this->auth->__get('id');
        $target_id = intval($this->request->param('userid'));
        $res = Userfriend::get(['lover_user_id'=>$user_id,'target_user_id'=>$target_id]);
        if($res->status == '3') {
            $res = \addons\doger\model\Userbasicinfo::get(['lover_user_id'=>$target_id]);
             $data['wechatid']= $res->wechatid;
        } else {
            $data = 'no right';
        }
        $this->success('success',$data);
    }

    /**
     * 获取好友列表
     * @param $type  1=我想认识的 2=想认识我的
     */
    public function get(){
        $type = intval($this->request->param('type'));
        $user_id = $this->auth->__get('id');
        $res = '';
        if(1 == $type){
            $res = Db::view('lover_user_friend','target_user_id,messagecontent,status,updatetime')
                ->view('lover_user_basicinfo',['nickname','avatar','gender','birthday','constellation','company','position','living_province','living_city','homeland_province','homeland_city','highestdegree'],'lover_user_basicinfo.lover_user_id=lover_user_friend.target_user_id','LEFT')
                ->where(['lover_user_friend.lover_user_id'=>$user_id])
                ->order('updatetime','desc')
                ->select();
        } else if (2 == $type){
            $res = Db::view('lover_user_friend','lover_user_id,messagecontent,status,updatetime')
                ->view('lover_user_basicinfo',['nickname','avatar','gender','birthday','constellation','company','position','living_province','living_city','homeland_province','homeland_city','highestdegree'],'lover_user_basicinfo.lover_user_id=lover_user_friend.lover_user_id','LEFT')
                ->where(['lover_user_friend.target_user_id'=>$user_id])
                ->order('updatetime','desc')
                ->select();
        } else {
            $this->error('params error');
        }
        // 返回用户列表
        $this->success('success',$res);
    }

}