<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 18:18
 */

namespace app\admin\controller\user;


use app\common\validate\DynamicValidate;
use app\services\user\DynamicServices;
use think\App;

class DynamicController extends \app\admin\controller\BaseController
{
	/**
	 * @var DynamicServices
	 */
	protected $services;

	public function __construct(App $app, DynamicServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['nickname'],
			['add_time'],
			['tel'],
			['state'],
			['audit_time'],
			['page', 0],
			['limit', 0]
		]);

		$with = ['user' => function ($query) {
			$query->field(['id', 'head_img', 'nickname', 'tel', 'identity'])->bind(['head_img', 'nickname', 'tel', 'identity']);
		}];

		return $this->successful($this->services->getList($data, $with));
	}

	public function handleAudit()
	{
		$data = $this->request->postMore([
			['id'],
			['state', 1],
			['remark']
		]);

		$this->validate($data, DynamicValidate::class . '.audit');

		$data['audit_time'] = $this->now;

		return $this->successful($this->services->handleSave($data));
	}

	public function top()
	{
		$data = $this->request->postMore([
			'id',
			[['top_time', 'd'], 0]
		]);

		$data['top_time'] = $data['top_time'] === 0 ? $data['top_time'] : $this->now;

		return $this->successful($this->services->handleSave($data));
	}
}
