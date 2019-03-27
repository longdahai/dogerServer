<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/5
 * Time: 0:08
 */

namespace addons\doger\model;


use think\Model;

class Tag extends Model
{
    protected $name = "lover_tag";
    protected $hidden = ['lover_user_ids','nums'];
}