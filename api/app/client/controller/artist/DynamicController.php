<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 17:45
 */

namespace app\client\controller\artist;


use app\common\validate\DynamicValidate;
use app\services\user\DynamicServices;
use think\App;

class DynamicController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, DynamicServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['is_me'],
			['uid'],
			['is_top'],
			['state', 1],
			['page', 0],
			['limit', 0]
		]);

		if ($data['is_me']) {
			$data['uid'] = $this->user->id;
			unset($data['is_me']);
		}

		return $this->successful($this->services->getList($data));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['content', ''],
			['img', ''],
			['state', 0],
		]);

		$data['uid'] = $this->user->id;

		$this->validate($data, DynamicValidate::class . '.save');

		if ($data['img']) {
			$data['img'] = explode(',', $data['img']);
		} else {
			$data['img'] = [];
		}

		return $this->successful($this->services->handleSave($data));
	}
}
