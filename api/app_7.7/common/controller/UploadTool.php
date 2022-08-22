<?php
/*
 * @Author: dashing
 * @Date: 2020/7/7 15:16
 */

namespace app\common\controller;

use Qiniu\Auth as QiniuAuth;
use Qiniu\Storage\UploadManager;
use think\App;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Filesystem;
use think\file\UploadedFile;

class UploadTool extends BaseController
{
	/**
	 * UploadTool constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		parent::__construct($app);
	}

	/**
	 * 上传图片
	 * @param $file null|array|UploadedFile
	 * @param $type
	 * @return string
	 * @throws \Exception
	 */
	public function upload($file, $type = 'qiniu')
	{
		if ($type == 'local') {
			if ($saveFileName = Filesystem::disk('public')->putFile('upload', $file)) {
				return $saveFileName;
			}
		}

		if ($type == 'qiniu') {
			// 七牛配置
			$config = Config::get('extend.upload.qiniu');
			// 七牛验证类
			$auth = new QiniuAuth($config['accessKey'], $config['secretKey']);
			// 空间名
			$bucket = $config['bucket'];
			// 七牛域名
			$doamin = $config['base_url'];
			// 生成上传Token
			$token = $auth->uploadToken($bucket);
			// 七牛上传方法
			$uploadMgr = new UploadManager();
			// 文件后缀
			$extension = $file->extension();
			// 调用 UploadManager 的 putFile 方法进行文件的上传。
			[$ret, $err] = $uploadMgr->putFile($token, date('Ym') . '/' . uniqid() . '.' . $extension, $file->getPathname());
			// 成功时返回路径
			if (!$ret) {
				// 文件最终路径
				throw new ValidateException('上传失败');
			}
			return $doamin . '/' . $ret['key'];
		}
	}

	/**
	 * base64
	 * @param $imgStr
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function uploadBase64Qiniu($imgStr)
	{
		// 七牛配置
		$config = Config::get('extend.upload.qiniu');
		// 七牛验证类
		$auth = new QiniuAuth($config['accessKey'], $config['secretKey']);
		// 空间名
		$bucket = $config['bucket'];
		// 七牛域名
		$doamin = $config['base_url'];
		// 生成上传Token
		$token = $auth->uploadToken($bucket);
		// 七牛上传方法
		$uploadMgr = new UploadManager();

		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		[$ret, $err] = $uploadMgr->putFile($token, date('YmdHis') . '/' . uniqid() . '.jpg', $imgStr);

		// 成功时返回路径
		if (!$ret) {
			return $err;
		}

		// 文件最终路径
		return $doamin . '/' . $ret['key'];
	}

	/**
	 * 文件流上传
	 * @param $imgStr
	 * @param string $fileName
	 * @return mixed|string
	 */
	public function uploadDocumentFlow($imgStr, $fileName = '')
	{
		$fileName = $fileName ? $fileName : date('Ymd') . '/' . uniqid() . '.jpg';

		// 七牛配置
		$config = Config::get('extend.upload.qiniu');
		// 七牛验证类
		$auth = new QiniuAuth($config['accessKey'], $config['secretKey']);
		// 空间名
		$bucket = $config['bucket'];
		// 七牛域名
		$doamin = $config['base_url'];
		// 生成上传Token
		$token = $auth->uploadToken($bucket);
		// 七牛上传方法
		$uploadMgr = new UploadManager();

		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		[$ret, $err] = $uploadMgr->put($token, $fileName, $imgStr);

		// 成功时返回路径
		if (!$ret) {
			throw  new ValidateException($err);
		}

		// 文件最终路径
		return $doamin . '/' . $ret['key'];
	}
}
