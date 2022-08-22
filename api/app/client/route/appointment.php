<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 17:51
 */

use think\facade\Route;

Route::group('appointment', function () {
	Route::get('list', 'appointment.appointment/getList'); // 列表
	Route::post('save', 'appointment.appointment/handleSave'); // 保存
})->middleware(\app\middleware\ClientAuth::class);
