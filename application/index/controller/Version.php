<?php
namespace app\index\Controller;
use app\Common\Controller\LC;
use think\console\Input;
use think\Url;
use think\Validate;
use think\Controller;
use Think\Request;
use think\Session;
class Version extends LC {
    /**
     * 增加版本
     */
    public function add(){
        if (!request()->isPost()){
            header('Content-Type:text/html; charset=utf-8');
            return $this->fetch();
        }
        else{
            if(Input('AppID') != 0){
                $app= db('Versions');
                $rule=[
                    'VersionName'=>'require',
                    'Time'=>'require',
                    'Log'=>'require',
                ];
                $msg = [
                    'VersionName.require' => '版本名不能为空!',
                    'Time.require'=> '更新时间不能为空!',
                    'Log.require'        => '更新日志不能为空！',
                ];
                $da=[
                    'VersionName'=>$_POST['VersionName'],
                    'Time'=>$_POST['Time'],
                    'Log'=>$_POST['Log']
                ];
                $VersionID = $app->where(array('AppID'=>Input('AppID')))->max('VersionID')+1;
                $validate=new Validate($rule,$msg);
                $result=$validate->check($da);
                if(!$result){
                    $this->error($validate->getError().'添加失败');
                }
                $data=array(
                    'AppID'=>Input('AppID'),
                    'VersionName'=>$_POST['VersionName'],
                    'Time'=>$_POST['Time'],
                    'Log'=>$_POST['Log'],
                    'VersionID'=>$VersionID,
                );
                if($lastInsId = $app->insert($data)){
                    $this->success("添加成功");
                }else{
                    $this->error($app->getError()."添加失败");
                }
            }
        }
    }
    /**
     * 修改版本
     */
    public function modify(){
        if (!request()->isPost()){
            header('Content-Type:text/html; charset=utf-8');
            $Dao = db("versions");
            $data = array(
                "AppID" => Input('AppID'),
                "VersionID" => Input('VersionID'),
            );
            $result = $Dao->where($data)->find();
            if (!$result) {
                $this->error("数据查询出错", Url('index'));
            } else {
                $this->assign('data', $result);
                return $this->fetch();
            }
        }
        else {
            $Dao = db("versions");
            $data = array(
                "AppID" => Input('AppID'),
                "VersionID" => Input('VersionID'),
            );
            $da=array(
                "VersionName"=>Input('VersionName'),
                "Time"=>Input('Time'),
                "Log"=>Input('Log'),
                "Note"=>Input('Note')
            );
            $result = $Dao->where($data)->update($da);
            if($result){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }
    }


}