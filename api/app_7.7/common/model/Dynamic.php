<?php
declare (strict_types=1);

namespace app\common\model;


class Dynamic extends BaseModel
{
	protected $type = [
		'audit_time' => 'timestamp:Y-m-d',
		'add_time' => 'timestamp:Y-m-d',
		'img' => 'json'
	];

	/**
	 * 关联模型 : 用户
	 * @return \think\model\relation\HasOne
	 */
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'uid');
	}

	/**
	 * 搜索器 : 昵称|uid
	 * @param $query
	 * @param $value
	 */
	public function searchNicknameAttr($query, $value)
	{
		is_numeric($value) ? $query->where('uid', $value) : $query->whereIn('uid', function ($query) use ($value) {
			$table = app()->make(User::class);
			$query->table($table->getTable())->whereLike('nickname', '%' . $value . '%')->field('id');
		});
	}

	/**
	 * 搜索器 : 手机号
	 * @param $query
	 * @param $value
	 */
	public function searchTelAttr($query, $value)
	{
		$query->whereIn('uid', function ($query) use ($value) {
			$table = app()->make(User::class);
			$query->table($table->getTable())->whereLike('tel', '%' . $value . '%')->field('id');
		});
	}

	/**
	 * 搜索器 : 提交时间
	 * @param $query
	 * @param $value
	 */
	public function searchAddTimeAttr($query, $value)
	{
		!is_array($value) ?: $query->whereBetweenTime('add_time', $value[0], $value[1]);
	}

	/**
	 * 搜索器 : 处理时间
	 * @param $query
	 * @param $value
	 */
	public function searchAuditTimeAttr($query, $value)
	{
		!is_array($value) ?: $query->whereBetweenTime('audit_time', $value[0], $value[1]);
	}

	/**
	 * 搜索器 : 已推荐的
	 * @param $query
	 * @param $value
	 */
	public function searchIsTopAttr($query, $value)
	{
		$query->where('top_time', '>', 0);
	}
}
