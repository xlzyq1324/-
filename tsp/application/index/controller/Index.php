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





    //砍价开始
    public function bargains()
    {
        $redirect_uri=urlencode("http://www.514superhero.cn/wxlist/index.php/Home/index/firstbargain");
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx82dcf28e0b79cdee&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
        header('Location:'.$url);
    }
    public function firstbargain()
    {
        $code=input('get.code');
        $userinfourl="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx82dcf28e0b79cdee&secret=e9478dd3dc6d63589f42a7f9b6253361&code=".$code."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$userinfourl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $objtoken = json_decode(curl_exec($ch),true) ;
        curl_close($ch);
        $access_token=$objtoken['access_token'];
        $openid=$objtoken['openid'];
        $getinfourl="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $chh = curl_init();
        curl_setopt($chh, CURLOPT_URL,$getinfourl);
        curl_setopt($chh, CURLOPT_RETURNTRANSFER, 1);
        $users = json_decode(curl_exec($chh),true) ;
        curl_close($chh);
        session('openid',$openid);

        $infos=db('user')->where("openid='".$openid."'")->find();
        if($infos){
            $this->assign('openid',$infos);
        }else{
            $inserts=db('user')->add($users);
            $infoss=db('user')->where("openid='".$openid."'")->find();
            $this->assign('openid',$infoss);
        }
        $url="http://www.514superhero.cn/wxlist/index.php/Home/index/bargain/openid/".$openid.".html";
        header('Location:'.$url);
    }
    public function helpbargains()
    {
        $session=I('openid');
        $code=I('get.code');
        $userinfourl="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx82dcf28e0b79cdee&secret=e9478dd3dc6d63589f42a7f9b6253361&code=".$code."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$userinfourl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $objtoken = json_decode(curl_exec($ch),true) ;
        curl_close($ch);
        $access_token=$objtoken['access_token'];
        $openid=$objtoken['openid'];
        $getinfourl="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $chh = curl_init();
        curl_setopt($chh, CURLOPT_URL,$getinfourl);
        curl_setopt($chh, CURLOPT_RETURNTRANSFER, 1);
        $users = json_decode(curl_exec($chh),true) ;
        curl_close($chh);
        session('openid',$openid);
        $infos=db('user')->where("openid='".$openid."'")->find();
        if($infos){
            $this->assign('openid',$infos);
        }else{
            $inserts=db('user')->add($users);
            $infoss=db('user')->where("openid='".$openid."'")->find();
            $this->assign('openid',$infoss);
        }
        $url="http://www.514superhero.cn/wxlist/index.php/Home/index/bargain/openid/".$session.".html";
        header('Location:'.$url);
    }
    public function bargain(){
        if(session('openid')){
            $session=I('openid');
            if($session==session('openid')){
                $infos=db('user')->where("openid='".session('openid')."'")->find();
                if($infos){
                    $this->assign('nickname',$infos['nickname']);
                }else{
                    echo "未找到该用户";exit;
                }
                $inf=db('bargain')->where("openid='".session('openid')."'")->find();
                $this->assign('kanjiamoney',$inf['money']-$inf['newmoney']);
                $this->assign('bargain',$inf);
                $this->assign('openid',$session);
                $this->display('bargain');
            }else{
                $infos=db('user')->where("openid='".session('openid')."'")->find();
                if($infos){
                    $this->assign('openid',$infos);
                }
                $inf=db('bargain')->where("openid='".$session."'")->find();
                $this->assign('bargain',$inf);
                $usinfo=db('bargainfor')->where("helpopenid='".session('openid')."'")->find();
                $this->assign('money',$usinfo['money']);
                $this->assign('openid',$session);
                $this->display('exbargain');
            }
        }else{
            $session=I('openid');
            $redirect_uri=urlencode("http://www.514superhero.cn/wxlist/index.php/Home/index/helpbargains/openid/".$session."");
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx82dcf28e0b79cdee&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header('Location:'.$url);
        }


    }
    public function do_bargain(){
        if(session('openid')){
            $session=I('openid');
            if(session('openid')==$session){
                $data['openid']=session('openid');
                $info=db('user')->where("openid='".session('openid')."'")->find();
                $data['username']=$info['nickname'];
                $data['money']=800;
                $infos=db('bargain')->where("username='". $data['username']."'")->find();
                if($infos){
//                    $url="http://www.514superhero.cn/wxlist/index.php/Home/index/bargain.html";
//                    header('Location:'.$url);
                    echo "不能重复砍价";
                }else{
                    //http://www.514superhero.cn/wxlist/index.php/Home/index/bargain.html
                    $data['newmoney']=$data['money']-100;
                    $inserts=db('bargain')->add($data);
                    echo "成功砍掉100元";
                }
            }else{
                $infos=db('bargainfor')->where("helpopenid='".session('openid')."' and useropenid='".$session."'")->find();
                if($infos){
                    echo "只能帮砍一次";
                }else{
                    $data['useropenid']=$session;
                    $data['helpopenid']=session('openid');
                    $datas=array();
                    while(count($datas)<100){
                        $val=rand(1,800);
                        $datas[$val]=$val;
                    }
                    $data['money']=$datas[$val];
                    $insert=db('bargainfor')->add($data);
                    echo "成功帮忙砍价";
                }

            }
        }
    }
    //砍价结束





    }
