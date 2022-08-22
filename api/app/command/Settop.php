<?php
declare (strict_types=1);

namespace app\command;
use think\facade\Db;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Settop extends Command
{
	protected function configure()
	{
		// 指令配置
		$this->setName('Settop')
			->setDescription('去除置顶');
	}

	protected function execute(Input $input, Output $output)
	{

		$output->writeln('----任务开始----' . date('Y-m-d H:i:s'));
		$output->writeln('-----------------------------------------------------------------------');
		/** @var OrderServices $order */
	
		// 指令输出
	
	    $data=DB::name('uppay')
	    ->where('status',1)
	    ->where('logo','not null')
	    ->where('end_time','<',time())
	    ->field('id,uid,exhibition_type')
	    ->select()->toarray();
	    foreach ($data as $k=>$v){
	        //更新uppay表
	        Db::name('uppay')->where('id',$v['id'])->update(['logo'=>null]);
	        //更新艺人表
	        if($v['exhibition_type']=='艺人榜单'){
	         Db::name('artist')->where('uid',$v['uid'])->update(['sort'=>0]);      
	        }else{
	         Db::name('artist')->where('uid',$v['uid'])->update(['sort_list'=>0]);        
	        }
	    	$output->writeln('会员UID：'.$v['uid'].'--'.$v['exhibition_type'].'置顶已过期');     
	    }
	  
		$output->writeln('-----------------------------------------------------------------------');
		$output->writeln('----任务结束----' . date('Y-m-d H:i:s'));
	}
}
