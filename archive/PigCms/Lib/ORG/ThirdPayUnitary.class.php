<?php

class ThirdPayUnitary
{
	public function index($orderid, $paytype, $third_id)
	{
		$this->m_unitary = M('unitary');
		$this->m_cart = M('unitary_cart');
		$this->m_order = M('unitary_order');
		$this->m_lucknum = M('unitary_lucknum');
		$this->m_user = M('unitary_user');
		$this->m_userinfo = M('userinfo');
		$where_order['orderid'] = $orderid;
		$order = $this->m_order->where($where_order)->find();
		$this->wecha_id = $order['wecha_id'];
		$this->token = $order['token'];

		if (!$order) {
			$where_cart['order_id'] = $order['pigcms_id'];
			$save_cart['state'] = 0;
			$save_cart['order_id'] = 0;
			$update_cart = $this->m_cart->where($where_cart)->save($save_cart);
			$del_order = $this->m_order->where($where_order)->delete();
			exit('订单不存在：' . $order['pigcms_id']);
		}
		else {
			$myorder = M('unitary_order')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'pigcms_id' => $orderid))->find();

			if ($myorder['addtime'] != 0) {
				ini_set('memory_limit', '-1');
				$cart_list = $this->m_cart->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'state' => 1, 'order_id' => $orderid))->order('id')->select();

				if ($cart_list == '') {
					M('unitary_order')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'pigcms_id' => $orderid))->delete();

					if ($_GET['unitaryid']) {
						$this->redirect('Wap/Unitary/goodswhere', array('token' => $this->token, 'unitaryid' => $_GET['unitaryid']));
					}
					else {
						$this->redirect('Wap/Unitary/index', array('token' => $this->token));
					}
				}

				foreach ($cart_list as $key => $val) {
					$unitary = M('unitary')->where(array('token' => $this->token, 'id' => $val['unitary_id']))->find();
					$lucknum_array = S('LUCKNUM_ARRAY_' . $this->token . '_' . $val['unitary_id']);
					if (($lucknum_array == NULL) || (count($lucknum_array) != M('unitary_lucknum')->where(array('token' => $this->token, 'unitary_id' => $val['unitary_id']))->count())) {
						$lucknum_array = array();
						$lucknum_list = M('unitary_lucknum')->where(array('token' => $this->token, 'unitary_id' => $val['unitary_id']))->field('lucknum')->select();

						foreach ($lucknum_list as $lv) {
							$lv['lucknum'] = $lv['lucknum'] ? $lv['lucknum'] : 0;
							$lucknum_array[] = intval($lv['lucknum']);
						}

						S('LUCKNUM_ARRAY_' . $this->token . '_' . $val['unitary_id'], $lucknum_array);
					}

					$lucknum_cart_count = M('unitary_lucknum')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'order_id' => $orderid, 'cart_id' => $val['id'], 'unitary_id' => $val['unitary_id']))->count();
					$val['count_s'] = $val['count'] - $lucknum_cart_count;

					if (count($lucknum_array) < $unitary['price']) {
						$lucknum_qc = S('LUCKNUM_QC_' . $this->token . '_' . $val['unitary_id']);
						if (($lucknum_qc == NULL) || (count($lucknum_qc) != ($unitary['price'] - count($lucknum_array)))) {
							$unitary_price_array = range(0, $unitary['price'] - 1);
							$lucknum_qc = array_diff_fast($unitary_price_array, $lucknum_array);
							$lucknum_qc = ($lucknum_qc ? $lucknum_qc : $unitary_price_array);
							S('LUCKNUM_QC_' . $this->token . '_' . $val['unitary_id'], $lucknum_qc);
						}
					}
					else {
						$lucknum_qc = range($unitary['price'] + 1, $unitary['price'] * 2);
					}

					$this_lucknum_array = NULL;
					$add_all_lucknum = NULL;

					if (0 < intval($val['count_s'])) {
						if ($val['count_s'] == 1) {
							$this_lucknum_array[] = array_rand($lucknum_qc, $val['count_s']);
						}
						else {
							$this_lucknum_array = array_rand($lucknum_qc, $val['count_s']);
						}

						foreach ($this_lucknum_array as $tlak => $tlav) {
							$add_all_lucknum[$tlak]['token'] = $this->token;
							$add_all_lucknum[$tlak]['wecha_id'] = $this->wecha_id;
							$add_all_lucknum[$tlak]['order_id'] = $orderid;
							$add_all_lucknum[$tlak]['cart_id'] = $val['id'];
							$add_all_lucknum[$tlak]['unitary_id'] = $val['unitary_id'];
							list($s1, $s2) = explode(' ', microtime());
							$mtime = (double) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
							$add_all_lucknum[$tlak]['addtime'] = $mtime;

							if (0 < count($lucknum_qc)) {
								$add_all_lucknum[$tlak]['lucknum'] = $tlav;
								unset($lucknum_qc[$add_all_lucknum[$tlak]['lucknum']]);
								$lucknum_array[] = intval($add_all_lucknum[$tlak]['lucknum']);
							}
							else {
								$max_lucknum = M('unitary_lucknum')->where(array('token' => $this->token, 'unitary_id' => $val['unitary_id']))->order('lucknum desc')->find();
								$add_all_lucknum[$tlak]['lucknum'] = $max_lucknum['lucknum'] + 1;
								$lucknum_array[] = intval($add_all_lucknum[$tlak]['lucknum']);
							}

							$add_all_lucknum[$tlak]['state'] = 0;
						}

						if (1000 < count($add_all_lucknum)) {
							$add_all_lucknum_chunk = array_chunk($add_all_lucknum, 1000);

							foreach ($add_all_lucknum_chunk as $add_all_val) {
								$allid = M('unitary_lucknum')->addAll($add_all_val);
							}
						}
						else {
							M('unitary_lucknum')->addAll($add_all_lucknum);
						}

						S('LUCKNUM_ARRAY_' . $this->token . '_' . $val['unitary_id'], $lucknum_array);
						S('LUCKNUM_QC_' . $this->token . '_' . $val['unitary_id'], $lucknum_qc);
						$params2['site'] = array('token' => $this->token, 'from' => '一元购购买消息', 'content' => '您的一元购有新的商品被购买' . $val['count'] . '人次，请注意查看。商品名称：' . $unitary['name']);
						$error2 = MessageFactory::method($params2, 'SiteMessage');
						$pay_count = M('unitary_lucknum')->where(array('token' => $this->token, 'unitary_id' => $val['unitary_id']))->count();
						$save_unitary2['proportion'] = ($pay_count / $unitary['price']) * 100;
						$update_unitary2 = $this->m_unitary->where(array('token' => $this->token, 'id' => $val['unitary_id']))->save($save_unitary2);
						if (($unitary['price'] <= $pay_count) && ($unitary['state'] != 2)) {
							$params['site'] = array('token' => $this->token, 'from' => '一元购开奖消息', 'content' => '您的一元购有新的商品开奖，请注意查看。商品名称：' . $unitary['name']);
							$error = MessageFactory::method($params, 'SiteMessage');
							$where_lucknum_all['token'] = $this->token;
							$lucknum_all_count = $this->m_lucknum->where($where_lucknum_all)->count();

							if ($lucknum_all_count < 100) {
								$save_unitary['lastnum'] = $lucknum_all_count;
							}
							else {
								$save_unitary['lastnum'] = 100;
							}

							$lucknum_all = $this->m_lucknum->where($where_lucknum_all)->order('addtime desc')->limit($save_unitary['lastnum'])->select();
							$save_unitary['lasttime'] = $lucknum_all[0]['id'];
							$sum = 0;

							foreach ($lucknum_all as $avo) {
								$thistime = floor($avo['addtime'] / 1000);
								$ms = substr($avo['addtime'], -3);
								$sum = $sum + (date('H', $thistime) . date('i', $thistime) . date('s', $thistime) . $ms);
							}

							$lucknum = fmod($sum, $unitary['price']);
							$save_unitary['lucknum'] = $lucknum;
							$save_unitary['state'] = 2;
							$save_unitary['endtime'] = time() + $unitary['opentime'];
							$where_cart3['state'] = 0;
							$where_cart3['token'] = $this->token;
							$where_cart3['unitary_id'] = $val['unitary_id'];
							$del_cart3 = $this->m_cart->where($where_cart3)->delete();
							$save_unitary['proportion'] = 100;
							$update_unitary = $this->m_unitary->where(array('token' => $this->token, 'id' => $val['unitary_id']))->save($save_unitary);
							$where_lucknum2['unitary_id'] = $val['unitary_id'];
							$where_lucknum2['token'] = $this->token;
							$where_lucknum2['lucknum'] = $lucknum;
							$where_lucknum2['state'] = 0;
							$save_lucknum2['state'] = 1;
							$update_lucknum2 = $this->m_lucknum->where($where_lucknum2)->save($save_lucknum2);
							$where_lucknum2['state'] = 1;
							$find_lucknum2 = $this->m_lucknum->where($where_lucknum2)->find();
							$model = new templateNews();
							$model->sendTempMsg('TM00695', array('href' => $this->siteUrl . U('Wap/Unitary/goodswhere', array('token' => $this->token, 'unitaryid' => $val['unitary_id'])), 'wecha_id' => $find_lucknum2['wecha_id'], 'title' => '一元夺宝中奖通知', 'headinfo' => '恭喜您在一元夺宝中获得【' . $unitary['name'] . '】点击查看', 'program' => $unitary['name'], 'result' => date('Y年m月d日H时i分s秒'), 'remark' => ''));
						}

						$update_order = M('unitary_order')->where(array('token' => $this->token, 'pigcms_id' => $orderid))->save(array('addtime' => 0));
					}
					else {
						$update_order = M('unitary_order')->where(array('token' => $this->token, 'pigcms_id' => $orderid))->save(array('addtime' => 0));
					}
				}
			}
		}
	}
}

echo "\r\n";

?>
