<?php
namespace app\common\controller;
/**
 * Created by PhpStorm.
 * User: 洪祺瑜
 * Date: 2017/11/2
 * Time: 14:21
 */
use think\Url;
use think\Config;
use think\View;
use think\Controller;
use think\Request;
class LC extends Controller
{
    public function _initialize()
    {
        if (request()->session('loginid') != Config::load('USER_AUTH_KEY')) {
            redirect(Url::build('Publicc/login'));
        }
        $this->assign('UserID', request()->session('UserID'));
        $this->assign('Username', request()->session('Username'));
        $this->assign('Name', request()->session('Name'));
        $this->assign('isAdmin',$this->isAdmin());
    }
    public function isAdmin(){
        if (request()->session('Permission') == 1){
            return true;
        }else{
            return false;
        }
    }
}