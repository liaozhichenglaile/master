<?php
declare (strict_types=1);

namespace app\common\model;


class Follow extends BaseModel
{
	public function artist()
	{
		return $this->hasOne(Artist::class, 'uid', 'artist_uid');
	}
}
