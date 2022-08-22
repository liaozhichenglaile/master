<?php
/*
 * @Author: dahing
 * @Date: 2020-04-15 14:46:28
 * @LastEditors:
 * @LastEditTime: 2020-06-27 15:56:58
 */

declare(strict_types=1);

namespace app\common\model;

use think\facade\Db;
use think\Model;

/**
 * 模型基类
 */
class BaseModel extends Model
{
	/**
	 * @inheritDoc
	 */
	protected $autoWriteTimestamp = true;

	/**
	 * @inheritDoc
	 */
	protected $createTime = 'add_time';

	/**
	 * @inheritDoc
	 */
	protected $updateTime = null;

	/**
	 * @inheritDoc
	 */
	protected $deleteTime = 'del_time';

	/**
	 * @inheritDoc
	 */
	protected $dateFormat = 'Y-m-d H:i';

	/**
	 * @inheritDoc
	 */
	protected $defaultSoftDelete = 0;

	/**
	 * 是否需要插入日志
	 * @var int
	 */
	protected $isNeedLog = 0;

	/**
	 * 业务内容
	 * @var string
	 */
	protected $doSomething;

	/**
	 * 修改条件
	 * @var array
	 */
	protected $updateWhere = [];

	/**
	 * 添加后
	 * @param Model $model
	 * @return bool|void
	 */
	public static function onAfterInsert($model)
	{
		if ($model->isNeedLog && !empty(app()->adminUser)) {
			if (empty($model->doSomething)) {  //如果没有传业务内容，则获取表注释拼接
				$model->doSomething = '新增了' . self::getTableComment($model) . '，“id“：' . $model->id;
			}
			self::addLog($model);
		}

		return true;
	}

	/**
	 * 更新前
	 * @param Model $model
	 * @return bool|mixed
	 */
	public static function onBeforeUpdate($model)
	{
		if ($model->isNeedLog && !empty(app()->adminUser)) {
			if (!self::getDoSomething($model)) {
				return false;
			}
			self::addLog($model);
		}

		return true;
	}

	/**
	 * 删除后
	 * @param Model $model
	 * @return bool|void
	 */
	public static function onAfterDelete($model)
	{
		if ($model->isNeedLog && !empty(app()->adminUser)) {
			if (empty($model->doSomething)) {  //如果没有传业务内容，则获取表注释拼接
				$model->doSomething = '删除了' . self::getTableComment($model) . '，“id“：' . $model->id;
			}
			self::addLog($model);
		}

		return true;
	}

	/**
	 * 获取修改变化内容
	 * @param $model \think\model
	 * @return bool
	 */
	private static function getDoSomething($model)
	{
		try {
			if (empty($model->getKey()) && empty($model->updateWhere)) {
				return false; //如果主键和更新条件都为空
			}
			$editData = $model->getChangedData(); //获取修改的数据
			//查询字段注释
			$tableStructure = Db::query("show full columns from {$model->getTable()}");

			$structureArr = [];
			//组合为 key 为 field value 为 Comment 的数组
			foreach ($tableStructure as $key => $value) {
				if (array_key_exists($value['Field'], $editData)) {     //只处理更新的字段注释
					//先替换中文，：（为英文,:(
					$value['Comment'] = str_replace('，', ',', $value['Comment']);
					$value['Comment'] = str_replace('：', ':', $value['Comment']);
					$value['Comment'] = str_replace('（', '(', $value['Comment']);
					//判断三个符号哪个先出现，根据最先出现的截断
					$pos['douPos'] = intval(strpos($value['Comment'], ','));
					$pos['maoPos'] = intval(strpos($value['Comment'], ':'));
					$pos['kuoPos'] = intval(strpos($value['Comment'], '('));

					//如果三个都没有找到
					if (!$pos['douPos'] && !$pos['maoPos'] && !$pos['kuoPos']) {
						$comment = $value['Comment'];
					} else {
						$min = 100;
						foreach ($pos as $pk => $pv) {
							if (0 == $pv) {
								continue;
							}
							if ($min > $pv) {
								$min = $pv;
							}
						}
						$comment = substr($value['Comment'], 0, $min);
					}
					$structureArr[$value['Field']] = empty($comment) ? $value['Field'] : $comment;
				}
			}
			if (empty($model->doSomething)) {
				$model->doSomething = '修改了' . self::getTableComment($model) . '【id：' . $model->id . '】，';
			}
			$editContent = $model->doSomething; //修改内容
			$fieldStr = ''; //需要查询验证的字段字符串
			foreach ($editData as $editKey => $editVal) {
				$fieldStr .= $editKey . ',';
			}
			//去掉最后一个,
			$fieldStr = rtrim($fieldStr, ',');
			$tableWhere = [];
			if (!empty($model->getKey())) {
				$tableWhere[] = ['id', '=', $model->getKey()];
			} else {
				$tableWhere = $model->updateWhere;
			}
			$oriData = $model::where($tableWhere)->field($fieldStr)->findOrEmpty();
			if ($oriData->isEmpty()) {
				return false;
			}

			$oriData = $oriData->toArray();

			/*
			* 循环日志判断字段数组变化
			* */
			foreach ($editData as $k => $v) {
				if ($oriData[$k] != $editData[$k]) {
					if ((null == $oriData[$k] || '' == $oriData[$k]) && 0 !== $oriData[$k]) {
						$oriData[$k] = '无';
					}
					if (is_array($oriData[$k])) {
						$oriData[$k] = json_encode($oriData[$k]);
					}
					if (is_array($editData[$k])) {
						$editData[$k] = json_encode($editData[$k]);
					}
					$editContent .= '“' . $structureArr[$k] . '”从' . $oriData[$k] . '改为了：' . $editData[$k] . '；';
				}
			}
			//如果没有数据改变，则不插入日志记录
			if ($editContent == $model->doSomething) {
				return false;
			}

			$model->doSomething = $editContent;

			return true;
		} catch (Exception $exception) {
			return false;
		}
	}

	/**
	 * 获取表注释
	 * @param $model \think\model
	 * @return string|string[]
	 */
	private static function getTableComment($model)
	{
		$dbname = env('DATABASE_DATABASE');
		$sql = "Select TABLE_COMMENT as comment  from INFORMATION_SCHEMA.TABLES where table_name='{$model->getTable()}' and table_schema='{$dbname}'";
		//查询表注释
		$comment = Db::query($sql);
		$commentStr = ''; //当前表注释
		if (!empty($comment)) {
			$commentStr = $comment[0]['comment'];
			if (false != strpos($commentStr, '记录表')) {
				//如果有记录表，去除表
				$commentStr = str_replace('表', '', $commentStr);
			} elseif (false != strpos($commentStr, '表')) {
				//如果只有表，将表改为记录
				$commentStr = str_replace('表', '记录', $commentStr);
			}
		}

		return $commentStr;
	}

	/**
	 * 添加日志
	 * @param $model \think\model
	 */
	protected static function addLog($model)
	{
		AdminLog::create(
			[
				'username' => app()->adminUser->username,
				'nickname' => app()->adminUser->nickname,
				'details' => $model->doSomething,
				'table' => $model->getTable(),
				'table_id' => $model->id,
				'add_time' => time()
			]
		);
	}
}
