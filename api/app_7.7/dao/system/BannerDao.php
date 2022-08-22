<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 18:04
 */

namespace app\dao\system;


use app\common\model\Banner;

class BannerDao extends \app\dao\BaseDao
{

	/**
	 * @inheritDoc
	 */
	protected function setModel()
	{
		return Banner::class;
	}

	/**
	 * 获取列表
	 * @param array $where
	 * @param int $page
	 * @param int $limit
	 * @param string $field
	 * @param array $with
	 * @return array
	 */
	public function getList(array $where, int $page, int $limit, string $field = '*', $with = [])
	{
		return $this->search($where)->field($field)->page($page, $limit)->with($with)->order('id desc')->select()->toArray();
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
