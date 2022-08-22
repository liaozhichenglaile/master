<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 17:54
 */

namespace app\client\controller\appointment;


use app\services\user\AppointmentServices;
use think\App;

class AppointmentController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, AppointmentServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['tel'],
			['name'],
			['page'],
			['limit'],
		]);

		$data['uid'] = $this->user->id;

		return $this->successful($this->services->getList($data));
	}

	public function handleSave()
	{
	 
		$data = $this->request->postMore([
			['id'],
		    ['tel'],
			['name'],
			['need'],
			['budget', 0],
		]);
        
		$data['uid'] = $this->user->id;
        
		return $this->successful($this->services->handleSave($data));
	}
}
