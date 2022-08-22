<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:09
 */

namespace app\admin\controller\system;


use app\common\validate\BannerValidate;
use app\services\system\BannerServices;
use think\App;

class BannerController extends \app\admin\controller\BaseController
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

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['img', ''],
			['jump_url', ''],
		]);

		$this->validate($data, BannerValidate::class . '.save');

		return $this->successful($this->services->handleSave($data));
	}

	public function handleDelete()
	{
		$data = $this->request->postMore([
			['id']
		]);

		$this->validate($data, BannerValidate::class . '.delete');

		return $this->successful($this->services->handleDelete($data['id']));
	}
}
