<?php
/*
 * @Author: dashing
 * @Date: 2020-10-17 16:50:15
 */

namespace app\common\controller;

use app\common\traits\ErrorTrait;
use think\facade\Config;
use think\facade\Queue as QueueThink;

/**
 * Class Queue
 * @package app\common\controller
 * @method $this do(string $do) 设置任务执行方法
 * @method $this job(string $job) 设置任务执行类名
 * @method $this errorCount(int $errorCount) 执行失败次数
 * @method $this data(...$data) 执行数据
 * @method $this secs(int $secs) 延迟执行秒数
 * @method $this log($log) 记录日志
 */
class Queue
{

	use ErrorTrait;

	/**
	 * 任务执行
	 * @var string
	 */
	protected $do = 'doJob';

	/**
	 * 默认任务执行方法名
	 * @var string
	 */
	protected $defaultDo;

	/**
	 * 任务类名
	 * @var string
	 */
	protected $job;

	/**
	 * 错误次数
	 * @var int
	 */
	protected $errorCount = 3;

	/**
	 * 数据
	 * @var array|string
	 */
	protected $data;

	/**
	 * 任务名
	 * @var null
	 */
	protected $queueName = null;

	/**
	 * 延迟执行秒数
	 * @var int
	 */
	protected $secs = 0;

	/**
	 * 记录日志
	 * @var string|callable|array
	 */
	protected $log;

	/**
	 * @var array
	 */
	protected $rules = ['do', 'data', 'errorCount', 'job', 'secs', 'log'];

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * Queue constructor.
	 */
	protected function __construct()
	{
		$this->defaultDo = $this->do;
	}

	/**
	 * @return static
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * 放入消息队列
	 * @param array|null $data
	 * @return mixed
	 */
	public function push(?array $data = null)
	{
		if (!$this->job) {
			return $this->setError('需要执行的队列类必须存在');
		}
		$res = QueueThink::{$this->action()}(...$this->getValues($data));
		$this->clean();
		return $res;
	}

	/**
	 * 清除数据
	 */
	public function clean()
	{
		$this->secs = 0;
		$this->data = [];
		$this->log = null;
		$this->queueName = null;
		$this->errorCount = 3;
		$this->do = $this->defaultDo;
	}

	/**
	 * 获取任务方式
	 * @return string
	 */
	protected function action()
	{
		return $this->secs ? 'later' : 'push';
	}

	/**
	 * 获取参数
	 * @param $data
	 * @return array
	 */
	protected function getValues($data)
	{
		$jobData['data'] = $data ?: $this->data;
		$jobData['do'] = $this->do;
		$jobData['errorCount'] = $this->errorCount;
		$jobData['log'] = $this->log;
		if ($this->do != $this->defaultDo) {
			$this->job .= '@' . Config::get('queue.prefix', 'ahb_') . $this->do;
		}
		if ($this->secs) {
			return [$this->secs, $this->job, $jobData, $this->queueName];
		} else {
			return [$this->job, $jobData, $this->queueName];
		}
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return $this
	 */
	public function __call($name, $arguments)
	{
		if (in_array($name, $this->rules)) {
			if ($name === 'data') {
				$this->{$name} = $arguments;
			} else {
				$this->{$name} = $arguments[0] ?? null;
			}
			return $this;
		} else {
			throw new \RuntimeException('Method does not exist' . __CLASS__ . '->' . $name . '()');
		}
	}
}
