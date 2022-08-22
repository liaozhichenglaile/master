<?php
/*
 * @Author: dashing
 * @Date: 2021/1/13 18:36
 */

use think\facade\Route;

Route::group('system', function () {
	Route::post('login', 'system.admin/login');
	Route::any('captcha', 'system.admin/createCaptcha');
	Route::any('upload_img', 'system.assist/uploadImg');
});

Route::group('system', function () {
	/** 管理员 */
	Route::get('me', 'system.admin/me');
	Route::post('logout', 'system.admin/logout');

	/** 轮播图 */
	Route::get('banner/list', 'system.banner/getList'); // 列表
	Route::post('banner/save', 'system.banner/handleSave'); // 保存
	Route::post('banner/delete', 'system.banner/handleDelete'); // 删除

	/** 字典 */
	Route::get('dictionary/list', 'system.dictionary/getList'); // 列表
	Route::post('dictionary/save', 'system.dictionary/handleSave'); // 保存
	Route::post('dictionary/delete', 'system.dictionary/handleDelete'); // 删除
	Route::get('dictionary/type', 'system.dictionary/getType'); // 类型

	/** 管理员 */
	Route::get('admin/list', 'system.admin/getList'); // 列表
	Route::post('admin/save', 'system.admin/handleSave'); // 保存
	Route::post('admin/delete', 'system.admin/handleDelete'); // 删除
})->middleware(\app\middleware\AdminAuth::class);
