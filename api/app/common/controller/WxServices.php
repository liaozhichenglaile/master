<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 13:37
 */

namespace app\common\controller;


use EasyWeChat\Factory;
use think\exception\ValidateException;

class WxServices
{
	/**
	 * @var array|mixed
	 */
	public $config = [];

	/**
	 * 小程序
	 * @var \EasyWeChat\MiniProgram\Application
	 */
	public $miniApp;

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->config = config('wechat');
		$this->miniApp = Factory::miniProgram($this->config['mini_program']['default']);
	}

	/**
	 * 上传图片
	 * @param $path
	 * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function uploadImage($path)
	{
		$ret = $this->miniApp->media->uploadImage($path);

		if (!data_get($ret, 'media_id')) {
			throw new ValidateException(data_get($ret, 'errmsg'));
		}

		return $ret;
	}

	/**
	 * 获取用户 session 信息
	 * @param $code
	 * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 */
	public function getSession($code)
	{
		return $this->miniApp->auth->session($code);
	}

	/**
	 * 解密
	 * @param $sessionKey
	 * @param $iv
	 * @param $encryptedData
	 * @return array
	 * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
	 */
	public function decryptData($sessionKey, $iv, $encryptedData)
	{
		return $this->miniApp->encryptor->decryptData($sessionKey, $iv, $encryptedData);
	}

	/**
	 * 小程序参数二维码
	 * @param $scene
	 * @param $path
	 * @param string $fileName
	 * @return bool|int|string
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
	 * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
	 */
	public function miniQrCode($scene, $path, $fileName = '')
	{
		$response = $this->miniApp->app_code->getUnlimit($scene, [
			'page' => $path,
			'width' => 600,
		]);

		$url = false;

		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			/** @var UploadTool $uploadTool */
			$uploadTool = app()->make(UploadTool::class);
			$url = $uploadTool->uploadDocumentFlow($response->getBodyContents(), $fileName);
		}

		return $url;
	}
}
