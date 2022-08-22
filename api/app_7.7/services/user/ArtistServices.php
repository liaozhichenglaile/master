<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 17:19
 */

namespace app\services\user;


use app\dao\user\ArtistDao;

class ArtistServices extends \app\services\BaseServices
{
	public function __construct(ArtistDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * 列表
	 * @param $where
	 * @param $with
	 * @param $withCount
	 * @param string $field
	 * @return array
	 */
	public function getList($where, $with = [], $withCount = [], $field = '*')
	{
		$list = $this->dao->getList($where, $where['page'], $where['limit'], $with, $withCount, $field);
		$count = $this->dao->getCount($where);
		return compact('count', 'list');
	}

	/**
	 * 添加编辑
	 * @param $data
	 * @return mixed
	 */
	public function handleSave($data)
	{
		return $this->dao->handleSave($data);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function delete($id)
	{
		return $this->dao->handleDelete($id);
	}

	public function getDetail($data, $with = [], $withCount = [])
	{
		return $this->dao->search($data)->with($with)->withCount($withCount)->findOrFail();
	}

	/**
	 * 涨粉丝
	 * @param $data
	 */
	public function virtualFans($data)
	{
		$model = $this->dao->where(['uid' => $data['uid']])->find();
		if ($model) {
			$model->virtual_fans += $data['virtual_fans'];
			$model->save();
		}
	}
}
