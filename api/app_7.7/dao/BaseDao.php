<?php
/*
 * @Author: dashing
 * @Date: 2020/9/3 16:24
 */

namespace app\dao;

use think\Model;

/**
 * Class BaseDao
 * @package app\dao
 */
abstract class BaseDao
{
	/**
	 * @var \think\model 数据操作模型
	 */
	protected $dao;

	/**
	 * BaseDao constructor.
	 */
	public function __construct()
	{
		$model = app()->make($this->setModel());

		$this->dao = $model;
	}

	/**
	 * 设置模型
	 * @return mixed
	 */
	abstract protected function setModel();

	/**
	 * 获取模型
	 * @return mixed|model
	 */
	public function getModel()
	{
		return $this->dao;
	}

	/**
	 * @param $where
	 * @return Model
	 */
	public function search($where)
	{
		unset($where['limit'], $where['page']);

		$where = array_filter($where, function ($val) {
			return $val !== '' && $val !== null && $val !== 'null' && $val !== [];
		});

		return $this->getModel()->withSearch(array_keys($where), $where);
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->getModel(), $name], $arguments);
	}
}
