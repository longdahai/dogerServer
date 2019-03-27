<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/7
 * Time: 22:26
 */

namespace addons\doger\controller;
use addons\doger\model\Usercard as UsercardModel;

class Usercard extends Base
{

    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 用户资料卡片
     * @param $id 用户卡片id，无则返回全部
     */
    public function index(){
        $user_id = $this->auth->__get('id');
        $card_id = $this->request->param('id');
        if(isset($card_id)) {
            $data = UsercardModel::get(function($query) use ($card_id){
                $query->where('id',$card_id);
            });
        } else {
            $data = UsercardModel::all(function($query) use($user_id){
                $query->where('lover_user_id',$user_id)->order('weigh','desc');
            });
        }
        $this->success('success',$data);
    }

    /**
     * 获取某个卡片
     * @param
     */
    public function get(){
        $this->index();
    }

    /**
     * 添加
     */
    protected function add(){
        $data = $this->request->param();
        $data['lover_user_id'] = $this->auth->__get('id');
        $usercard = new UsercardModel();
        $res = $usercard->allowField(true)->save($data);
        if($res) {
            $this->success('add success!');
        } else {
            $this->error('add failed!');
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        $card_id = $this->request->param('id');
        $data = $this->request->param();
        if (empty($card_id)){
            $this->add();
        } else {
            $usercard = new UsercardModel();
            $res = $usercard->allowField(true)->save($data,['id' => $card_id]);
            if($res){
                $this->success('update success!');
            } else {
                $this->error('update failed!',$res);
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $raw_data = $this->request->param('id');
        $cardids = explode(",",$raw_data);
        $cardids = array_filter($cardids);
        $res = UsercardModel::destroy($cardids);
        if($res){
            $this->success('delete success');
        } else {
            $this->error('delete failed');
        }
    }

}