<?php
/*
 * @Author: dahing
 * @Date: 2020-06-11 09:30:40
 * @LastEditors:
 * @LastEditTime: 2020-06-12 10:53:49
 */

declare(strict_types=1);

namespace app\middleware;

use app\common\controller\ResponseCode;
use app\common\model\User;
use thans\jwt\exception\JWTException;
use thans\jwt\exception\TokenExpiredException;
use thans\jwt\middleware\JWTAuth;

/**
 * 客户端验证
 */
class ClientAuth extends JWTAuth
{
	public function handle($request, \Closure $next)
	{
		try {
			$auth = $this->auth;

			$auth = $auth->auth();
		} catch (TokenExpiredException $e) {
			$auth->setRefresh();

			$auth = $auth->getPayload();

			$response = 1;
		}

		if (!isset($auth['client_uid'])) {
			throw new JWTException('验证失败');
		}

		$client = User::GetInfo($auth['client_uid']->getValue('uid'));

		if (!$client->state) {
			//return json(['code' => ResponseCode::AuthError, 'message' => '账号已被冻结']);
		}

		app()->bind('clientUser', $client);

		if (isset($response)) {
			return $this->setAuthentication($next($request));
		}

		return $next($request);
	}
}
