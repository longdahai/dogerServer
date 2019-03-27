<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/26
 * Time: 0:59
 */

namespace addons\doger\controller;


class Prefer extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 获取用户匹配偏好
     */
    public function index(){
        $user_id = $this->auth->__get('id');
        $data = \addons\doger\model\Prefer::get(['lover_user_id'=>$user_id]);
        if($data){
            $this->success('success',$data);
        } else {
            $this->success('success');
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
        $prefer = new \addons\doger\model\Prefer();
        $flag = $prefer->where(['lover_user_id'=>$user_id])->select();

        if($flag){
            $res = $prefer->allowField(true)->save($data,['lover_user_id'=>$user_id]);
        } else {
            $data['lover_user_id']  = $user_id;
            $res = $prefer->allowField(true)->save($data);
        }

        if($res){
            $this->success('success',$res);
        } else {
            $this->error('failed');
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