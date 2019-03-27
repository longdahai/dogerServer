<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/9
 * Time: 19:44
 */

namespace addons\doger\controller;
use addons\doger\model\Userbasicinfo as UserbasicinfoModel;

class Basicinfo extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 用户基本信息
     */
    public function index(){
        $user_id = $this->auth->__get('id');
        $data = UserbasicinfoModel::get(['lover_user_id' => $user_id]);
        $this->success('success',$data);
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
        $data = '';
        $flag = 1;
//        $cardNo = $this->request->param('cardNo');
//        if (isset($cardNo)){
//            if( intval($this->request->param('cardNo')) == 4){
//                $data = 'completed';
//                $flag = 1;
//            }
//        }

        if($this->editBasicinfo($flag)){
            $this->success('success',$data);
        } else {
            $this->error('failed');
        }
    }

    /**
     * 更新用户基本资料
     * $param flag 0:不修改is_new字段  1:修改user表is_new
     */
    protected function editBasicinfo($flag = 1){
        $data = $this->request->param();
        foreach ($data as $key=>$value){
            if(is_array($value)) {
                foreach ($value as $k=>$v){
                    $keyname = $key . '_' . $k;
                    $data[$keyname] = $v;
                }
            }
        }
        $userbasicinfo = new UserbasicinfoModel();
        if(UserbasicinfoModel::get(['lover_user_id' =>$this->auth->__get('id')])) {
            if(isset($data['birthday'])){
                $data['constellation'] = null;
            }
            $userbasicinfo = $userbasicinfo->allowField(true)->save($data,['lover_user_id' => $this->auth->__get('id')]);
        } else {
            $data['lover_user_id'] = $this->auth->__get('id');
            $userbasicinfo = $userbasicinfo->allowField(true)->save($data);
        }
        if($flag) {
            $user = new \addons\doger\model\User();
            $user->save(['is_new' => 0],['id'=> $this->auth->__get('id')]);
        }

        if($userbasicinfo){
            return true;
        }

        return false;
    }

    /**
     * 删除
     */
    public function del(){

    }

    /**
     * 物理删除
     */
    public function rm(){

    }
}