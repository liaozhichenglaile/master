<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:09
 */

namespace app\admin\controller\system;


use app\common\validate\DictionaryValidate;
use app\services\system\DictionaryServices;
use think\App;

class DictionaryController extends \app\admin\controller\BaseController
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
			['name'],
			['type_code'],
			['type_name'],
			['page', 0],
			['limit', 0]
		]);

		return $this->successful($this->services->getList($data));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['name'],
			['type_code'],
			['type_name']
		]);

		$this->validate($data, DictionaryValidate::class . '.save');

		return $this->successful($this->services->handleSave($data));
	}

	public function handleDelete()
	{
		$data = $this->request->postMore([
			'id'
		]);

		$this->validate($data, DictionaryValidate::class . '.delete');

		return $this->successful($this->services->handleDelete($data['id']));
	}

	public function getType(){
		return $this->successful($this->services->getType());
	}
}
