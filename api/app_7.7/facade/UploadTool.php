<?php
/*
 * @Author: dahing
 * @Date: 2020-04-14 17:51:45
 * @LastEditors:
 * @LastEditTime: 2020-06-22 11:09:08
 */

namespace app\facade;

use think\Facade;

/**
 * 上传图片
 * Class UploadTool
 * @package app\facade
 * @method static string upload(object $file, string $type = 'qiniu') 上传图片
 * @method static string uploadDocumentFlow($imgUrl) 文件里上传
 */
class UploadTool extends Facade
{
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @return string
	 */
	protected static function getFacadeClass()
	{
		return \app\common\controller\UploadTool::class;
	}
}
