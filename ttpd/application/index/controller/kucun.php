<?php
namespace app\index\Controller;
use thik\Controller;
class  kucun extends Controller
{

   public function index()
   {
        //1、用户名不可有非法字符； /\W/   如果可以匹配 则有非法字符
        //2、手机号  /^(151|176|138)\d{8}$/
        //3、邮箱   /^[A-Za-z0-9]+[@][a-z0-9]+[.][a-z]+$/
        //4、验证真实姓名 /^[\x{4e00}-\x{9fa5}]{1,5}\b$/u
        //5、整数 /^\d+$/
        //6、仅中文 /^[\x{4e00}-\x{9fa5}]{1,5}\b$/u
        //7、身份证
        // /^(3[1-7]|4[1-6]|5[0-4]|6[1-5]|8[1-2])\d{4}(19[1-9][1-9]|20[0-1][0-8]|[0-9][0-9])(0[0-9]|1[0-2])((0[1-9]|1[0-9]|2[0-9]|3[0-1]))\d{4}$/
        //8、ip地址  
        //   /^(([1]{1}[0-9]{0,1}[0-9]{0,1})|([2]{1}[0-5]{0,1}[0-5]{0,1}))[.](([1]{1}[0-9]{0,1}[0-9]{0,1})|([2]{1}[0-5]{0,1}[0-5]{0,1}))[.](([1]{1}[0-9]{0,1}[0-9]{0,1})|([2]{1}[0-5]{0,1}[0-5]{0,1}))[.](([1]{1}[0-9]{0,1}[0-9]{0,1})|([2]{1}[0-5]{0,1}[0-5]{0,1}))$/
        //9、正常url  /^(http|https):\/\/www[.]\w+[.](com|cn|top)$/

   }

}