<?php
declare (strict_types=1);

namespace app\listener;

use app\services\user\ArtistServices;

class VirtualFansEvent
{
	/**
	 * 事件监听处理
	 *
	 * @return mixed
	 */
	public function handle($data)
	{
		/** @var ArtistServices $artist */
		$artist = app()->make(ArtistServices::class);
		$artist->virtualFans($data);
	}
}
