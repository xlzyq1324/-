<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Controller;
use think\Request;

class Common extends Controller
{
    public function __construct()
    {
        parent::__construct();
      // echo "<pre>";echo "cookie";print_r(cookie('username'));
        if (cookie('username') != '') {
            session('username', cookie('username'));
        }
      //  echo "<pre>";echo "session";print_r(session('username'));exit;
        if (session('username') == '') {

        }else{
            $this->redirect('index/index');
        }
    }
}
