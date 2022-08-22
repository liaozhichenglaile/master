<?php
/*
 * @Author: dashing
 * @Date: 2021/2/1 10:15
 */

namespace app\client\controller\artist;


use app\common\validate\CollectValidate;
use app\services\user\CollectServices;
use think\App;

class CollectController extends \app\client\controller\BaseController
{
	/**
	 * @var CollectServices
	 */
	protected $services;

	public function __construct(App $app, CollectServices $services)
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

		$data['uid'] = $this->user->id;

		$with = ['dynamic'];

		return $this->successful($this->services->getList($data, $with));
	}

	public function follow()
	{
		$data = $this->request->postMore([
			['id'],
			['dynamic_id']
		]);

		$data['uid'] = $this->user->id;

		$this->validate($data, CollectValidate::class . '.save');

		return $this->successful($this->services->handleSave($data));
	}

	public function handleDelete()
	{
		$data = $this->request->postMore([
			['id'],
		]);

		return $this->successful($this->services->handleDelete($data['id']));
	}
}
