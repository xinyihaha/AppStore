<?php
/**
 * Created by PhpStorm.
 * User: 洪祺瑜
 * Date: 2017/11/2
 * Time: 14:56
 */
namespace app\index\controller;
use app\common\controller\LC;
use think\Config;
use think\View;
use app\common\controller\Page;
use think\Controller;
use think\Paginator;
class User extends LC{
    /**
     * 用户信息页面
     */
    public function index(){
        if(!$this->isAdmin()){
            $this->error("您访问的页面不存在");
        }
        $number=config('PAGE_USER_ROW');
        $list=db('users')->paginate($number,db('users')->count());
        $page=$list->render();
        $this->assign('list', $list);        // 分页赋值
        $this->assign('page', $page);        // 分页赋值
        return $this->fetch();
    }
    /**
     * 修改用户信息页面
     * @param $UserID 管理员可以指导UserID进行修改，用户默认自己的ID
     */
    public function modify($UserID =0){
        $request=request();
        if($request->isPost()){
            if($UserID == 0 || !$this->isAdmin()) {
                $UserID = request()->session('UserID');
            }
            if(Input('post.submit') == 'chpw'){
                $Users = db('Users');
                $password1=Input('post.password1');
                $password2=Input('post.password2');
                $password3=Input('post.password3');
                $Password= $Users->where("UserID=$UserID")->value('Password');
                if($password1!=$Password||$password3!=$password2||$password2=="")
                {
                    $this->error("修改密码出错(请检查输入内容！！)",Url('User/modify'));
                }

                $Users->where("UserID=$UserID")->update(['Password'=>$password2]);
                $this->success('密码修改成功');
            }else if(Input('post.submit') == 'chpf'){
                $Name=Input('post.Name');
                $Email=Input('post.Email');
                if($Name==""||$Email=="")
                {
                    $this->error("修改信息出错(请检查输入内容！！)",Url('User/modify'));
                }
                $Users = db('Users');
                //$Users->create();
                $Users->where("UserID=$UserID")->update(['Name'=>$Name,'Email'=>$Email]);
                $this->success('用户信息修改成功');
            }
            return;
        }

        if($UserID == 0 || !$this->isAdmin()) {
            $UserID = request()->session('UserID');
        }
        $users = db("users");
        $condition['UserID']=$UserID;
        $user = $users->where($condition)->find();
        $this->assign('data',$user);
        return $this->fetch();
    }
    public function info(){
        $users = db("users");
        $condition['UserID']=request()->session('UserID');
        $user = $users->where($condition)->find();
        $this->assign('data',$user);
        return $this->fetch();
    }
}