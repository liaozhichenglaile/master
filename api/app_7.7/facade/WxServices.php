<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 14:16
 */

namespace app\facade;

/**
 * Class WxServices
 * @package app\facade
 * @mixin \app\common\controller\WxServices
 */
class WxServices extends \think\Facade
{
	protected static function getFacadeClass()
	{
		return \app\common\controller\WxServices::class;
	}
}
