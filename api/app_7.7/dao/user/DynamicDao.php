<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 17:43
 */

namespace app\dao\user;


use app\common\model\Dynamic;

class DynamicDao extends \app\dao\BaseDao
{

	/**
	 * @inheritDoc
	 */
	protected function setModel()
	{
		return Dynamic::class;
	}

	/**
	 * 获取列表
	 * @param array $where
	 * @param int $page
	 * @param int $limit
	 * @param string $field
	 * @param array $with
	 * @return \think\Collection
	 */
	public function getList(array $where, int $page, int $limit, $with = [], string $field = '*')
	{
		return $this->search($where)->field($field)->page($page, $limit)->with($with)->order('id desc')->select();
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

		if ($data['id'] > 0) {
			$model = $model->findOrFail($data['id']);
		}

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
