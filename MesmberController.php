<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mesmber;
//  2109.11.5
//创建人：谢洁
//用户的注册，用户的登录 用户信息的修改
class MesmberController extends Controller
{
    //用户的注册

    public function  Register(Request $request){
         if($request->isMethod('post')){//判断是否是post方法，是的话接受数值，否的话跳到注册页面
             $phone = $request['phone'];//接受电话号码
             $name =$request['name'];//接受用户名
             $pwd = md5($request['pwd']);//接受密码并且密码加密

             if (strlen($phone) != 11){
                 return ['code'=>1,'msg'=>'手机号长度不对','data'=>''];//验证手机号的长度
             }

             $this->validate($request, [//判断数据是否符合要求
                 'phone' => 'required|unique:member',
                 'name' => 'required|min:6',
                 'name'=>'required|max:20'
             ]);

             $model = new Mesmber();//引用数据库

            $res = $model->insert([
                'name'=>$name,
                'pwd'=>$pwd,
                'phone'=>$phone,
            ]);

             if ($res){
                return view('member.Login');//添加成功
             }else{
                 return ['code'=>1,'msg'=>'添加失败','data'=>''];//添加失败
             }

         }else{
             return view('mesber.register');//跳转到注册页面
         }
    }


    //用户的登录
    public function Login(Request $request){
        $name = $request['name'];
        $pwd = md5($request['pwd']);
        $this->validate($request, [//判断数据是否符合要求
            'name' => 'required|min:6',
            'name'=>'required|max:20'
        ]);
        $model = new Mesmber();//调用数据库

        $res = $model->where('name',$name)->where('pwd',$pwd)->get();//查询数据库，判断是否登录成功
              $res =json_decode($res,true);//吧对象转化数组
       $id = $res[0]['id'];//定义id

        if($res){
            return view('member.reset',['id'=>$id]);//把id1传到页面中
        }else{
            return ['code'=>1,'msg'=>'登录失败','data'=>''];//登录失败
        }

    }

    public function Reset(Request $request){
        $model = new Mesmber();//定义数据库


        $res = $model->where('id',$request['id'])->where('pwd',md5($request['pwd']))->get();

       //查找条件
        if (!$res){//判断密码是否正确
            return ['code'=>1,'msg'=>'密码不正确','data'=>null];
        }

        if($request['n_pwd'] != $request['n_pwdd']){//判断新密码与验证密码是否一致
            return ['code'=>1,'msg'=>'新密码和验证密码不一致','data'=>null];
        }

        $ress = $model ->where('id',$request['id'])->update([
            'pwd'=>md5($request['n_pwd']),//修改用户密码
        ]);

       $data = $model->where('id',$request['id'])->get();
       
        if ($ress){
            return view('member.news',['data'=>$data]);//跳转页面
        }else{
            return ['code'=>1,'msg'=>'修改失败','data'=>null];
        }
    }
}
