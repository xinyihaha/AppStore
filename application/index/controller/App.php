<?php
/**
 * Created by PhpStorm.
 * User: 洪祺瑜
 * Date: 2017/11/2
 * Time: 14:56
 */
namespace app\index\controller;
use app\common\controller\LC;
use app\common\controller\Page;
use org\Upload;
use think\Config;
use think\Url;
use think\View;
use think\Validate;
use think\Model;
use think\Controller;
class App extends LC{
    /**
     * app列表
     */
    public function index(){
        $number=config('PAGE_APP_ROW');
        $UserID=request()->session('UserID');
        $condition['UserID']=$UserID;
        $list=db('apps')->where($condition)->paginate($number,db('apps')->where($condition)->count());
        $page=$list->render();
        $this->assign('list', $list);        // 分页赋值
        $this->assign('page', $page);        // 分页赋值
        return $this->fetch('App/index');
    }
    public function all(){
        $number=config('PAGE_APP_ROW');
        $list=db('apps')->paginate($number,db('apps')->count());
        $page=$list->render();
        $this->assign('list', $list);        // 分页赋值
        $this->assign('page', $page);
        // 分页赋值
        return $this->fetch('App/all');
    }
    /**
     * 增加app
     */
    public function add(){
        $request=request();
        if (!$request->isPost()){
            header('Content-Type:text/html; charset=utf-8');
           return $this->fetch('App/add');
        }
        else {
            $rule = [
                'AppName' => 'require',
                'SymbolName' => 'require',
                'VersionID' => 'require',
                'CategoryID' => 'require',
                'AppDetail' => 'require'
            ];
            $msg = [
                'AppName.require' => '应用名不能为空',
                'SymbolName.require' => '内部名不能为空',
                'VersionID.require' => '版本号不能为空',
//                'SymbolName.uniquire'=>'已有改内部名',
                'CategoryID.require' => '类目不能为空',
                'AppDetail.require' => 'App描述不能为空'
            ];
            $da = [
                'AppName' => $_POST['AppName'],
                'SymbolName' => $_POST['SymbolName'],
                'VersionID' => $_POST['VersionID'],
                'CategoryID' => $_POST['CategoryID'],
                'AppDetail' => $_POST['AppDetail']
            ];
            $SymbolName=$_POST['SymbolName'];
            $re=db('apps')->where('SymbolName',$SymbolName)->find();
            if($re){
               $this->error("已有该内部名！", Url('App/index'));
            }
            $validate=new Validate($rule,$msg);
            $check=$validate->check($da);
            $UserID=request()->session('UserID');
            $app = db('apps');
            $AppID=$app->count()+1;
            $upload = new Upload(Config::get(),'Local');// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg', '7z', 'zip', 'rar');// 设置附件上传类型
           /*$upload->rootPath = './Upload/'.$UserID.'/'.$AppName.'/';*/
            $upload->savePath = $UserID.'/'.$AppID.'/';
            $picinfo = $upload->uploadOne($_FILES['Pic']);
            $softwareinfo = $upload->uploadOne($_FILES['Software']);
            $houzhui = substr(strrchr($softwareinfo['name'], '.'), 1);
            $result = basename($softwareinfo['name'], "." . $houzhui);
                if ($softwareinfo['name'] != $SymbolName) {
                   $upload->delDir( './Upload/'.$upload->savePath);
                   $this->error("上传的文件和应用名不符，请修改后再上传", Url('App/index'));
                } else {
                   if (!($picinfo && $softwareinfo)) {
                       $this->error($upload->getError());
                   } else {// 上传成功 获取上传文件信息
                       if (!$check) {
                           $upload->delDir( './Upload/'.$upload->savePath);
                           $this->error($validate->getError() . "添加失败", Url('App/index'));//$app->getError在db\exception\Connection.php中的第732行
                       } else {
                           $size = (float)round($softwareinfo['size'] / 1048576, 6);
                           $data = array(
                               'AppID' => $AppID,
                               'AppName' => $_POST['AppName'],
                               'SymbolName' => $_POST['SymbolName'],
                               'VersionID' => $_POST['VersionID'],
                               'AppStatus' => '0',
                               'CategoryID' => $_POST['CategoryID'],
                               'AppDetail' => $_POST['AppDetail'],
                               'Pic' => $picinfo['name'],
                               'Size'=> $size,
                               /*'Uploadsize' => $size,*/
                               'UserID' => request()->session('UserID'),
                           );
                           if (!$app->insert($data)) {
                               //exit($Users->getError());
                               $upload->delDir( './Upload/'.$upload->savePath);
                               $this->error($app->getError() . '添加失败', Url('App/index'));
                           } else {
                               $this->success('添加成功，页面跳转中...', Url('App/index'));
                           }
                       }
                   }
               }
           }
    }




            /*$info   =   $upload->uploadOne($_FILES['Pic']);
            if(!$info) {
                $this->error($upload->getError());
            }else{// 上传成功 获取上传文件信息

                if(!$result){
                    $this->error($validate->getError()."添加失败");//$app->getError在db\exception\Connection.php中的第732行
                }
                else{
                    $data=array(
                        'AppName'=>$_POST['AppName'],
                        'SymbolName'=>$_POST['SymbolName'],
                        'AppStatus'=>$_POST['AppStatus'],
                        'CategoryID'=>$_POST['CategoryID'],
                        'AppDetail'=>$_POST['AppDetail'],
                        'Pic'=>$info['savepath'].$info['savename'],
                        'UserID'=>request()->session('UserID'),
                        'Time'=>time()
                    );
                    $app =db('apps');
                    if(!$app->insert($data))
                    {
                        //exit($Users->getError());
                        $this->error($app->getError().'添加失败',Url('App/index'));
                    }
                    else
                    {
                        $this->success('添加成功，页面跳转中...',Url('App/index'));
                    }
                }
            }
        }
    }*/


    /**
     * app详情
     */
    public function detail(){
        $data=array(
            "AppName"  => Input('AppName')
        );
        $Dao=db("apps");
        $condition['AppName']=$data['AppName'];
        $result = $Dao->where($condition)->select();
        /*$Dao1=db("versions");
        $result1 = $Dao1->where($condition)->select();*/
        if(!$result){
            $this->error("数据查询出错",Url('App/index'));
        }
        else{
            $this->assign('data',$result);
            /*$this->assign('vdata',$result1);*/
            /*var_dump($result);*/
            return $this->fetch("App/detail");
        }
    }

    /**
     * 修改app
     */
    public function modify(){
        $data=array(
            "AppID"  => Input('AppID')
        );
        $Dao=db("apps");
        $condition['AppID']=$data['AppID'];
        $result = $Dao->where('AppID','like',$condition['AppID'])->find();
        if(!$result){
            $this->error("数据查询出错",Url('App/index'));
        }
        else{
            $this->assign('data',$result);
            return $this->fetch("App/modify");
        }
    }

    /**
     * 删除app
     */
    public function delete(){
        $upload = new Upload(Config::get(), 'Local');
        $data=array(
            "AppID"  => Input('get.AppID')
        );
        $Dao=db("apps");
        $deletePath = request()->session('UserID').'/'.$data['AppID'].'/';
        $condition['AppID']=$data['AppID'];
        $result = $Dao->where($condition)->delete();
		db('apps')->count()-1;//心怡修改////////////////////////
        $view=new View();
        if(!$result){
            $this->error("数据删除出错",Url('App/index'));
        }
        else{
            $upload->delDir( './Upload/'.$deletePath);
            $this->success("App删除成功",Url('App/index'));
        }
    }


    /**
     * 提交修改文件
     */

    public function modifyok(){
        $AppID=Input('AppID');
//        $c['AppID']=$AppID;
        $SymbolName=Input('SymbolName');
        $da=array(
            'AppName'=>Input('AppName'),
            'SymbolName'=>Input('SymbolName'),
            'VersionID'=>Input('VersionID'),
            'CategoryID'=>Input('CategotyID'),
            'AppDetail'=>Input('AppDetail'),
        );
        $app= db('apps');
        $result=$app->where('SymbolName','=',$SymbolName)->where('AppID','<>',$AppID)->find();
        if($da['AppName']==""){
            $this->error("应用名字不能为空");
        }
        else if($da['SymbolName']==""||$result){
            $this->error("内部名不能为空或者该内部名已存在");
        }
        else  if($da['VersionID']==""){
            $this->error("版本号不能为空");
        }
        else if($da['CategoryID']==""){
            $this->error("分类不能为空");
        }
        else if($da['AppDetail']==""){
            $this->error("应用描述不能为空");
        }
        else if($app->where('AppID','=',$AppID)->update($da)){
            $this->success('APP信息修改成功');
        }
        else{
            $this->error("APP信息修改失败");
        }
    }



    /**
     * app审核
     */
    public function review($page = 1){
        $number=config('PAGE_APP_ROW');
        /*if(!$this->isAdmin()){
            $this->error("您访问的页面不存在");
        }*/
        $list=db('apps')->where('AppStatus',0)->paginate($number,db('apps')->where('AppStatus',0)->count());
        $page=$list->render();
        $this->assign('list', $list);        // 分页赋值
        $this->assign('page', $page);        // 分页赋值
       return $this->fetch("App/review");
    }

    /**
     * app处理审核
     */
    public function reviewok(){
//        $data=array(
//            "AppID"  => Input('path.3')
//        );

        $Dao=db('apps');
        $AppID=Input('AppID');
        $data['AppStatus']=1;
        $result = $Dao->where('AppID',$AppID)->update($data);
//        var_dump($result)
//            ;die();
        $re=db('apps')->where('AppID','like',$AppID)->where('AppStatus','=',1)->find();
        if(!$re){
            $this->error("数据审核出错");
        }
        else{
            $this->success("App审核成功");
        }
    }

    /**
     * app搜索
     */
    public function search(){
        $appid=Input('post.search');
        $Dao = db("apps");
        if($appid){
            $condition['AppName|AppID']=array('like',"%$appid%");
            $result = $Dao->where($condition)->select();
            if(!$result){
                $this->error("您查询的App不存在!",Url('index'));
            }
            else{
                $this->assign('list',$result);
                return $this->fetch("App/index");
            }
        }
        else{
            $this->error("您查询操作失败!",Url('Index/searchcourse'));
        }
    }

    public function searchByStatus(){
        /*$Status=Input('post.search');*/
        $Status=$_POST['search'];
        $AppName=Input('AppName');
        $Dao = db("apps");
        if($Status!=null){
            $condition['AppStatus']=array('=',$Status);
            $condition['AppName']=array('=',$AppName);
            $result = $Dao->where($condition)->select();
            if(!$result){
                $this->error("您查询的该状态的应用不存在!",Url('App/index'));
            }
            else{
                $this->assign('data',$result);
                return $this->fetch("App/detail");
            }
        }
        else{
            $this->error("您查询操作失败!",Url('Index/'));
        }
    }
}