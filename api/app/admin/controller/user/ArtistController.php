<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 17:21
 */

namespace app\admin\controller\user;

use think\facade\Db;
use app\common\validate\ArtistValidate;
use app\services\user\ArtisthouServices;
use think\App;

class ArtistController extends \app\admin\controller\BaseController
{
	/**
	 * @var ArtistServices
	 */
	protected $services;

	public function __construct(App $app, ArtisthouServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['nickname'],
			['name'],
			['tel'],
			['add_time'],
			['state'],
			['artist_type'],
			['sex'],
			['province'],
			['city'],
			['district'],
			['sort'],
			['page', 0],
			['limit', 0]
		]);

		$with = ['user' => function ($query) {
			$query->field(['id', 'head_img', 'nickname', 'tel', 'openid', 'add_time'])->bind(['head_img', 'nickname', 'tel', 'openid', 'reg_time' => 'add_time']);
		}];

		$withCount = ['fans'];
	
		return $this->successful($this->services->getList($data, $with, $withCount));
	}
	

       
	
	

	public function handleAudit()
	{
		$data = $this->request->postMore([
			['id'],
			['state'],
			['remark']
		]);

		$this->validate($data, ArtistValidate::class . '.audit');

		$data['audit_time'] = $this->now;
	
		//退款操作
		if($data['state']==2){
	    $uid= db::name('artist')->where('id',$data['id'])->value('uid');
	  
        $res=db::name('order')->where(['uid'=>$uid,'status'=>1])->field('money,orderid,transaction_id,id')->find();
      
        if($res){
         
              //更新状态
        include '1.php';
        $orderNo =  $res['orderid'];           //商户订单号（商户订单号与微信订单号⼆选⼀，⾄少填⼀个）
        $wxOrderNo = $res['transaction_id'];           //微信订单号（商户订单号与微信订单号⼆选⼀，⾄少填⼀个）
        $totalFee =$res['money'];          //订单⾦额，单位:元
        $refundFee =$res['money'];         //退款⾦额，单位:元
        $refundNo = 'refund_'.uniqid();    //退款订单号(可随机⽣成)
        $wxPay = new \WxpayService();
        $result = $wxPay->doRefund($totalFee, $refundFee, $refundNo, $wxOrderNo,$orderNo);
        if($result===true){
          db::name('order')->where(['id'=>$res['id']])->update(['status'=>2]);
          db::name('artist')->where(['uid'=>$uid])->update(['status'=>2]);
          
        
        }
     
        }
		}
       return $this->successful($this->services->handleSave($data));  
	
	}

	public function getDetail()
	{
		$data = $this->request->getMore([
			'id'
		]);

		$with = ['user' => function ($query) {
			$query->field(['id', 'head_img', 'nickname', 'tel', 'openid', 'add_time'])->bind(['head_img', 'nickname', 'tel', 'openid', 'reg_time' => 'add_time']);
		}];

		return $this->successful($this->services->getDetail($data, $with));
	}

	public function top()
	{
		$data = $this->request->postMore([
			'id',
			[['top_time', 'd'], 0]
		]);

		$data['top_time'] = $data['top_time'] === 0 ? $data['top_time'] : $this->now;

		return $this->successful($this->services->handleSave($data));
	}

	public function handleSave()
	{
		$data = $this->request->postMore([
			'id',
			[['virtual_fans', 'd'], 0]
		]);

		return $this->successful($this->services->handleSave($data));
	}

	public function handleSort()
	{
		$data = $this->request->postMore([
			'id',
			[['sort', 'd'], 0]
		]);

		return $this->successful($this->services->handleSave($data));
	}
}
