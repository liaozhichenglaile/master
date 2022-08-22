<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 15:23
 */

namespace app\common\validate;


class AdminValidate extends \think\Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名'    =>    ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'username|账号' => ['require', 'alphaNum'],
		'password|密码' => ['require', 'alphaNum'],
		'captcha_code|验证码' => ['require'],
		'captcha_key|验证码key' => ['require'],
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名'    =>    '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	/**
	 * 场景
	 *
	 * @var string[]
	 */
	protected $scene = [
		'login' => ['username', 'password', 'captcha_code', 'captcha_key']
	];

	public function sceneSave(){
		return $this->only(['username','password'])->append('username',['unique'=>'admin']);
	}
}
