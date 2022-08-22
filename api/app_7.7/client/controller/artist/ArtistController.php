<?php
/*
 * @Author: dashing
 * @Date: 2021/1/20 16:36
 */

namespace app\client\controller\artist;


use app\client\controller\BaseController;
use app\common\validate\ArtistValidate;
use app\services\user\ArtistServices;
use think\App;

class ArtistController extends BaseController
{
	protected $services;

	public function __construct(App $app, ArtistServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['audit_time_sort'],
			['fans_num_sort'],
			['dynamic_num_sort'],
			['artist_type'],
			['height'],
			['job'],
			['sex'],
			['province'],
			['city'],
			['district'],
			['name'],
			['state', 1],
			['page', 0],
			['limit', 0]
		]);

		$with = [];

		if ($this->user) {
			$with = ['follow' => function ($query) {
				$query->field(['id', 'uid', 'artist_uid'])->where('uid', $this->user->id);
			}];
		}

		$withCount = ['fans'];

		$field = 'id,uid,name,stage_name,province,city,district,artist_type,height,weight,bust,add_time,cover_img,virtual_fans';

		if ($data['dynamic_num_sort']) {
			$field = str_replace('uid', env('database.prefix') . 'artist.uid', $field);
		}

		return $this->successful($this->services->getList($data, $with, $withCount, $field));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			['id'],
			['name'],
			['stage_name'],
			['height'],
			['weight'],
			['bust'],
			['waistline'],
			['hipline'],
			['shoe_size'],
			['college'],
			['job'],
			['speciality'],
			['wechat_id'],
			['id_card'],
// 			['identity_front'],
// 			['identity_back'],
			['moka'],
			['cover_img'],
			['artist_type'],
			['state', 0],
			['province'],
			['city'],
			['district'],
			['tag'],
		]);

		$data['uid'] = $this->user->id;

		$this->validate($data, ArtistValidate::class . '.save');

		$data['sex'] = get_sex($data['id_card']);
		$data['age'] = get_age($data['id_card']);
		$data['constellation'] = get_constellation($data['id_card']);

		return $this->successful($this->services->handleSave($data));
	}

	public function getDetail()
	{
		$data = $this->request->getMore([
			['id'],
			['is_me'],
			['uid'],
			['view_uid']
		]);

		$viewUid = $data['view_uid'];
		unset($data['view_uid']);

		$with = [];
		$withCount = [];
		if ($data['is_me']) {
			$data['uid'] = $this->user->id;
			unset($data['is_me'], $data['id']);
		} else {
			$with = ['user' => function ($query) {
				$query->field(['id', 'head_img'])->bind(['head_img']);
			}];

			if ($this->user) {
				$with['follow'] = function ($query) {
					$query->field(['id', 'uid', 'artist_uid'])->where('uid', $this->user->id);
				};
			}

			$withCount = ['fans'];
		}

		$ret = $this->services->getDetail($data, $with, $withCount);
		if ($ret->uid && $viewUid && $ret->uid != $viewUid) {
			event('VirtualFansEvent', ['uid' => $ret->uid, 'virtual_fans' => 7]);
		}
		return $this->successful($ret);
	}

	/**
	 * 首页推荐
	 * @return \think\Response
	 */
	public function recommend()
	{
		$data = $this->request->postMore([
			['state', 1],
			['page', 0],
			['limit', 0]
		]);

		$with = ['dynamic' => function ($query) {
			$query->field(['uid', 'img'])->where(['state' => 1])->order('id', 'desc')->limit(3);
		}, 'user' => function ($query) {
			$query->field(['id', 'head_img'])->bind(['head_img']);
		}
		];

		$withCount = ['fans'];

		$field = 'id,uid,name,stage_name,city,artist_type,tag,virtual_fans,moka';

		$data['is_top'] = 1;

		return $this->successful($this->services->getList($data, $with, $withCount, $field));
	}
}
