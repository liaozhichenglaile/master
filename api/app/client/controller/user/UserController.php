<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 11:23
 */

namespace app\client\controller\user;


use app\common\validate\UserValidate;
use app\facade\WxServices;
use app\services\user\UserServices;
use thans\jwt\facade\JWTAuth;
use think\App;
use think\exception\ValidateException;
use think\facade\Cache;

class UserController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, UserServices $services)
	{
	 
		parent::__construct($app);
		$this->services = $services;
	}

	/**
	 * 获取用户 session 信息
	 * @return \think\Response
	 */
	public function getSession()
	{
		$code = $this->request->post('code');

		$this->validate(['code' => $code], UserValidate::class . '.getSession');

		$wx = WxServices::getSession($code);

		if (!$openid = data_get($wx, 'openid', 0)) {
			throw new ValidateException(data_get($wx, 'errmsg'));
		}

		//$openid = 'o6z_M4mjlhC0DlC3_QkpRg6JH8e8';

		$model = $this->services->handleSave(['openid' => $openid, 'union_id' => data_get($wx, 'unionid', '')]);

		$token = JWTAuth::builder(['client_uid' => $model->id]);

		Cache::delete('client_user:info:' . $model->id);

		$ret = [
			'token' => $token,
			'token_type' => 'bearer ',
			'expires_in' => env('JWT_TTL'),
		];

		return $this->successful('请求成功', array_merge($ret, $wx));
	}

	/**
	 * 登录
	 * @return \think\Response
	 */
	public function login()
	{
	    
	  
		$request = $this->request;

		$data = $request->postMore([
			['code'],
			['iv'],
			['encrypted_data'],
		]);

		$this->validate($data, UserValidate::class . '.miniLogin');

		$wx = WxServices::getSession($data['code']);

		if (!$data['session_key'] = data_get($wx, 'session_key', 0)) {
			throw new ValidateException(data_get($wx, 'errmsg'));
		}

		if (preg_match('~micromessenger~i', $request->header('user-agent'))) {
			return $this->successful($this->services->miniLogin($data));
		}

		return $this->successful($this->services->miniLogin($data));
	}

	public function miniLogin()
	{
		$request = $this->request;

		$data = $request->postMore([
			['nickname', $this->user->nickname],
			['head_img', $this->user->head_img],
		]);

		$data['id'] = $this->user->id;

		if (preg_match('~micromessenger~i', $request->header('user-agent'))) {
			return $this->successful($this->services->handleSave($data));
		}

		return $this->successful($this->services->handleSave($data));
	}

	/**
	 * 小程序绑定手机
	 * @return \think\Response
	 */
	public function miniBindTel()
	{
		$request = $this->request;

		$data = $request->postMore([
			['code'],
			['iv'],
			['encrypted_data'],
		]);

		$data['id'] = $this->user->id;

		$this->validate($data, UserValidate::class . '.miniBindTel');

		$wx = WxServices::getSession($data['code']);

		if (!$data['session_key'] = data_get($wx, 'session_key', 0)) {
			throw new ValidateException(data_get($wx, 'errmsg'));
		}

		return $this->successful($this->services->miniBindTel($data));
	}

	/**
	 * 个人信息
	 * @return \think\Response
	 */
	public function me()
	{
	 //   dump(1111);die;
	    
		$withCount = ['fans', 'collect', 'follow'];

		$with = ['artist' => function ($query) {
			$query->field(['uid', 'virtual_fans'])->bind(['virtual_fans']);
		}];

		return $this->successful($this->services->getDetail(['id' => $this->user->id], $with, $withCount));
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @return \think\Response
	 */
	public function logout()
	{
		JwtAuth::refresh();
		Cache::delete('client_user:info:' . $this->user->id);

		return $this->successful('Successfully logged out');
	}
}
