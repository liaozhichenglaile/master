<?php
/*
 * @Author: dashing
 * @Date: 2020/11/5 14:35
 */

namespace app\common\model;


use think\facade\Cache;

class Admin extends BaseModel
{

	/**
	 * @param $id
	 * @return array|mixed|\think\Model
	 */
	public static function GetInfo($id)
	{
		if (!$model = Cache::get('admin_user:info:' . $id)) {
			$model = self::where(['id' => $id])->findOrFail();

			Cache::set('admin_user:info:' . $model->id, $model, 7 * 24 * 60 * 60);
		}

		return $model;
	}

	/**
	 * @param \think\Model $model
	 */
	public static function onAfterUpdate($model)
	{
		Cache::delete('admin_user:info:' . $model->id);
	}
}
