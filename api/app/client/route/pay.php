<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 15:01
 */

use think\facade\Route;

/* 无需验证 */
Route::group('pay', function () {
	Route::post('session_key', 'user.user/getSession'); // 获取sessionKey
	Route::post('login', 'user.user/login'); // 登录
   	Route::get('pay', 'pay.pay/pay'); //支付 认证支付
   
    Route::post('notify', 'pay.pay/notify'); //支付回调
    Route::get('notify', 'pay.pay/notify'); //支付回调
});

Route::group('pay', function () {

	Route::post('bind_tel', 'user.user/miniBindTel'); // 小程序绑定手机
	Route::post('mini_login', 'user.user/miniLogin'); // 登录
	Route::post('setmealPay', 'pay.pay/setmealPay'); //套餐支付
	Route::post('pay', 'pay.pay/pay'); //支付
})->middleware(\app\middleware\ClientAuth::class);
