<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class Prefer extends Model
{
    protected $name = "lover_user_prefer";
    protected $hidden = ['lover_user_id','id'];
}