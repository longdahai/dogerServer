<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/26
 * Time: 1:17
 */

namespace addons\doger\controller;


class Reports extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * reports
     */
    public function index(){

    }


    /**
     * 添加
     */
    public function add(){

    }

    /**
     * 编辑
     */
    public function report(){
        $user_id = $this->auth->__get('id');
        $data = $this->request->param();
        $r = \addons\doger\model\Reports::get(['lover_user_id'=>$user_id, 'target_user_id' => $data['target_user_id']]);
        $report = new \addons\doger\model\Reports();
        if($r){
            $this->success('已举报过了!');
        } else {
            $data['lover_user_id'] = $user_id;
            $res = $report->allowField(true)->save($data);
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