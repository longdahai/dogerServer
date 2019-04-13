<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/26
 * Time: 1:17
 */

namespace addons\doger\controller;


class Formid extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 用户表单信息
     */
    public function index(){

    }


    /**
     * 添加
     */
    public function add(){
        $data = $this->request->param();
        $user_id = $this->auth->__get('id');
        $f = new \addons\doger\model\Formid();
        $data['expiretime'] = null;
        $data['user_id'] = $user_id;
        $r = $f->allowField(true)->save($data);
        if($r) {
            $this->success('add success',$r);
        } else {
            $this->error('add failed',$r);
        }
    }

    /**
     * 编辑
     */
    public function edit(){

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