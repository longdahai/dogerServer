<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/4/14
 * Time: 2:13
 */

namespace addons\doger\controller;

use addons\doger\library\TemplateHelper;

class Test
{
    protected  $app;
    protected  $methods_test = ['getlist','gettps'];
    public function index(){
        $this->app = new TemplateHelper();
        $methods = get_class_methods($this->app);
        array_splice($methods,0,1);
        foreach ($this->methods_test as $v) {
            $mm =  new \ReflectionMethod($this->app,$v);
            var_dump($mm->invoke($this->app));
        }
    }
}