<?php
/*
 * @Author: dashing
 * @Date: 2021/1/21 11:23
 */

namespace app\client\controller\pay;


use app\common\validate\UserValidate;
use app\facade\WxServices;
use app\services\user\UserServices;
use thans\jwt\facade\JWTAuth;
use think\App;
use think\facade\Db;
use think\exception\ValidateException;
use think\facade\Cache;

class PayController extends \app\client\controller\BaseController
{
	protected $services;

	public function __construct(App $app, UserServices $services)
	{
	 
		parent::__construct($app);
		$this->services = $services;
	}

	
	
	
	//小程序支付 认证支付
	public function pay(){
	  include 'wxpay.php';

	 $res1=db::name('order')->where(['uid'=>$this->user->id,'status'=>1,'remark'=>'充值艺人'])->field('id')->find();
  	    
	  //新老玩家判断
	  if(!$res1){
	      
	   $userinfo=Db::name('artist')->where('uid',$this->user->id)->field('id')->find();
	  
	  $res=db::name('order')->where(['uid'=>$this->user->id,'status'=>2,'remark'=>'充值艺人'])->field('id')->find(); 
	 
	  if(!$userinfo || $res){

	  $openid=$this->user->openid;
	  $out_trade_no=date('YmdHis').rand(100000,999999);
	  $total_fee=10000;
	  $body = '订单付款';
	  
	  $weixinpay = new \WxPay( $openid,$out_trade_no, $body, $total_fee);

      $return = $weixinpay->pay();
      $return['code']=1000;
      //创建订单
  
      Db::name('order')->insert([
          'uid'        => $this->user->id,
          'orderid'    => $out_trade_no,
          'openid'     => $openid,
          'createtime' => time(),
          'remark'     => '充值艺人',
          'money'      => $total_fee/100
          ]);
          
        return $this->successful($return);
  	  }
  	  }
  	  
  	      
  	    
  	    $return=['code'=>1001];
  	    $this->successful($return);   
  	  
	  
	   return $this->successful($return);
	  

	}
	
	
		//小程序支付 认证支付
	public function setmealPay(){
	  
	    
	   $data=$this->request->post();
	    if(empty($data['money']) || empty($data['list']) || empty($data['index'])){
	    	throw new ValidateException('请求错误');
	    }
	     $sort_list=db::name('artist')->where('id',$this->user->id)->field('sort,sort_list')->find();
	     if($sort_list){
	       if($sort_list['sort']>0 || $sort_list['sort_list']>0){
          	throw new ValidateException('您的置顶仍在有效期');
         }   
	     }
       
	    
	    if($data['type']==1){
	      $exhibition_type='艺人列表'; 
	      
	    }else{
	      $exhibition_type='艺人榜单'; 
	   }
	    
	    if($data['list']==5){
	         $position_type='1-5';    
	    }else if($data['list']==10){
	       $position_type='6-10';
	         
	    }
	    
	    
	     if($data['index']==1){
	        $day='7';    
	    }else if($data['index']==2){
	        $day='30'; 
	    }else if($data['index']==3){
	        
	   if($data['list']==10){
	     $day=$data['money']/69.9;
	      
	    }else{
	     $day=$data['money']/129;
	    }
	        
	    
	    }
	 
	  include 'wxpay.php';
      
      

	  $openid=$this->user->openid;
	  $out_trade_no=date('YmdHis').rand(100000,999999);
	  $total_fee=str_replace('元','',$data['money'])*100;
	  $body = '订单付款';

	  $weixinpay = new \WxPay( $openid,$out_trade_no, $body, $total_fee);

      $return = $weixinpay->pay();
     
      //创建订单
  
      Db::name('uppay')->insert([
          'uid'               => $this->user->id,
          'orderid'           => $out_trade_no,
          'openid'            => $openid,
          'add_time'          => time(),
          'exhibition_type'   => $exhibition_type,
          'position_type'     => $position_type,
          'day'               => $day,
          'start_time'        => time(),
          'end_time'          => time()+86400*$day,
          'money'             => str_replace('元','',$data['money'])
          ]);
          
          
        return $this->successful($return);
  	  
  	  }
     
  	    
  	 
	
	
		//小程序支付
	public function notify(){
	    $testxml  = file_get_contents("php://input");
       
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
       
        $result = json_decode($jsonxml, true);//转成数组，
     //  trace(json_encode($result),'error');
        if($result){
            //如果成功返回了
            $out_trade_no = $result['out_trade_no'];
                if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                //执行业务逻辑
                $res=db::name('order')->where(['orderid'=>$out_trade_no,'status'=>0])->field('id')->find();
                if($res){
                   Db::name('order')->where('orderid',$out_trade_no)->update([
                    'status'=>1,
                    'transaction_id'=>$result['transaction_id'],
                    'paytime'=>time()
                    ]);
                }else{
                    $res1=db::name('uppay')->where(['orderid'=>$out_trade_no,'status'=>0])
                    ->field('id,uid,position_type,exhibition_type')
                    ->find();  
                    
                   Db::name('uppay')->where('orderid',$out_trade_no)->update([
                    'status'=>1,
                    'transaction_id'=>$result['transaction_id'],
                    'paytime'=>time()
                    ]); 
                  
                    
                 
                    
                if($res1['exhibition_type']=='艺人榜单'){
                        
                        if($res1['position_type']=='1-5'){
                            
                            for ($i = 10; $i > 5; $i--) {
                            $info=db::name('artist')->where('sort',$i)->field('id')->find(); 
                            if(!$info){
                                  //    更新艺术人的排序
                            db::name('artist')->where('id',$res1['uid'])->update(['sort'=>$i]);
                            break;
                                
                            }
                            }
                      
                            }else if($res1['position_type']=='6-10'){
                                
                            for ($i = 5; $i > 0; $i--) {
                            $info=db::name('artist')->where('sort',$i)->field('id')->find(); 
                            if(!$info){
                                  //    更新艺术人的排序
                            db::name('artist')->where('id',$res1['uid'])->update(['sort'=>$i]);
                            break;    
                            }
                            }
                        
                            }
                            //找图标
                            $map['exhibition_type']='艺人榜单';
                            $map['end_time']=array('>',time());
                            $map['status']=1;
                            
                            $count=DB::name('uppay')->where($map)->filed('logo,id,add_time')->order('add_time','desc')->select();
                            foreach ($count as $k=>$v){
                              if($k==10)  break;
                               DB::name('uppay')->where('id',$v['id'])->update(['logo'=>$k+1]); 
                            }
                } 
                
                if($res1['exhibition_type']=='艺人列表'){
                     
                     
                     if($res1['position_type']=='1-5'){
                            
                            for ($i = 10; $i > 5; $i--) {
                            $info=db::name('artist')->where('sort_list',$i)->field('id')->find(); 
                            if(!$info){
                                  //    更新艺术人的排序
                            db::name('artist')->where('id',$res1['uid'])->update(['sort_list'=>$i]);
                            break;
                                
                            }
                            }
                            
                            
                      
                            }else if($res1['position_type']=='6-10'){
                                
                            for ($i = 5; $i > 0; $i--) {
                            $info=db::name('artist')->where('sort_list',$i)->field('id')->find(); 
                            if(!$info){
                                  //    更新艺术人的排序
                            db::name('artist')->where('id',$res1['uid'])->update(['sort_list'=>$i]);
                            break;    
                            }
                            }
                        
                            }
                     
                         //找图标
                            $map1['exhibition_type']='艺人列表';
                            $map1['end_time']=array('>',time());
                            $map1['status']=1;
                            
                            $count=DB::name('uppay')->where($map1)->filed('logo,id,add_time')->order('add_time','desc')->select();
                            foreach ($count as $k=>$v){
                              if($k==10)  break;
                               DB::name('uppay')->where('id',$v['id'])->update(['logo'=>$k+1]); 
                            }
                     
                     
                 }
                 
              
                 
                 
                }
               
        }
        return '{"code": "SUCCESS","message": ""}';

	}


}


}
