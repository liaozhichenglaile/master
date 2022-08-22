<?php
/*
 * @Author: dahing
 * @Date: 2020-06-04 17:02:30
 * @LastEditors:
 * @LastEditTime: 2020-06-27 11:22:53
 */

declare(strict_types=1);

namespace app\middleware;

use app\common\controller\ResponseCode;
use app\common\model\Admin;
use thans\jwt\exception\JWTException;
use thans\jwt\exception\TokenExpiredException;
use thans\jwt\middleware\JWTAuth;

/**
 * 后台验证
 */
class AdminAuth extends JWTAuth
{
	public function handle($request, \Closure $next)
	{
		try {
			$auth = $this->auth;

			if ($request->get('token')) {
				$request->withHeader(array_merge($request->header(), ['Authorization' => $request->get('token')]));
			}

			$auth = $auth->auth();
		} catch (TokenExpiredException $e) {
			$auth->setRefresh();

			$auth = $auth->getPayload();

			$response = 1;
		}

		if (!isset($auth['admin_uid'])) {
			throw new JWTException('验证失败');
		}

		$admin = Admin::GetInfo($auth['admin_uid']->getValue('uid'));

		if (!$admin->state) {
			return json(['code' => ResponseCode::AuthError, 'message' => '账号已被冻结']);
		}

		app()->bind('adminUser', $admin);

		if (isset($response)) {
			return $this->setAuthentication($next($request));
		}

		return $next($request);
	}
}
