<?php
declare (strict_types=1);

namespace app\common\validate;

use think\Validate;

class DynamicValidate extends Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名' =>  ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'id' => ['require'],
		'state' => ['require', 'number', 'between' => '0,2'],
		'content' => ['requireWithout' => 'img'],
		'img' => ['requireWithout' => 'content']
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名' =>  '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	protected $scene = [
		'audit' => ['id', 'state'],
		'save' => ['content', 'img']
	];
}
