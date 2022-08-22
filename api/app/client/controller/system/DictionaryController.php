<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:09
 */

namespace app\client\controller\system;


use app\services\system\DictionaryServices;
use think\App;

class DictionaryController extends \app\client\controller\BaseController
{
	/**
	 * @var DictionaryServices
	 */
	protected $services;

	public function __construct(App $app, DictionaryServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['code'],
			['name'],
			['type_code'],
			['type_name'],
			['page', 0],
			['limit', 0]
		]);

		return $this->successful($this->services->getList($data));
	}
}
