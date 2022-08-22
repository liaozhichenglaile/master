<?php
declare (strict_types=1);

namespace app\common\model;


class Artist extends BaseModel
{
	protected $type = [
		'audit_time' => 'timestamp:Y-m-d',
		'add_time'   => 'timestamp:Y-m-d'
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
	 * 关联模型 : 粉丝
	 * @return \think\model\relation\HasMany
	 */
	public function fans()
	{
		return $this->hasMany(Follow::class, 'uid', 'uid');
	}

	/**
	 * 关联模型 : 关注
	 * @return \think\model\relation\HasOne
	 */
	public function follow()
	{
		return $this->hasOne(Follow::class, 'artist_uid', 'uid');
	}

	/**
	 * 关联模型 : 作品
	 * @return \think\model\relation\HasMany
	 */
	public function dynamic()
	{
		return $this->hasMany(Dynamic::class, 'uid', 'uid');
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
	 * 搜索器 : 姓名|艺名
	 * @param $query
	 * @param $value
	 */
	public function searchNameAttr($query, $value)
	{
		$query->where(function ($query) use ($value) {
			$query->whereOr('name', 'like', '%' . $value . '%')->whereOr('stage_name', 'like', '%' . $value . '%');
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
	 * 搜索器 : 申请时间
	 * @param $query
	 * @param $value
	 */
	public function searchAddTimeAttr($query, $value)
	{
		!is_array($value) ?: $query->whereBetweenTime('add_time', $value[0], $value[1]);
	}

	/**
	 * 搜索器 : id排序
	 * @param $query
	 */
	public function searchSortAttr($query)
	{
		$query->order('id', 'desc');
	}

	/**
	 * 搜索器 : 审核时间排序
	 * @param $query
	 * @param $value
	 */
	public function searchAuditTimeSortAttr($query, $value)
	{
		$query->order('audit_time', $value);
	}

	/**
	 * 搜索器 : 粉丝数排序
	 * @param $query
	 * @param $value
	 */
	public function searchFansNumSortAttr($query, $value)
	{
		$table = app()->make(Follow::class);

		$subSql = $table->field(['artist_uid', 'count(*) num'])->group('artist_uid')->buildSql();

		$query->join([$subSql => 'f'], env('database.prefix') . 'artist.uid = f.artist_uid')->order('num', $value);
	}

	/**
	 * 作品数排序
	 * @param $query
	 * @param $value
	 */
	public function searchDynamicNumSortAttr($query, $value)
	{
		$table = app()->make(Dynamic::class);

		$subSql = $table->field(['uid', 'count(*) num'])->group('uid')->buildSql();

		$query->join([$subSql => 'f'], env('database.prefix') . 'artist.uid = f.uid')->order('num', $value);
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

	/**
	 * 搜索器 : 类型
	 * @param $query
	 * @param $value
	 */
	public function searchArtistTypeAttr($query, $value)
	{
		$query->whereFindInSet('artist_type', $value);
	}

	public function searchHeightAttr($query, $value)
	{
		if ($value) {
			$query->whereBetween('height', $value);
		}
	}
}
