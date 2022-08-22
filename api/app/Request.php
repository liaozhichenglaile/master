<?php

namespace app;

// 应用请求对象类
class Request extends \think\Request
{
	protected $filter = ['trim'];

	/**
	 * 获取请求的数据
	 * @param array $params
	 * @param bool $suffix
	 * @return array
	 */
	public function more(array $params, bool $suffix = false): array
	{
		$p = [];
		$i = 0;
		foreach ($params as $param) {
			if (!is_array($param)) {
				$p[$suffix == true ? $i++ : $param] = filter_emoji($this->param($param));
			} else {
				if (!isset($param[1])) $param[1] = null;
				if (!isset($param[2])) $param[2] = '';
				if (is_array($param[0])) {
					$name = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
					$keyName = $param[0][0];
				} else {
					$name = is_array($param[1]) ? $param[0] . '/a' : $param[0];
					$keyName = $param[0];
				}

				$p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $keyName)] = filter_emoji($this->param($name, $param[1], $param[2]));
			}
		}
		return $p;
	}

	/**
	 * 获取get参数
	 * @param array $params
	 * @param bool $suffix
	 * @return array
	 */
	public function getMore(array $params, bool $suffix = false): array
	{
		return $this->more($params, $suffix);
	}

	/**
	 * 获取post参数
	 * @param array $params
	 * @param bool $suffix
	 * @return array
	 */
	public function postMore(array $params, bool $suffix = false): array
	{
		return $this->more($params, $suffix);
	}
}
