<?php
declare (strict_types=1);

namespace app\common\validate;

use think\Validate;

class ArtistValidate extends Validate
{
	/**
	 * 定义验证规则
	 * 格式：'字段名' =>  ['规则1','规则2'...]
	 *
	 * @var array
	 */
	protected $rule = [
		'id' => ['require'],
		'state|状态' => ['require', 'number', 'between' => '0,2'],
		'name|姓名' => ['require', 'max' => 20],
		'stage_name|艺名' => ['require', 'max' => 20],
		'height|身高' => ['require', 'max' => 5],
		'weight|体重' => ['require', 'max' => 5],
		'bust|胸围' => ['require', 'max' => 5],
		'waistline|腰围' => ['require', 'max' => 5],
		'hipline|臀围' => ['require', 'max' => 5],
		'shoe_size|鞋码' => ['require', 'max' => 5],
		'college|院校' => ['require', 'max' => 100],
		'job|职业' => ['require', 'max' => 30],
		'speciality|特长' => ['require', 'max' => 255],
		'wechat_id|微信号' => ['require', 'max' => 30],
		'id_card|身份证' => ['require', 'idCard', 'max' => 30],
// 		'identity_front|身份证正面' => ['require', 'max' => 255],
// 		'identity_back|身份证反面' => ['require', 'max' => 255],
		'moka|模卡' => ['require', 'max' => 255],
		'cover_img|封面图' => ['require', 'max' => 255],
		'artist_type|艺人类型' => ['require', 'max' => 255],
		'province|省' => ['require', 'max' => 30],
		'city|市' => ['require', 'max' => 30],
		'district|区' => ['require', 'max' => 30],
		'tag' => ['max' => 255],
	];

	/**
	 * 定义错误信息
	 * 格式：'字段名.规则名' =>  '错误信息'
	 *
	 * @var array
	 */
	protected $message = [];

	protected $scene = [
		'audit' => ['id', 'state'],
		'save' => ['name', 'stage_name', 'height', 'weight', 'bust', 'waistline', 'hipline', 'shoe_size', 'college', 'job', 'speciality', 'wechat_id', 'id_card', 'identity_front', 'identity_back', 'moka', 'cover_img', 'artist_type', 'province', 'city', 'district']
	];
}
