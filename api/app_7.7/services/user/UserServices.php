<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 16:02
 */

namespace app\services\user;


use app\dao\user\UserDao;
use app\facade\WxServices;
use think\exception\ValidateException;
use think\helper\Arr;

class UserServices extends \app\services\BaseServices
{
	public function __construct(UserDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * 列表
	 * @param $where
	 * @return array
	 */
	public function getList($where)
	{
		$list = $this->dao->getList($where, $where['page'], $where['limit']);
		$count = $this->dao->getCount($where);
		return compact('count', 'list');
	}

	/**
	 * 添加编辑
	 * @param $data
	 * @return mixed
	 */
	public function handleSave($data)
	{
		return $this->dao->handleSave($data);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function delete($id)
	{
		return $this->dao->handleDelete($id);
	}

	/**
	 * 小程序微信授权登录
	 * @param $data
	 * @return array
	 */
	public function miniLogin($data)
	{
		$ret = WxServices::decryptData($data['session_key'], $data['iv'], $data['encrypted_data']);

		$openid = Arr::get($ret, 'openId');

		$nickname = Arr::get($ret, 'nickName');

		$head_img = Arr::get($ret, 'avatarUrl');

		$union_id = Arr::get($ret, 'unionId');

		return $this->dao->handleSave(compact('openid', 'nickname', 'head_img', 'union_id'));
	}

	/**
	 * 小程序绑定手机
	 * @param $data
	 * @return mixed
	 */
	public function miniBindTel($data)
	{
		$ret = WxServices::decryptData($data['session_key'], $data['iv'], $data['encrypted_data']);

		$tel = Arr::get($ret, 'purePhoneNumber');

		if ($this->dao->where(['tel' => $tel])->where('id', '<>', $data['id'])->find()) {
			throw new ValidateException('手机号已被绑定');
		}

		return $this->dao->handleSave(['tel' => $tel, 'id' => $data['id']]);
	}

	public function getDetail($data, $with = [], $withCount = [])
	{
		return $this->dao->search($data)->with($with)->withCount($withCount)->findOrFail();
	}
}
