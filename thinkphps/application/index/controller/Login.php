<?php
namespace app\index\controller;
use app\index\controller\Common;
use think\Controller;
class login extends Common
{
    public function index()
    {

        return $this->fetch();

    }
//    public  function niubi(){
////        echo "<pre>";print_r(session('username'));
//        echo session('username').'欢迎登陆';
//    }
    public function zhuce()
    {
        return $this->fetch();

    }
    public function adduser()
    {
        $data=input('post.');
        $db=db('yanzheng')->where("username='".$data['username']."'")->find();
        if($db){
            $this->success('已有该用户','index/zhuce');
        }else{
            $info=db('user')->insert($data);
            if($info){
                $this->success('注册成功','index/index');
            }
        }

    }
    public function do_login()
    {
        $data=input('post.');
        $username=input('post.username');
        $db=db('yanzheng')->where("username='".$username."'")->find();
        if($db){
            if($data['password']==$db['password']){
                if(input('post.ischecks'))
                {
                    // C('COOKIE_EXPIRE',time()+864000);
                    cookie('username',$username,time()+864000);
                    //setcookie('username',$username,864000);
                }
                session('username',$data['username']);
                session('password',$data['password']);
//                $this->success('登录成功','index/index');

                $result=[
                    'msg'=>1,
                    'statu'=>"登陆成功"
                ];
                return json($result);

            }else{
//                $this->success('密码错误','index/index');
                $result=[
                    'msg'=>2,
                    'statu'=>"密码错误"
                ];
                return json($result);
            }

        }else{
//            $this->success('没有此用户，请注册后登陆','index/zhuce');
            $result=[
                'msg'=>3,
                'statu'=>"查无此人"
            ];
            return json($result);
        }

    }


    }
