<?php
/**
 * Created by PhpStorm.
 * User: 洪祺瑜
 * Date: 2017/11/2
 * Time: 14:56
 */
namespace app\index\controller;
use think\Controller;
use Think\Request;
use think\Validate;
use think\Session;
class Publicc extends Controller{
    /**
     * 注册处理
     */
    public function dealreg(){
        header('Content-Type:text/html; charset=utf-8');
        $request=request();
        if (!$request->isPost()){
            $this->error("您访问的页面不存在");
        }
        $rule=[
            'Username'=>'require',
            'Password'=>'require',
            'Email'=>'email',
        ];
        $msg = [
            'Username.require' => '用户名不能为空',
            'Password.require'=> '密码不能为空',
            'email'        => '邮箱格式错误',
        ];
        $da=[
            'Username'=>$_POST['Username'],
            'Password'=>$_POST['Password'],
            'Email'=>$_POST['Email']
        ];
        $name=$_POST['Username'];
        $validate=new Validate($rule,$msg);
        $result=$validate->check($da);
        $re=db("Users")->where('Username',$name)->find();
        if($re)
        {
            $this->error('已有该用户名，注册失败',Url('Publicc/register'));
        }
        if(!$result){
            $this->error($validate->getError().'注册失败',Url('Publicc/register'));
        }
        else{
            $data=array(
                'Username'=>$_POST['Username'],
                'Password'=>$_POST['Password'],
                'Email'=>$_POST['Email'],
                'Name'=>$_POST['Name'],
                'Permission'=>2,//默认开发者权限   1.管理员 2.开发者
                'RegTime'=>date('Y-m-d H:i:s',time())//注册时间
            );
            $Users = db('Users');
            if(!$Users->insert($data))
            {
                //exit($Users->getError());
                $this->error($Users->getError().'注册失败',Url('Publicc/register'));
            }
            else
            {
                $this->success('注册成功，页面跳转中...',Url('Publicc/login'));
            }
        }


    }
    /**
     * 注册
     */
    public function register(){
        header('Content-Type:text/html; charset=utf-8');
        return $this->fetch();
    }

    /**
     * 登录
     */
    public function login(){
        header('Content-Type:text/html; charset=utf-8');
        return $this->fetch('Publicc/login');
    }
    /**
     * 处理登录
     */
    public function deallogin(){
        $request=request();
        if (!$request->isPost()) {
            $this->error("您访问的页面不存在");
        }
        $Username=Input('post.username');
//        $Password=md5(C('PW_Salt').I('post.password','','md5'));
        $Password =Input('post.password');
        $condition = array(
            'Username' => $Username,
            'Password'=> $Password
        );
        $user=db("users");
        $result=$user->where($condition)->find();
        if($result){
            Session::set('loginid',Config('USER_AUTH_KEY'));//记录认证标记，
            Session::set('UserID',$result['UserID']);//用户ID标记；
            Session::set('Username',$result['Name']);//用户登录时间标记
            Session::set('Permission',$result['Permission']);
            header('Content-type:text/html;Charset=UTF-8');
            $this->assign('Username',$result['Name']);
            $this->success('登录成功',Url('Index/index'));
        }else{
            header('Content-type:text/html;Charset=UTF-8');
            $this->error('登录失败',Url('Publicc/login'));
        }
    }
    /**
     * 注销
     */
    public function logout(){
        $_SESSION = array(); //清除SESSION值.
        if(isset($_COOKIE[session_name()])){  //判断客户端的cookie文件是否存在,存在的话将其设置为过期.
            setcookie(session_name(),'',time()-1,'/');
        }
        session::clear();  //清除服务器的sesion文件
        $this->success('退出成功',Url('Index/index'));
    }
    /**
     * 邮箱
     */
    public function email(){
        header('Content-Type:text/html; charset=utf-8');
        return $this->fetch("Index/email");

    }
    /**
     * 帮助
     */
    public function help(){
        header('Content-Type:text/html; charset=utf-8');
        $UN=session('Username');
        $this->assign('Username',$UN);
        return $this->fetch('Index/help');

    }
}