<?php
/**
 * Created by PhpStorm.
 * User: 洪祺瑜
 * Date: 2017/11/2
 * Time: 14:55
 */
namespace app\index\controller;
use app\common\controller\LC;
class Ajax extends LC{
    public function checkusername($username)
    {
        $condition = array(
            'Username' => $username,
        );
        $user = db('Users')->where($condition)->find();
        if ($user) {
            echo json_encode(array("ret" => 1));
        } else {
            echo json_encode(array("ret" => 0));
        }
    }
    /**
     * 异步判断登录格式
     */
    public function islogin($username)
    {
        $condition = array(
            'Username' => $username,
        );
        $user = db('Users')->where($condition)->find();
        if ($user) {
            echo json_encode(array("ret" => 1));
        } else {
            echo json_encode(array("ret" => 0));
        }

    }
}