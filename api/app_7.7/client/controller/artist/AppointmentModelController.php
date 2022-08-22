<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 10:52
 */

namespace app\client\controller\artist;


use app\services\user\AppointmentModelServices;
use think\App;
use think\exception\ValidateException;

class AppointmentModelController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, AppointmentModelServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['page', 0],
			['limit', 0],
		]);

		$data['uid'] = $this->user->id;

		$with = ['artist'];

		return $this->successful($this->services->getList($data, $with));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['artist_uid'],
		]);

		$data['uid'] = $this->user->id;

		if ($data['artist_uid'] == $data['uid']) {
			throw new ValidateException('自己不能预约自己哦!');
		}

		$ret = $this->services->handleSave($data);

		event('VirtualFansEvent', ['uid' => $data['artist_uid'], 'virtual_fans' => 17]);

		return $this->successful($ret);
	}
}
