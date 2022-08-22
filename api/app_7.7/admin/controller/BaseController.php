<?php
/*
 * @Author: dashing
 * @Date: 2020/9/3 15:26
 */

namespace app\admin\controller;


use think\App;

/**
 * Class BaseController
 * @package app\admin\controller
 */
abstract class BaseController extends \app\common\controller\BaseController
{
	/**
	 * @var \app\common\model\Admin 登录用户模型
	 */
	protected $user;

	/**
	 * BaseController constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->user = $app->adminUser ?? null;
	}
}
