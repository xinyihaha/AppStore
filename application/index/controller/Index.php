<?php
namespace app\index\controller;
use app\common\controller\LC;
use think\View;
use think\Controller;
use Think\Request;
use Think\Validate;
class Index extends LC
{
    public function index()
    {
        $view=new View();
        header('Content-Type:text/html; charset=utf-8');
        $Dao=db("apps");
        $flag=0;
        $list=$Dao->order('DownloadCount desc')->limit(5)->select();
       $down=$Dao->field('AppName,COUNT(DownloadCount)')->group('AppName')->select();//查询类似应用的相应总量；
        $down=array(
            'sum'=>$Dao->SUM('DownloadCount'),
            'avg'=>$Dao->AVG('DownloadCount'),
            'max'=>$Dao->MAX('DownloadCount'),
            'count'=>$Dao->count('DownloadCount'),
        );
        $this->assign('down',$down);
        $this->assign('flag',$flag);
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function More()
    {
        $view=new View();
        header('Content-Type:text/html; charset=utf-8');
        $Dao=db("apps");
        $flag=1;
        $list=$Dao->order('DownloadCount desc')->select();
        $down=$Dao->field('AppName,COUNT(DownloadCount)')->group('AppName')->select();//查询类似应用的相应总量；
        $down=array(
            'sum'=>$Dao->SUM('DownloadCount'),
            'avg'=>$Dao->AVG('DownloadCount'),
            'max'=>$Dao->MAX('DownloadCount'),
            'count'=>$Dao->count('DownloadCount'),
        );
        $this->assign('down',$down);
        $this->assign('flag',$flag);
        $this->assign('list',$list);
        return $this->fetch("Index/index");
    }
}
