<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Controller;
use app\index\controller\Common;
class Login extends Common
{
    public function login(){
        return $this->fetch();
    }
    public function zhuce(){
        return $this->fetch();
    }

}
