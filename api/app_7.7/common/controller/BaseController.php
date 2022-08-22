<?php
/*
 * @Author: dahing
 * @Date: 2020-06-04 15:47:56
 * @LastEditors:
 * @LastEditTime: 2020-06-16 14:56:06
 */

declare(strict_types=1);

namespace app\common\controller;

use think\App;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
	/**
	 * Request实例
	 * @var \app\Request
	 */
	protected $request;

	/**
	 * 应用实例
	 * @var App
	 */
	protected $app;

	/**
	 * 是否批量验证
	 * @var bool
	 */
	protected $batchValidate = false;

	/**
	 * 控制器中间件
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * 请求时间
	 *
	 * @var int
	 */
	protected $now;

	/**
	 * 登录用户
	 */
	protected $user;

	/**
	 * 构造方法
	 * @access public
	 * @param App $app 应用对象
	 */
	protected function __construct(App $app)
	{
		$this->app = $app;
		$this->request = $this->app->request;
		$this->now = $this->request->time();

		// 控制器初始化
		$this->initialize();
	}

	// 初始化
	protected function initialize()
	{
	}

	/**
	 * 验证数据
	 * @access protected
	 * @param array $data 数据
	 * @param string|array $validate 验证器名或者验证规则数组
	 * @param array $message 提示信息
	 * @param bool $batch 是否批量验证
	 * @return array|string|true
	 */
	protected function validate(array $data, $validate, array $message = [], bool $batch = false)
	{
		if (is_array($validate)) {
			$v = new Validate();
			$v->rule($validate);
		} else {
			if (strpos($validate, '.')) {
				// 支持场景
				[$validate, $scene] = explode('.', $validate);
			}
			$class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
			$v = new $class();
			if (!empty($scene)) {
				$v->scene($scene);
			}
		}

		$v->message($message);

		// 是否批量验证
		if ($batch || $this->batchValidate) {
			$v->batch(true);
		}

		return $v->failException(true)->check($data);
	}

	/**
	 * 返回请求
	 *
	 * @param string $code code码
	 * @param string $message message
	 * @param int|string|array $data 数据
	 * @return \think\Response
	 */
	public function response($code, $message, $data = -1)
	{
		if ($data === -1) {
			return json(compact('code', 'message'))->header(['Access-Control-Expose-Headers' => 'Authorization']);
		}

		return json(compact('code', 'message', 'data'))->header(['Access-Control-Expose-Headers' => 'Authorization']);
	}

	/**
	 * 失败返回
	 * @param mixed ...$ret
	 * @return \think\Response
	 */
	public function fail(...$ret)
	{
		$count = count($ret);

		$data = [];

		$firstParam = current($ret);
		$endParam = end($ret);

		switch ($count) {
			case 1:
				[$code, $msg] = [ResponseCode::FailCode, $endParam];
				break;
			case 2:
				[$code, $msg, $data] = is_numeric($firstParam) ? [$firstParam, $endParam, []] : [ResponseCode::FailCode, $firstParam, $endParam];
				break;
			case 3:
				[$code, $msg, $data] = $ret;
				break;
			default:
				[$code, $msg] = [ResponseCode::FailCode, $endParam];
		}

		return $this->response($code, $msg, $data);
	}

	/**
	 * 成功返回
	 * @param mixed ...$ret
	 * @return \think\Response
	 */
	public function successful(...$ret)
	{
		$count = count($ret);

		$data = [];

		$firstParam = current($ret);
		$endParam = end($ret);

		switch ($count) {
			case 1:
				[$code, $msg, $data] = is_string($endParam) ? [ResponseCode::Success, $endParam, []] : [ResponseCode::Success, '成功', $endParam];
				break;
			case 2:
				[$code, $msg, $data] = is_numeric($firstParam) ? [$firstParam, $endParam, []] : [ResponseCode::Success, $firstParam, $endParam];
				break;
			case 3:
				[$code, $msg, $data] = $ret;
				break;
			default:
				[$code, $msg] = [ResponseCode::Success, $endParam];
		}

		return $this->response($code, $msg, $data);
	}
}
