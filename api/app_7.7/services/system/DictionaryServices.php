<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:06
 */

namespace app\services\system;


use app\dao\system\DictionaryDao;

class DictionaryServices extends \app\services\BaseServices
{
	public function __construct(DictionaryDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * 列表
	 * @param $where
	 * @return array
	 */
	public function getList($where)
	{
		$list = $this->dao->getList($where, $where['page'], $where['limit']);
		$count = $this->dao->getCount($where);
		return compact('count', 'list');
	}

	/**
	 * 添加编辑
	 * @param $data
	 * @return mixed
	 */
	public function handleSave($data)
	{
		return $this->dao->handleSave($data);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function handleDelete($id)
	{
		return $this->dao->handleDelete($id);
	}

	public function getType()
	{
		return $this->dao->group('type_code')->column('type_name', 'type_code');
	}
}
