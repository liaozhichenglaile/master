<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 18:13
 */

namespace app\services\user;


use app\dao\user\AppointmentModelDao;

class AppointmentModelServices extends \app\services\BaseServices
{
	public function __construct(AppointmentModelDao $dao)
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
	public function delete($id)
	{
		return $this->dao->handleDelete($id);
	}
}
