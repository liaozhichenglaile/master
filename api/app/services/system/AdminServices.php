<?php
/*
 * @Author: dashing
 * @Date: 2020/9/4 14:44
 */

namespace app\services\system;

use app\dao\system\AdminDao;

/**
 * Class AdminServices
 * @package app\services\system
 */
class AdminServices extends \app\services\BaseServices
{
	/**
	 * AdminServices constructor.
	 * @param AdminDao $dao
	 */
	public function __construct(AdminDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * 登录
	 * @param $data
	 * @return array
	 */
	public function login($data)
	{
		[$username, $password, $captcha] = array_values($data);

		return $this->dao->login($username, $password);
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

	/**
	 * @param $id
	 * @return bool
	 */
	public function changeState($id)
	{
		return $this->dao->handleChangeState($id);
	}
}
