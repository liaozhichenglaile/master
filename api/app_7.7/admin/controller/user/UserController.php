<?php
/*
 * @Author: dashing
 * @Date: 2021/1/18 16:06
 */

namespace app\admin\controller\user;


use app\common\validate\UserValidate;
use app\services\user\UserServices;
use think\App;
use think\facade\Db;;
class UserController extends \app\admin\controller\BaseController
{
	/**
	 * @var UserServices
	 */
	protected $services;

	public function __construct(App $app, UserServices $services)
	{
		parent::__construct($app);
		$this->services = $services;
	}

	public function getList()
	{
		$data = $this->request->getMore([
			['name'],
			['add_time'],
			['tel'],
			['identity'],
			['page', 0],
			['limit', 0]
		]);

		return $this->successful($this->services->getList($data));
	}
	
		public function getorderlist()
	{
	   $data=$this->request->get();
	   if(!empty($data['orderid'])){
	     $map['orderid'] =$data['orderid']; 
	   }
	    if(!empty($data['uid'])){
	     $map['uid'] =$data['uid']; 
	   }
	    if(!empty($data['transaction_id'])){
	     $map['transaction_id'] =$data['transaction_id']; 
	   }
	      if(!empty($data['status'])){
	     $map['status'] =$data['status']; 
	   }else{
	   //  $map['status'] =['in','1,2'];   
	   }
	
	   $data=DB::name('order')->where($map ?? '')
	   ->where('status = 1 or status = 2')
	   ->limit($data['limit']*($data['page']-1),$data['limit'])
	   ->order('id','desc')
	   ->select()->toArray();
	   foreach ($data as $k=>$v){
	      if($v['paytime']){
	        $data[$k]['paytime'] =date('Y-m-d H:i:s',$v['paytime']);      
	       }
	   
	     $data[$k]['createtime'] =date('Y-m-d H:i:s',$v['createtime']); 
	   }
	   $list['list']=$data;
	   $list['count']=DB::name('order')->where($map?? '')->where('status = 1 or status = 2')->count();
      
		return $this->successful($list);
	}
	
	
     public function getuppaylist()
        {
            $data=$this->request->get();
            if(!empty($data['orderid'])){
            $map['orderid'] =$data['orderid']; 
            }
            if(!empty($data['uid'])){
            $map['uid'] =$data['uid']; 
            }
            if(!empty($data['transaction_id'])){
            $map['transaction_id'] =$data['transaction_id']; 
            }
            
            if(!empty($data['exhibition_type'])){
            $map['exhibition_type'] =$data['exhibition_type']; 
            }
         
            $data=DB::name('uppay')
            ->where($map?? '')
            ->order('id','desc')
            ->where('status = 1 or status = 2')
            ->limit($data['limit']*($data['page']-1),$data['limit'])
            ->select()->toArray();
            foreach ($data as $k=>$v){
            if($v['pay_time']){
            $data[$k]['pay_time'] =date('Y-m-d H:i:s',$v['pay_time']);      
            }
            
            
            
            $data[$k]['start_time'] =date('Y-m-d H:i',$v['start_time']); 
            $data[$k]['end_time'] =date('Y-m-d H:i',$v['end_time']); 
            
            $data[$k]['add_time'] =date('Y-m-d H:i:s',$v['add_time']); 
            }
            $list['list']=$data;
            $list['count']=DB::name('uppay')->where('status = 1 or status = 2')->where($map?? '')->count();
            
            return $this->successful($list);
            }
}
