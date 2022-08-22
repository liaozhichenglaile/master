<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 17:13
 */

namespace app\dao\user;


use app\common\model\Artist;

class ArtisthouDao extends \app\dao\BaseDao
{

	/**
	 * @inheritDoc
	 */
	protected function setModel()
	{
		return Artist::class;
	}

	/**
	 * 获取列表
	 * @param array $where
	 * @param int $page
	 * @param int $limit
	 * @param array $with
	 * @param array $withCount
	 * @param string $field
	 * @return \think\Collection
	 */
	public function getList(array $where, int $page, int $limit, $with = [], $withCount = [], string $field = '*')
	{
		return $this->search($where)->field($field)->page($page, $limit)
			->with($with)->withCount($withCount)->order(['sort' => 'desc', 'update_time' => 'desc'])->select();
	}

	/**
	 * 获取特定条件的总数
	 * @param array $where
	 * @return array|int
	 */
	public function getCount(array $where)
	{
		return $this->search($where)->count();
	}

	/**
	 * 添加编辑
	 * @param $data
	 * @return mixed
	 */
	public function handleSave($data)
	{
		$model = $this->getModel();

		if (!empty($data['id']) && $data['id'] > 0) {
			$model = $model->findOrFail($data['id']);
		}

		if (!empty($data['uid'])) {
			$model = $model->where(['uid' => $data['uid']])->findOrEmpty();
			unset($data['id']);
		}

		if (!$model->isEmpty() && isset($data['state'])) {
			if ((int)$data['state'] === 1) {
				$model->user->identity = 1;
				$model->user->save();
				event('VirtualFansEvent', ['uid' => $model->uid, 'virtual_fans' => 36]);
			} else {
				$model->user->identity = 0;
				$model->user->save();
			}
		}
        $data['update_time']=time();
		$model->save($data);

		return $model;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function handleDelete($id)
	{
		$model = $this->getModel()->findOrFail($id);
		return $model->delete();
	}
}
