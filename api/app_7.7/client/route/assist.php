<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 17:51
 */

use think\facade\Route;

Route::group('assist', function () {
	Route::post('upload_img', 'assist.assist/uploadImg'); // 上传图片
});
