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
		$datainfo['total'] =$total;

		// 日期
		$riqi = Db::name('huayan_article')->limit(7)
									      ->order('id', 'asc')
									      ->column('addTime');
		foreach ($riqi as $key => $val) {
			$rq[] = date("Y-m-d",$val );
		}
		// 7日温度
		$waterTemperatureYs7Day = Db::name('huayan_article')->limit(7)
														    ->order('id', 'asc')
														    ->column('waterTemperatureYs');
	    $waterTemperatureCcs7Day = Db::name('huayan_article')->limit(7)
														    ->order('id', 'asc')
														    ->column('waterTemperatureCcs');

	    // 7日PH
		$phys7Day = Db::name('huayan_article')->limit(7)
														    ->order('id', 'asc')
														    ->column('phYs');
	    $phccs7Day = Db::name('huayan_article')->limit(7)
														    ->order('id', 'asc')
														    ->column('phCcs');

	    $res['riqi'] = json_encode($rq);
		$res['yswd'] = json_encode($waterTemperatureYs7Day);
		$res['ccswd'] = json_encode($waterTemperatureCcs7Day);
		$res['ysph'] = json_encode($phys7Day);
		$res['ccsph'] = json_encode($phccs7Day);
		//dump($res7day);

		return $this->fetch('',[
				'datainfo' => $datainfo,
				'res' => $res,
			]);
	}

	//  新增数据
	public function add()
	{
		// 保存文档数据
        if ($this->request->isPost()) {
            $Huayan = new HuayanModel();
            $result = $Huayan->saveData();
            if (false === $result) {
                $this->error('添加失败');
            }
            $this->success('添加成功');
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

    //  编辑数据
    public function edit($id = null)
    {	
    	$info = Db::name('huayan_article')->where('id', $id)->find();
		$baobiao = Db::name('huayan_baobiao')->where('a_id', $id)->select();
    	$avatar = get_avatar();
    	
    	// 保存文档数据
        if ($this->request->isPost()) {
            $Huayan = new HuayanModel();
            $result = $Huayan->saveData($id);
            if (false === $result) {
                $this->error('编辑失败');
            }
            $this->success('编辑成功');
        }

        return $this->fetch('',[
    			'avatar' => $avatar,
    			'info' => $info,
				'baobiao' => $baobiao,
    		]);
    }

	public function hylist()
	{
		$map = $this->getMap();
		// 读取化验数据
		$data_list = Db::name('huayan_article')->where($map)->select();


		
		// 查看按钮
		$btn_find = [
            'title' => '查看',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('find', ['uid' => '__id__'])
        ];

        // 导出按钮
        $btn_down = [
            'title' => '导出',
            'icon'  => 'fa fa-fw fa-anchor',
            'href'  => url('down', ['id' => '__id__'])
        ];
		// 使用ZBuilder
		return ZBuilder::make('table')
			->setPageTitle('列表')
			->addTopButton('add') // 添加顶部按钮
			->addTopButton('delete') // 添加顶部按钮
			->addRightButtons('edit,delete') // 批量添加右侧按钮
			->addRightButton('custom', $btn_find) // 添加查看按钮
			->addRightButton('custom', $btn_down) // 添加导出按钮
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
		$baobiao = Db::name('huayan_baobiao')->where('a_id', $uid)->select();
		//dump($baobiao);
		return $this->fetch('',[
				'info' => $info,
				'baobiao' => $baobiao,
			]);
	}

	public function down($id = null)
    {
    	// dump($id)
        // 查询数据
        $data = Db::name('huayan_article')->where('id', $id)->select();
        //dump($data);die;
        // 设置表头信息（对应字段名,宽度，显示表头名称）
        $cellName = [
            ['id', '10', 'ID'],

        ];
        // 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
        plugin_action('Excel/Excel/export', ['水质报表', $cellName, $data]);
    }



}

