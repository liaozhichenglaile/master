<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:09
 */

namespace app\client\controller\system;


use app\services\system\BannerServices;
use think\App;

class BannerController extends \app\client\controller\BaseController
{
	/**
	 * @var BannerServices
	 */
	protected $services;

	public function __construct(App $app, BannerServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['page', 0],
			['limit', 0]
		]);

		return $this->successful($this->services->getList($data));
	}
}
