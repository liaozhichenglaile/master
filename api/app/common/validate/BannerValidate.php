<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:02
 */

namespace app\common\validate;


class BannerValidate extends \think\Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名' =>  ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'id' => ['require'],
		'img' => ['require'],
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名' =>  '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	protected $scene = [
		'save' => ['img'],
		'delete' => ['id']
	];
}
