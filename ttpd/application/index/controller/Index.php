<?php
namespace app\index\controller;
use think\cache\driver\redis;
use think\Controller;

class Index extends Controller
{

//判断session/cookie的值验证跳转index或niubi这个页面
    public  function niubi(){
        print_r(cookie('username'));

        // exit;
        if(cookie('username')){
            session('username',cookie('username'));
        }
//        print_r(session('username'));exit;
        if(session('username')){
            $this->assign('username',session('username'));
        }else{
            $this->redirect('Login/index');
        }
        return $this->fetch();
//        echo "<pre>";print_r(session('username'));
//        echo session('username').'欢迎登陆';
    }






    //注册页面
    public function zhuce()
    {
        return $this->fetch();

    }
    //注册的验证加方法
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







//被秒杀的物品列表
    public function qianggou()
    {
        $ses=session('username');
        echo "用户名：".$ses;
        $datas=db('qianggou')->select();
//        echo "<pre>";
//        print_r($datas);
        $this->assign('datas',$datas);
        return $this->fetch();
    }
    //获取redis
    public function getmsshop(){
        $redis=new \Redis();
        $ret = $redis->connect("127.0.0.1",6379);
        $num=3;
        for($i=0;$i<$num;$i++){
            $redis->lPush('goods',1);
        }
    }
    //秒杀物品
    public function qgadd()
    {
        $redis=new \Redis();
        $ret = $redis->connect("127.0.0.1",6379);
        $id=input('id');
        $ms=$redis->lpop('goods');
        if($ms){
            $msus=$redis->lPush('mssuccuser',session('username'));
            if($msus){
                $info=db('qianggou')->where('Id='.$id)->setDec('sumber',1);
                $data['username']=session('username');
                $data['goodsid']=$id;
                $infos=db('dbd')->insert($data);
                if($infos){
                    echo "秒杀成功";
                }
            }
        }else{
            echo "抢光了";
        }
    }

    }
