<?php
/*
 * @Author: dashing
 * @Date: 2020-09-03 16:21:40
 */

namespace app\services;

use think\facade\Db;

/**
 * Class BaseServices
 * @package app\services
 */
abstract class BaseServices
{
	/**
	 * 模型注入
	 * @var \think\model
	 */
	protected $dao;

	/**
	 * 数据库事务操作
	 * @param callable $closure
	 * @return mixed
	 */
	public function transaction(callable $closure)
	{
		return Db::transaction($closure);
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->dao, $name], $arguments);
	}
}
