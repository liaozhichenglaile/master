<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 18:28
 */

namespace app\admin\controller\user;


use app\common\validate\AppointmentValidate;
use app\services\user\AppointmentServices;
use think\App;

class AppointmentController extends \app\admin\controller\BaseController
{
	/**
	 * @var AppointmentServices
	 */
	protected $services;

	public function __construct(App $app, AppointmentServices $services)
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
			['reserved_tel'],
			['state'],
			['audit_time'],
			['page', 0],
			['limit', 0]
		]);

		$with = ['user' => function ($query) {
			$query->field(['id', 'head_img', 'nickname', 'tel'])->bind(['head_img', 'nickname', 'reserved_tel' => 'tel']);
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

		$this->validate($data, AppointmentValidate::class . '.audit');

		$data['audit_time'] = $this->now;

		return $this->successful($this->services->handleSave($data));
	}
}
