<?php

class ThirdPayDonation
{
	public function index($orderid, $paytype = '', $third_id = '')
	{
		if ($order = M('Donation_order')->where(array('orderid' => $orderid))->find()) {
			if ($order['paid']) {
				$siteurl = $_SERVER['HTTP_HOST'];
				$siteurl = strtolower($siteurl);
				if ((strpos($siteurl, 'http:') === false) && (strpos($siteurl, 'https:') === false)) {
					$siteurl = 'http://' . $siteurl;
				}

				$siteurl = rtrim($siteurl, '/');
				$set = D('Donation_set')->where(array('token' => $order['token'], 'did' => $order['did']))->find();

				if ($order['sid']) {
					if ($share = D('Donation_share')->where(array('id' => $order['sid']))->find()) {
						$money = (isset($set['money']) ? $set['money'] : 1);
						$money = ($money ? $money : 1);
						$num = floor($order['price'] / $money);

						if (D('Donation_value')->where(array('token' => $order['token'], 'did' => $order['did'], 'wecha_id' => $share['wecha_id']))->find()) {
							D('Donation_value')->where(array('token' => $order['token'], 'did' => $order['did'], 'wecha_id' => $share['wecha_id']))->setInc('num', $num);
						}
						else {
							D('Donation_value')->add(array('token' => $order['token'], 'did' => $order['did'], 'wecha_id' => $share['wecha_id'], 'num' => $num));
						}
					}
				}

				D('Donation')->where(array('id' => $order['did']))->save(array('is_delete' => 1));
				$messages = $params = array();
				$messages[] = 'TemplateMessage';
				$params['template'] = array();
				$params['template']['template_id'] = 'OPENTM207528726';
				$params['template']['template_data']['href'] = $siteurl . '/index.php?g=Wap&m=Donation&a=pay_success&token=' . $order['token'] . '&wecha_id=' . $order['wecha_id'] . '&orderid=' . $order['orderid'] . '&id=' . $order['id'];
				$params['template']['template_data']['wecha_id'] = $order['wecha_id'];
				$params['template']['template_data']['first'] = $set['tip'];
				$params['template']['template_data']['keyword1'] = $order['orderid'];
				$params['template']['template_data']['keyword2'] = date('Y年m月d日 H:i');
				$params['template']['template_data']['keyword3'] = $order['price'];
				$params['template']['template_data']['remark'] = '点击查看详情';
				MessageFactory::method($params, $messages);
			}

			header('Location:/index.php?g=Wap&m=Donation&a=pay_success&token=' . $order['token'] . '&wecha_id=' . $order['wecha_id'] . '&id=' . $order['did']);
		}
		else {
			exit('订单不存在：' . $out_trade_no);
			exit('订单不存在');
		}
	}
}

echo "\r\n";

?>
