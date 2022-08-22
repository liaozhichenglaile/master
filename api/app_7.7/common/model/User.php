<?php
/*
 * @Author: dashing
 * @Date: 2020/11/5 14:36
 */

namespace app\common\model;


use think\exception\ValidateException;
use think\facade\Cache;

class User extends BaseModel
{

	/**
	 * @param $id
	 * @return array|mixed|\think\Model
	 */
	public static function GetInfo($id)
	{
		$key = 'client_user:info:' . $id;

		if (!$model = Cache::get($key)) {
			$model = self::where(['id' => $id])->find();

			if (!$model) {
				throw new ValidateException('账号被禁用');
			}

			Cache::set($key, $model, 7 * 24 * 60 * 60);
		}

		return $model;
	}

	/**
	 * @param \think\Model $model
	 */
	public static function onAfterUpdate($model)
	{
		Cache::delete('client_user:info:' . $model->id);
	}

	/**
	 * 关联模型 : 收藏
	 * @return \think\model\relation\HasMany
	 */
	public function collect()
	{
		return $this->hasMany(Collect::class, 'uid', 'id');
	}

	/**
	 * 关联模型 : 关注
	 * @return \think\model\relation\HasMany
	 */
	public function follow()
	{
		return $this->hasMany(Follow::class, 'uid', 'id');
	}

	/**
	 * 关联模型 : 粉丝
	 * @return \think\model\relation\HasMany
	 */
	public function fans()
	{
		return $this->hasMany(Follow::class, 'artist_uid', 'id');
	}

	/**
	 * 关联模型 : 艺人
	 * @return \think\model\relation\HasOne
	 */
	public function artist()
	{
		return $this->hasOne(Artist::class, 'uid', 'id');
	}

	/**
	 * 搜索器 : uid|昵称
	 * @param $query
	 * @param $value
	 */
	public function searchNameAttr($query, $value)
	{
		is_numeric($value) ? $query->where('id', $value) : $query->whereLike('nickname', '%' . $value . '%');
	}

	public function searchAddTimeAttr($query, $value)
	{
		!is_array($value) ?: $query->whereBetweenTime('add_time', $value[0], $value[1]);
	}
}
