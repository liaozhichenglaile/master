<?php
/*
 * @Author: dashing
 * @Date: 2021/1/13 18:36
 */

use think\facade\Route;

Route::group('user', function () {
    
});

Route::group('user', function () {
	/** 用户 */
	Route::get('list', 'user.user/getList'); // 列表

	
	Route::get('getorderlist', 'user.user/getorderlist'); // 列表
	Route::get('getuppaylist', 'user.user/getuppaylist'); // 列表

	/** 艺人 */
	Route::get('artist/list', 'user.artist/getList'); // 列表
	Route::get('artist/detail', 'user.artist/getDetail'); // 详情
	Route::post('artist/audit', 'user.artist/handleAudit'); // 审核
	Route::post('artist/top', 'user.artist/top'); // 置顶
	Route::post('artist/save', 'user.artist/handleSave'); // 保存
	Route::post('artist/sort', 'user.artist/handleSort'); // 排序

	/** 商家预约 */
	Route::get('appointment/list', 'user.appointment/getList'); // 列表
	Route::post('appointment/audit', 'user.appointment/handleAudit'); // 审核

	/** 预约 */
	Route::get('appointment_model/list', 'user.appointment_model/getList'); // 列表
	Route::post('appointment_model/audit', 'user.appointment_model/handleAudit'); // 审核

	/** 作品 */
	Route::get('dynamic/list', 'user.dynamic/getList'); // 列表
	Route::post('dynamic/audit', 'user.dynamic/handleAudit'); // 审核
	Route::post('dynamic/top', 'user.dynamic/top'); // 置顶
	
	
	
})->middleware(\app\middleware\AdminAuth::class);
