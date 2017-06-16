<?php
namespace app\huayan\model;

use think\Model as ThinkModel;
use think\Db;

class Huayan extends ThinkModel
{
	/**
     * 新增或更新文档
     */
    public function saveData($id =null)
    {
    	if(!request()->isPost()){
            $this->error('请求失败');
        }
    	$data = request()->post();
    	//dump($data);die;

    	$data['addTime'] = time();
        $data['touser'] = session('user_auth.nickname');
        if(session('user_auth.role_name') == '二水厂'){
        	$data['isWaterworks'] = 2;
        }else if(session('user_auth.role_name') == '三水厂'){
        	$data['isWaterworks'] = 3;
        }else{
        	$data['isWaterworks'] = 0;
        }

    	$res = Db::name('huayan_article')->insert($data,['id' => $id]);
    	$a_id = Db::name('huayan_article')->getLastInsID();
        foreach ($data['time'] as $key => $val) {
        	$baobiao[$key]['time']=$val;
        	$baobiao[$key]['ccseyhl']=$data['ccseyhl'][$key];
        	$baobiao[$key]['ccszd']=$data['ccszd'][$key];
        	$baobiao[$key]['yszd']=$data['yszd'][$key];
        	$baobiao[$key]['lqzd']=$data['lqzd'][$key];
        	$baobiao[$key]['a_id']=$a_id;
        }
        //halt($baobiao);

    	$resbaobiao = Db::name('huayan_baobiao')->insertAll($baobiao,['id' => $id]);
        if($res && $resbaobiao){
            return true;
        }else{
            return false;
        }
    }

}