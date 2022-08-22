<?php
/*
 * @Author: dashing
 * @Date: 2021/1/30 13:49
 */

use think\facade\Route;

Route::group('system', function () {
	/** 字典 */
	Route::get('dictionary/list', 'system.dictionary/getList'); // 列表

	/** 轮播图 */
	Route::get('banner/list', 'system.banner/getList'); // 列表
});

Route::group('system', function () {
})->middleware(\app\middleware\ClientAuth::class);
