<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 17:31
 */

use think\facade\Route;

Route::group('artist', function () {
	/** 艺人 */
	Route::get('list', 'artist.artist/getList'); // 列表
	Route::get('detail', 'artist.artist/getDetail'); // 详情
	Route::get('recommend', 'artist.artist/recommend'); // 首页推荐

	/** 作品 */
	Route::get('dynamic/list', 'artist.dynamic/getList'); // 列表
})->middleware(\app\middleware\AnyClientAuth::class);

Route::group('artist', function () {
	/** 艺人 */
	Route::post('save', 'artist.artist/handleSave'); // 保存

	/** 关注 */
	Route::get('follow/list', 'artist.follow/getList'); // 列表
	Route::post('follow', 'artist.follow/follow'); // 关注
	Route::post('follow/delete', 'artist.follow/handleDelete'); // 取消关注

	/** 作品 */
	Route::post('dynamic/save', 'artist.dynamic/handleSave'); // 保存

	/** 预约 */
	Route::get('appointment/list', 'artist.appointment_model/getList'); // 列表
	Route::post('appointment/save', 'artist.appointment_model/handleSave'); // 保存
})->middleware(\app\middleware\ClientAuth::class);
