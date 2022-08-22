<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 18:39
 */

namespace app\client\controller\artist;


use app\common\validate\FollowValidate;
use app\services\user\FollowServices;
use think\App;
use think\exception\ValidateException;

class FollowController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, FollowServices $services)
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

		$with = ['artist'];

		return $this->successful($this->services->getList($data, $with));
	}

	public function follow()
	{
		$data = $this->request->postMore([
			['id'],
			['artist_uid']
		]);

		$data['uid'] = $this->user->id;

		if ($data['artist_uid'] == $data['uid']) {
			throw new ValidateException('自己不能关注自己哦!');
		}

		$this->validate($data, FollowValidate::class . '.save');

		$ret = $this->services->handleSave($data);

		event('VirtualFansEvent', ['uid' => $data['artist_uid'], 'virtual_fans' => 23]);

		return $this->successful($ret);
	}

	public function handleDelete()
	{
		$data = $this->request->postMore([
			['id'],
		]);

		return $this->successful($this->services->handleDelete($data['id']));
	}
}
