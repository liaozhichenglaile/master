<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 15:01
 */

use think\facade\Route;

/* 无需验证 */
Route::group('user', function () {
	Route::post('session_key', 'user.user/getSession'); // 获取sessionKey
	Route::post('login', 'user.user/login'); // 登录
});

Route::group('user', function () {
	Route::get('me', 'user.user/me'); // 个人信息
	Route::post('bind_tel', 'user.user/miniBindTel'); // 小程序绑定手机
	Route::post('mini_login', 'user.user/miniLogin'); // 登录
})->middleware(\app\middleware\ClientAuth::class);
