<?php
namespace app\huayan\model;

use think\Model as ThinkModel;

class Article extends ThinkModel
{
	//protected $autoWriteTimestamp = true;
	public function adddata($data)
	{
		return $this->save($data);
	}

}