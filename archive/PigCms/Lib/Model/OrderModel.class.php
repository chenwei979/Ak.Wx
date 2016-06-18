<?php
class OrderModel extends Model {



	public function weixinRefund($token, $orderid, $from, $refund_fee = false) {
		$is_sub = $this->where(array('token'=>$token, 'orderid'=>$orderid, 'module'=>$from))->getField('is_sub');
		if ($is_sub) {
			return array('status'=>1);
		}
		$payHandel=new payHandle($token, $from, 'weixin');
		$cert = M('wxcert')->where(array('token'=>$token))->find();
		$orderInfo=$payHandel->beforePay($orderid);
		$price = (int) ($orderInfo['price']*100);
		if (!$refund_fee) {
			$refund_fee = $price;
		}
		if(empty($_GET['platform']) && empty($_GET['pl'])){
			//读取微信支付配置
			$payConfig = M('Alipay_config')->where(array('token'=>$token))->find();
			$payConfigInfo = unserialize($payConfig['info']);
			$payConfig = array_map('trim', $payConfigInfo['weixin']);
		} else {
			$payConfigInfo['new_appid'] = C('appid');//C('platform_weixin_appid');
			$payConfigInfo['appsecret'] = C('secret');//C('platform_weixin_appsecret');
			$payConfigInfo['mchid'] = C('platform_weixin_mchid');
			$payConfigInfo['key'] = C('platform_weixin_key');
			$payConfig = $payConfigInfo;
		}
		import('@.ORG.Weixinnewpay.WxPayPubHelper');
		$weixinPay = M('WeixinPay')->where(array('system'=>1))->find();
		$wxuser = M('Wxuser')->where(array('token'=>$token))->find();
		if ($weixinPay['status'] && $wxuser['merchant_id'] && $is_sub) {
			$payConfig['new_appid'] = $weixinPay['appid'];
			$payConfig['appsecret'] = $weixinPay['appsecret'];
			$payConfig['mchid'] = $weixinPay['merchant_id'];
			$payConfig['key'] = $weixinPay['key'];
		}
		$refund = new Refund_pub($payConfig['new_appid'], $payConfig['mchid'], $payConfig['key'], $payConfig['appsecret']);
		$refund->apiclient_cert = getcwd().strstr($cert['apiclient_cert'], '/uploads');
		$refund->apiclient_key = getcwd().strstr($cert['apiclient_key'], '/uploads');
		if ($weixinPay['status'] && $wxuser['merchant_id'] && $is_sub) {
			$refund->setParameter("sub_appid", $wxuser['appid']);
			$refund->setParameter("sub_mch_id", $wxuser['merchant_id']);
		}
		$refund_id = date('YmdHis').mt_rand(100000, 999999);
		$refund->setParameter("out_trade_no", $orderid);//商户订单号
		$refund->setParameter("out_refund_no", $refund_id);//商户订单号
		$refund->setParameter('transaction_id', $orderInfo['transactionid']);
		$refund->setParameter('total_fee', $price);
		$refund->setParameter('refund_fee', $refund_fee);
		$refund->setParameter('op_user_id', $token);
		$result = $refund->getResult();
		if ('SUCCESS' == $result['result_code']) {
			$from = strtolower($from);
			$data = $this->where(array('token'=>$token, 'orderid'=>$orderid, 'module'=>$from))->find();
			if ($data) {
				$data['refund_id'] = $result['refund_id'];
				$data['refund_no'] = $result['out_refund_no'];
				$data['refund_price'] = ($data['refund_price'] + ($result['refund_fee']/100));
				$data['refund_status'] = ($data['refund_status'] + 1);
				$this->where(array('token'=>$token, 'orderid'=>$orderid, 'module'=>$from))->save($data);
			}
		} else {
			$result['status'] = 1;
		}
		return $result;
	}
	
	public function addOrder($token, $orderid, $module, $is_sub = 0) {
		$module = strtolower($module);
		$data = $this->where(array('token'=>$token, 'orderid'=>$orderid, 'module'=>$module))->find();
		if (empty($data)) {
			$this->add(array('token'=>$token, 'orderid'=>$orderid, 'module'=>$module, 'is_sub'=>$is_sub));
		}
	}
}