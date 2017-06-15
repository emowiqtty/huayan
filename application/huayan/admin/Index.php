<?php
namespace app\huayan\admin;

use app\admin\controller\Admin;
use app\huayan\model\Huayan as HuayanModel;
use app\common\builder\ZBuilder;
use think\Db;

/**
 * huyan 后台模块
 */
class Index extends Admin
{

	// 主页
	public function index()
	{	
		$total = Db::name('huayan_article')->count();
		$datainfo['total'] =235;
		return $this->fetch('',[
				'datainfo' => $datainfo,
			]);
	}

	//  新增数据
	public function add()
	{
		if(request()->isPost()){
            $data = input('post.');
            $data['addTime'] = time();
            $data['touser'] = get_nickname();
            if(session('user_auth.role_name') == '二水厂'){
            	$data['isWaterworks'] = 2;
            }else if(session('user_auth.role_name') == '三水厂'){
            	$data['isWaterworks'] = 3;
            }else{
            	$data['isWaterworks'] = 0;
            }

            


            //dump($data); die;

	    	$res = Db::name('huayan_article')->insert($data);
	    	$baobiao['article_id'] = Db::name('huayan_article')->getLastInsID();
		


            $baobiao['data'] = json_encode(array($data['rz']));
            dump($baobiao['data']); die;
	    	$resbaobiao = Db::name('huayan_baobiao')->insert($baobiao);
	        if($res && $resbaobiao){
	            $this->success('添加成功');
	        }else{
	            $this->error('添加失败');
	        }
        }else{
        	$avatar = get_avatar();
        	return $this->fetch('',[
        			'avatar' => $avatar,
        		]);
        }
		
	}

	public function delete($ids = null)
    {
        if ($ids === null) $this->error('参数错误');

        $document_id    = is_array($ids) ? '' : $ids;

        // 删除数据
        $deldata = Db::name('huayan_article')->where('id', 'in', $ids)->delete();
        if($deldata){
	            $this->success('删除成功');
	    }else{
	            $this->error('删除失败');
	    }

    }



	public function hylist()
	{
		$map = $this->getMap();
		// 读取化验数据
		$data_list = Db::name('huayan_article')->where($map)->select();
		
		// 查看按钮
		$btn_find = [
            'title' => '查看',
            'icon'  => 'fa fa-fw fa-key',
            'href'  => url('find', ['uid' => '__id__'])
        ];
		// 使用ZBuilder
		return ZBuilder::make('table')
			->setPageTitle('列表')
			->addTopButton('add') // 添加顶部按钮
			->addTopButton('delete') // 添加顶部按钮
			->addRightButtons('edit,delete') // 批量添加右侧按钮
			->addRightButton('custom', $btn_find) // 添加授权按钮
			->setSearch(['id' => 'ID', 'touser' => '填报人员']) // 设置搜索参数
			->setPageTips('demo', 'danger')
		    ->addColumns([ // 批量添加列
		         ['id', 'ID'],
		         ['touser', '填报人员'],
		         ['isWaterworks', '所属部门', 'status','', ['管理员', '一水厂', '二水厂', '三水厂']],
		         ['addTime', '添加时间' , 'datetime'],
		         ['right_button', '操作', 'btn']

		    ])
		    ->setRowList($data_list) // 设置表格数据
		    ->fetch(); // 渲染页面

	}

	public function find($uid)
	{
		$info = Db::name('huayan_article')->where('id', $uid)->find();
		return $this->fetch('',[
				'info' => $info,
			]);
	}



}

