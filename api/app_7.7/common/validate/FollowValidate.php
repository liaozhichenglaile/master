<?php
declare (strict_types=1);

namespace app\common\validate;

use think\Validate;

class FollowValidate extends Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名' =>  ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'uid' => ['require'],
		'artist_uid' => ['require']
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名' =>  '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	protected $scene = [
		'save' => ['uid', 'artist_uid']
	];
}
