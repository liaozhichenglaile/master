<?php
/*
 * @Author: dahing
 * @Date: 2020-06-04 16:44:25
 * @LastEditors:
 * @LastEditTime: 2020-06-04 18:16:39
 */

namespace app\common\controller;

/**
 * 响应状态码
 */
class ResponseCode
{
	/**
	 * 成功
	 */
	const Success = 0;

	/**
	 * db处理异常
	 */
	const DbError = 1002;

	/**
	 * 请求参数错误
	 */
	const ParamError = 1002;

	/**
	 * 用户异常
	 */
	const UserWaring = 1002;

	/**
	 * 数据为空
	 */
	const DataEmpty = 1002;

	/**
	 * 无权限
	 */
	const PermissionEmpty = 1002;

	/**
	 * 认证失败
	 */
	const AuthError = 1001;

	const FailCode = 1002;
}
