<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:02
 */

namespace app\common\validate;


class DictionaryValidate extends \think\Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名' =>  ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'id' => ['require'],
		'name|名词名称' => ['require', 'max' => 20],
		'type_code|类型CODE' => ['require', 'max' => 20, 'alphaDash'],
		'type_name|类型名称' => ['require', 'max' => 20],
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名' =>  '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	protected $scene = [
		'save' => ['name', 'type_code', 'type_name'],
		'delete' => ['id']
	];
}
