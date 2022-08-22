<?php

namespace app;

use app\common\controller\ResponseCode;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use thans\jwt\exception\JWTException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\exception\PDOException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
	/**
	 * 不需要记录信息（日志）的异常类列表
	 * @var array
	 */
	protected $ignoreReport = [
		HttpException::class,
		HttpResponseException::class,
		ModelNotFoundException::class,
		DataNotFoundException::class,
		ValidateException::class,
	];

	/**
	 * 记录异常信息（包括日志或者其它方式记录）
	 *
	 * @access public
	 * @param Throwable $exception
	 * @return void
	 */
	public function report(Throwable $exception): void
	{
		// 使用内置的方式记录异常日志
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @access public
	 * @param \think\Request $request
	 * @param Throwable $e
	 * @return Response
	 */
	public function render($request, Throwable $e): Response
	{
		// 添加自定义异常处理机制

		if ($e instanceof RouteNotFoundException) {
			return json(['code' => ResponseCode::ParamError, 'message' => $e->getMessage()]);
		}

		// 验证失败
		if ($e instanceof ValidateException) {
			return json(['code' => ResponseCode::ParamError, 'message' => $e->getMessage()]);
		}

		// 身份验证失败
		if ($e instanceof JWTException) {
			return json(['code' => ResponseCode::AuthError, 'message' => $e->getMessage()]);
		}

		// 数据查询失败
		if ($e instanceof DataNotFoundException) {
			return json(['code' => ResponseCode::DataEmpty, 'message' => '未找到相关信息']);
		}

		// 数据查询失败
		if ($e instanceof ModelNotFoundException) {
			return json(['code' => ResponseCode::DataEmpty, 'message' => '未找到相关信息']);
		}

		// 数据操作失败
		if ($e instanceof PDOException) {
			// return json(['code' => ResponseCode::DbError, 'message' => '数据异常']);
		}

		// 授权失败
		if ($e instanceof DecryptException) {
			return json(['code' => ResponseCode::Success, 'message' => '授权失败,请重试', 'data' => 'authError']);
		}

		// 其他错误交给系统处理
		return parent::render($request, $e);
	}
}
