<?php
/*
 * @Author: dashing
 * @Date: 2021/1/29 14:48
 */

namespace app\admin\controller\system;


use app\facade\UploadTool;

class AssistController extends \app\admin\controller\BaseController
{
	public function uploadImg()
	{
		$file = $this->request->file('file');

		validate([
			'file' => [
				'fileSize' => 2 * 1024 * 1024,
				'fileExt' => 'jpg,png,jpeg'
			]])
			->check(['file' => $file]);

		return $this->successful(['url' => UploadTool::upload($file)]);
	}
}
