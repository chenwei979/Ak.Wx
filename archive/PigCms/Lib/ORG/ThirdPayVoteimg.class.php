<?php 
class ThirdPayVoteimg{
	public function index($order_id , $paytype='' , $third_id=''){
		$wecha_id = '';
		$token = '';
		$order = M('voteimg_book')->where(array("orderid"=>$order_id))->find();
		if(!empty($order)){
			$wecha_id = $order['wecha_id'];
			$token = $order['token'];
			$vote_id = $order['vote_id'];
			if($order['paid']){
				//给顾客发模板消息
				$model = new templateNews();
				$siteurl=$_SERVER['HTTP_HOST'];
				$siteurl=strtolower($siteurl);
				if(strpos($siteurl,"http:")===false && strpos($siteurl,"https:")===false) $siteurl='http://'.$siteurl;
				$siteurl=rtrim($siteurl,'/');
                $model->sendTempMsg('OPENTM202521011', array('href' =>$siteurl."/index.php?g=Wap&m=Voteimg&a=HomePage&token={$token}&wecha_id={$wecha_id}&id={$order['vote_id']}&token={$order['token']}", 'wecha_id' => $wecha_id, 'first' => '红包拉票交易提醒', 'keyword1' => $order_id, 'keyword2' => date("Y年m月d日H时i分s秒"), 'remark' => '充值完成！'));
				//给商家发站内信
				$params = array();
				$params['site'] = array('token'=>$token, 'from'=>'红包拉票消息','content'=>"粉丝".$order['wecha_name']."刚刚对订单号：".$order_id."进行了支付，请您注意查看并处理");
				MessageFactory::method($params, 'SiteMessage');
				//修改订单状态
				M('voteimg_book')->where(array("orderid"=>$order_id))->save(array('paystatus'=>1,'pay_time'=>$_SERVER['REQUEST_TIME']));
				//修改红包状态
				M('voteimg_confighb')->where(array('hb_id'=>$order['hb_id']))->save(array('total_money'=>array('exp','total_money+'.$order['price']),'paystatus'=>1));
				header('Location:'.U('Voteimg/index', array('token' => $token, 'id' => $vote_id)));
			}else{
				header('Location:'.U('Voteimg/index', array('token' => $token, 'id' => $vote_id)));
			}
		}else{
			exit('订单不存在：'.$order_id);
		}
	}
}