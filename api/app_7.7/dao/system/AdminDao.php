<?php
/*
 * @Author: dashing
 * @Date: 2020/11/5 14:52
 */

namespace app\dao\system;


use app\common\model\Admin;
use thans\jwt\facade\JWTAuth;
use think\exception\ValidateException;
use think\facade\Cache;

class AdminDao extends \app\dao\BaseDao
{

	/**
	 * @inheritDoc
	 */
	protected function setModel()
	{
		return Admin::class;
	}

	/**
	 * 获取列表
	 * @param array $where
	 * @param int $page
	 * @param int $limit
	 * @param string $field
	 * @param array $with
	 * @return array
	 */
	public function getList(array $where, int $page, int $limit, string $field = '*', $with = [])
	{
		return $this->search($where)->field($field)->page($page, $limit)->with($with)->order('id desc')->select()->toArray();
	}

	/**
	 * 获取特定条件的总数
	 * @param array $where
	 * @return array|int
	 */
	public function getCount(array $where)
	{
		return $this->search($where)->count();
	}

	/**
	 * 登录
	 * @param $username
	 * @param $password
	 * @return array
	 */
	public function login($username, $password)
	{
	    
	  
		$password = set_password($password);
     
		if (!$model = $this->getModel()->where(compact('username', 'password'))->find()) {
			throw new ValidateException('用户不存在或密码错误请重试');
		}

		if (!$model->state) {
			throw new ValidateException('此账号已被禁用');
		}

		Cache::delete('admin_user:info:' . $model->id);

		$token = JWTAuth::builder(['admin_uid' => $model->id]);

		return [
			'token' => $token,
			'token_type' => 'bearer ',
			'expires_in' => env('JWT_TTL'),
			'nickname' => $model->nickname
		];
	}

	/**
	 * 添加编辑
	 * @param $data
	 * @return mixed
	 */
	public function handleSave($data)
	{
		$model = $this->getModel();

		if ($data['id'] > 0) {
			$model = $model->findOrFail($data['id']);
		} else {
			$model = $model->where(['username' => $data['username']])->findOrEmpty();
			unset($data['id']);
		}

		if ($model->password !== $data['password']) {
			$data['password'] = set_password($data['password']);
		}

		$model->save($data);

		return $model;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function handleDelete($id)
	{
		$model = $this->getModel()->findOrFail($id);
		return $model->delete();
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function handleChangeState($id)
	{
		$model = $this->getModel()->findOrFail($id);
		$model->state = $model->state ? 0 : 1;
		return $model->save();
	}
}
