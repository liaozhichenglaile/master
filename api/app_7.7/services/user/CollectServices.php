<?php
/*
 * @Author: dashing
 * @Date: 2021/2/1 10:12
 */

namespace app\services\user;


use app\dao\user\CollectDao;

class CollectServices extends \app\services\BaseServices
{
	public function __construct(CollectDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * 列表
	 * @param $where
	 * @param $with
	 * @return array
	 */
	public function getList($where, $with = [])
	{
		$list = $this->dao->getList($where, $where['page'], $where['limit'], $with);
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
}
