<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Controller;

class Index extends Controller
{
    public function ex()
    {
       return  $this->fetch();
    }
    public function inserexcel()
    {
        Loader::import('PHPExcel.PHPExcel');
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
        Loader::import('PHPExcel.PHPExcel.Reader.Excel5');
        //获取表单上传文件
           $file = request()->file('excel');
           $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads');

        if($info) {
          // echo $info->getFilename();
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . $exclePath;   //上传文件的地址
            $objReader =\PHPExcel_IOFactory::createReader("Excel2007");
            $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8

            $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            print_r($excel_array);exit;
            array_shift($excel_array);  //删除第一个数组(标题);
            $users = [];
            foreach($excel_array as $k=>$v) {
                $users[$k]['username'] = $v[0];
                $users[$k]['sex'] = $v[1];
                $users[$k]['idcate'] = $v[2];
                $users[$k]['dorm_id'] = $v[3];
                $users[$k]['iclass'] = $v[4];
                // print_r($users) ;exit;

            }
            //echo "<pre>";print_r($users);exit;
           $add=Db::name('users')->insertAll($users); //批量插入数据
           if($add){
            $this->success('succ');
           }else{
            $this->error('fail');
           }
        } else {
            echo $file->getError();
        }
    }

    public function index()
    {
        if(cookie('username')){
            session('username',cookie('username'));
        }
        if(session('username')){
            $this->assign('username',session('username'));
        }else{
            $this->redirect('Login/login');
        }

        $db=db('users');
        $info=$db->paginate('5');
        $this->assign('find',$info->toArray());
        $page=$info->render();
        $this->assign('page',$page);
        return $this->fetch();

    }
    public function do_zhuce(){
        $data=input('post.');
        $db=db('user')->where("username='".$data['username']."'")->find();
        if($db){
            $this->success('已有该用户','index/login');
        }else{
            $info=db('user')->insert($data);
            if($info){
                $this->success('注册成功','index/login');
            }
        }
    }
    public function do_login(){
        $data=input('post.');
        $username=input('post.username');
        $code=$data['yanzhengma'];
        unset($data['yanzhengma']);
        $db=db('user')->where("username='".$data['username']."'")->find();
        if(captcha_check($code)) {
            if($db){
                if($data['password']==$db['password']){
                    session('username',$data['username']);
                    $result=[
                        'msg'=>1,
                        'statu'=>'登录成功'
                    ];
                    if(isset($data['ischeck']))
                    {
                        cookie('username',$username,864000);
                    }
                    return json($result);
                }else{
                    $result=[
                        'msg'=>2,
                        'statu'=>'密码错误'
                    ];
                    return json($result);
                }

            }else{
                $result=[
                    'msg'=>3,
                    'statu'=>'用户名不存在'
                ];
                return json($result);
            }
        }else{
            $result=[
                'msg'=>4,
                'statu'=>'验证码错误'
            ];
            return json($result);
        }

    }
    public function reg()
    {
        $data=input('post.');
        //获取表单上传文件
        $file = request()->file('files');

        if (empty($file)) {
            $this->error('请选择上传文件');
        }
        //移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            $this->success('文件上传成功');
            echo $info->getFilename();
        } else {
            //上传失败获取错误信息
            $this->error($file->getError());
        }
        $email=input('post.email');
        $username=input('post.username');
        $title="你好,".$username.'欢迎注册相亲网';
        $body="你好，".$username.',恭喜你，相亲失败';
        sendmail($email,$title,$body);
    }
    // 1.创建excel文件
    // 2.读取表
    // 3.把读出来的信息插入excel表
    // 4.保存
    public function do_excel(){
       $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AB');
        // echo "<pre>";
        // print_r($lists);exit;
        $path=dirname(__FILE__);//找到当前脚本所在路径
        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory'); //引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $phpexcel=new \PHPExcel();//建excel表
        $rowsheet=db('iclass')->select();//查询有几个班级
        foreach($rowsheet as $key=>$row){
            $phpexcel->createSheet();//建sheet
            $phpexcel->setactivesheetindex($key);
            $phpSheet = $phpexcel->getActiveSheet();//加标记
            $phpSheet->setTitle($row['classname']);//每个的sheet的名称
            $lists=Db::query('SHOW FULL COLUMNS from wx_users');
            foreach($lists as $key=>$l){//获取当前表结构,赋给sheet的第一行
               $comment=$l['Comment']?$l['Comment']:$l['Field'];
               $phpSheet->setCellValue($cellName[$key].'1',$comment);
            }
            $users=db('users')->where("iclass=".$row['id'])->select();
            $i=2;
            foreach($users as $key=>$use){//获取表的值，赋给excel
                $j=0;
                foreach($use as $u){
                    $phpSheet->setCellValue($cellName[$j].$i,$u);
                    $j++;
                }
                $i++;
            }

        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($phpexcel, "Excel2007"); //创建生成的格式
        header('Content-Disposition: attachment;filename="学生列表'.time().'.xlsx"'); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

    }
    public function do_import($file){
         $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    $type=$type==='csv' ? $type : 'Excel5';
        $path=dirname(__FILE__);//找到当前脚本所在路径
        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory'); //引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)             //设置第一个内置表（一个xls文件里可以有多个表）为活动的
            ->setCellValue( 'A1', 'Hello' )         //给表的单元格设置数据
            ->setCellValue( 'B2', 'world!' )      //数据格式可以为字符串
            ->setCellValue( 'C1', 12)            //数字型
            ->setCellValue( 'D2', 12)            //
            ->setCellValue( 'D3', true )           //布尔型
            ->setCellValue( 'D4', '=SUM(C1:D2)' );//公式
    }
    public function excel()
    {
        $path = dirname(__FILE__); //找到当前脚本所在路径
        Loader::import('PHPExcel.PHPExcel'); //手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory'); //引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel(); //实例化
        $iclasslist=db('iclass')->select();
        foreach($iclasslist as $key=> $v){
            $PHPExcel->createSheet();
            $PHPExcel->setactivesheetindex($key);
            $PHPSheet = $PHPExcel->getActiveSheet();
            $PHPSheet->setTitle($v['classname']); //给当前活动sheet设置名称
            $PHPSheet->setCellValue("A1", "编号")
                     ->setCellValue("B1", "姓名")
                     ->setCellValue("C1", "性别")
                     ->setCellValue("D1", "身份证号")
                     ->setCellValue("E1", "宿舍编号")
                     ->setCellValue("F1", "班级");//表格数据
            $userlist=db('users')->where("iclass=".$v['id'])->select();
            //echo db('users')->getLastSql();
            $i=2;
            foreach($userlist as $t)
            {
                $PHPSheet->setCellValue("A".$i, $t['id'])
                         ->setCellValue("B".$i, $t['username'])
                        ->setCellValue("C".$i, $t['sex'])
                        ->setCellValue("D".$i, $t['idcate'])
                        ->setCellValue("E".$i, $t['dorm_id'])
                        ->setCellValue("F".$i, $t['iclass']);
                        //表格数据
                $i++;
            }


        }
       // exit;
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header('Content-Disposition: attachment;filename="学生列表'.time().'.xlsx"'); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
