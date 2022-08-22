<?php
/*
 * @Author: dashing
 * @Date: 2020/11/5 14:40
 */

namespace app\admin\controller\system;


use app\common\validate\AdminValidate;
use app\services\system\AdminServices;
use edward\captcha\facade\CaptchaApi;
use thans\jwt\facade\JWTAuth;
use think\App;
use think\facade\Cache;

class AdminController extends \app\admin\controller\BaseController
{
	/**
	 * @var AdminServices
	 */
	protected $services;

	/**
	 * AdminController constructor.
	 * @param App $app
	 * @param AdminServices $services
	 */
	public function __construct(App $app, AdminServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	/**
	 * 登录
	 * @return \think\Response
	 */
	public function login()
	{
	    
	    
		$data = $this->request->postMore([
			['username', ''],
			['password', ''],
			['captcha_code', ''],
			['captcha_key', '']
		]);

		$this->validate($data, AdminValidate::class . '.login');

		if (!CaptchaApi::check($data['captcha_code'], $data['captcha_key'])) {
			return $this->fail('验证码错误');
		}

		return $this->successful('登录成功', $this->services->login($data));
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @return \think\Response
	 */
	public function logout()
	{
		JwtAuth::refresh();
		Cache::delete('admin_user:info:' . $this->user->id);

		return $this->successful('Successfully logged out');
	}

	/**
	 * 个人信息
	 * @return \think\Response
	 */
	public function me()
	{
		$this->user->roles = ['admin'];
		return $this->successful($this->user->hidden(['password']));
	}

	/**
	 * 生成验证码
	 * @return \think\Response
	 */
	public function createCaptcha()
	{
		$ret = CaptchaApi::create();

		unset($ret['code']);

		return $this->successful($ret);
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['page', 0],
			['limit', 0],
		]);

		return $this->successful($this->services->getList($data));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['username'],
			['password'],
		]);

		$this->validate($data, AdminValidate::class . '.save');

		return $this->successful($this->services->handleSave($data));
	}

	public function handleDelete()
	{
		$data['id'] = $this->request->postMore(['id']);

		return $this->successful($this->services->handleDelete($data['id']));
	}
}
