<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/26
 * Time: 1:17
 */

namespace addons\doger\controller;


class Usertag extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 用户标签信息
     */
    public function index(){
        $user_id = $this->auth->__get('id');
        $data = \addons\doger\model\Usertag::get(['lover_user_id'=>$user_id]);
        if($data){
            $this->success('success',$data);
        } else {
            $this->error('failed');
        }
    }


    /**
     * 添加
     */
    public function add(){

    }

    /**
     * 编辑
     */
    public function edit(){
        $user_id = $this->auth->__get('id');
        $data = $this->request->param();
        $r = \addons\doger\model\Usertag::get(['lover_user_id' => $user_id]);
        $usertag = new \addons\doger\model\Usertag();
        if($r){
            $res = $usertag->allowField(true)->save($data,['lover_user_id'=> $user_id]);
        } else {
            $data['lover_user_id'] = $user_id;
            $res = $usertag->allowField(true)->save($data);
        }
        if($res){
            $this->success('success',$res);
        } else {
            $this->error('failed',$res);
        }
    }

    /**
     * 删除
     */
    public function del(){

    }

    /**
     * 删除
     */
    public function rm(){

    }
}