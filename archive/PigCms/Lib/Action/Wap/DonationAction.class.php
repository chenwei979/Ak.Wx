<?php
/**
 * 募捐
 */
class DonationAction extends WapAction
{
	public $_id = 0;
	public $_set;

	public function _initialize()
	{
		parent::_initialize();
		$this->_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$this->assign('id', $this->_id);
		$this->_set = D('Donation_set')->where(array('token' => $this->token, 'did' => $this->_id))->find();
		$this->_set['circle_name'] = isset($this->_set['circle_name']) && $this->_set['circle_name'] ? $this->_set['circle_name'] : '校友';
		$this->assign('set', $this->_set);
	}

	public function index()
	{
		if (empty($this->token)) {
			$this->error('非法操作');
			exit();
		}

		if (!$this->isSubscribe()) {
			$this->memberNotice('详细了解捐款信息，请点击关注公众账号。', 1, '', '关注');
		}

		$donation = $this->check_donation();
		$total_array = $this->total_cnt();
		$count_share = D('Donation_order')->field('count(distinct(wecha_id)) as people, count(distinct (sid)) as share')->where(array(
	'did'   => $this->_id,
	'token' => $this->token,
	'paid'  => 1,
	'sid'   => array('gt', 0)
	))->find();
		$count_share['people'] = $count_share['people'] ? intval($count_share['people']) : 0;
		$count_share['share'] = $count_share['share'] ? intval($count_share['share']) : 0;
		$this->assign('count_share', $count_share);
		$sql = 'SELECT sum(o.price) as price, o.sid, o.isanonymous, u.wechaname, u.truename, u.city, u.portrait FROM ' . C('DB_PREFIX') . 'userinfo AS u INNER JOIN ' . C('DB_PREFIX') . 'donation_share AS s ON s.wecha_id=u.wecha_id INNER JOIN  ' . C('DB_PREFIX') . 'donation_order AS o ON s.id=o.sid WHERE o.paid=1 AND o.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND o.did=\'' . $this->_id . '\' GROUP BY s.id ORDER BY price DESC LIMIT 0, 5';
		$orders = D()->query($sql);
		$share_ids = array();

		foreach ($orders as $ord) {
			if ($ord['sid'] && !in_array($ord['sid'], $share_ids)) {
				$share_ids[] = $ord['sid'];
			}
		}

		if ($share_ids) {
			$count_list_tmp = D('Donation_order')->field('sid, count(distinct (wecha_id)) as cnt, sum(price) as price')->where(array(
	'did'   => $this->_id,
	'token' => $this->token,
	'paid'  => 1,
	'sid'   => array('in', $share_ids)
	))->group('sid')->select();
			$count_list = array();

			foreach ($count_list_tmp as $r) {
				$count_list[$r['sid']] = $r;
			}

			$share_list_tmp = D('Donation_share')->where(array(
	'token' => $this->token,
	'id'    => array('in', $share_ids)
	))->select();
			$share_list = array();

			foreach ($share_list_tmp as $sr) {
				$share_list[$sr['id']] = $sr;
			}
		}

		foreach ($orders as &$ors) {
			$ors['people'] = isset($count_list[$ors['sid']]['cnt']) ? intval($count_list[$ors['sid']]['cnt']) : 1;
			$ors['price'] = isset($count_list[$ors['sid']]['price']) ? $count_list[$ors['sid']]['price'] : $ors['price'];
			$ors['share_content'] = isset($share_list[$ors['sid']]['content']) ? $share_list[$ors['sid']]['content'] : $donation['share_content1'];
		}

		$this->assign('orders', $orders);
		$this->assign('total_arr', $total_array);
		$this->assign('donation', $donation);
		$this->display();
	}

	private function total_cnt()
	{
		$total_array = D('Donation_order')->field('count(distinct (wecha_id)) as cnt, sum(price) as price')->where(array('token' => $this->token, 'paid' => 1, 'did' => $this->_id))->find();
		$total_array['cnt'] = $total_array['cnt'] ? intval($total_array['cnt']) : 0;
		$total_array['price'] = $total_array['price'] ? $total_array['price'] : 0;
		return $total_array;
	}

	private function check_donation()
	{
		$donation = D('Donation')->where(array('token' => $this->token, 'id' => $this->_id))->find();

		if (empty($donation)) {
			$this->error('该募捐或不存在或意见取消了，请您联系发起机构核实情况！');
			exit();
		}

		if (time() < $donation['starttime']) {
			$this->error('此次募捐活动还未开始！');
			exit();
		}

		if ($donation['endtime'] < time()) {
			$this->error('此次募捐已经结束了！');
			exit();
		}

		if (!strstr($donation['pic'], 'http://')) {
			$donation['pic'] = $this->siteUrl . $donation['pic'];
		}

		if ($donation['share_pic']) {
			if (!strstr($donation['share_pic'], 'http://')) {
				$donation['share_pic'] = $this->siteUrl . $donation['share_pic'];
			}
		}
		else if (!strstr($donation['reply_pic'], 'http://')) {
			$donation['share_pic'] = $this->siteUrl . $donation['reply_pic'];
		}
		else {
			$donation['share_pic'] = $donation['reply_pic'];
		}

		$donation['day'] = ((strtotime(date('Ymd000000', $donation['endtime'])) - strtotime(date('Ymd000000'))) / 86400) + 1;
		return $donation;
	}

	public function to_pay()
	{
		$price = (isset($_GET['price']) ? $_GET['price'] : 0);
		$sid = (isset($_GET['sid']) ? $_GET['sid'] : 0);
		$isanonymous = (isset($_GET['isanonymous']) ? intval($_GET['isanonymous']) : 0);
		$donation = $this->check_donation();

		if ($price < 0.01) {
			$this->error('捐赠的金额至少是一分钱');
		}

		$data = array();
		$data['price'] = $price;
		$data['token'] = $this->token;
		$data['wecha_id'] = $this->wecha_id;
		$data['did'] = $this->_id;
		$data['sid'] = $sid;
		$data['dateline'] = time();
		$data['isanonymous'] = $isanonymous;
		list($msec, $sec) = explode(' ', microtime());
		$orderid = date('YmdHis', $sec) . substr($msec, 2, 4);
		$orderid = 'd' . $orderid;
		$data['orderid'] = $orderid;

		if (D('Donation_order')->add($data)) {
			$payConfig = M('Alipay_config')->where(array('token' => $this->token, 'open' => 1))->find();
			$payConfigInfo = unserialize($payConfig['info']);
			$payConfig = array_map('trim', $payConfigInfo['weixin']);
			$pl = ($payConfig['open'] ? 0 : 1);
			$this->redirect(U('Alipay/to_pay', array('price' => $price, 'orderName' => $donation['name'], 'orderid' => $orderid, 'from' => 'Donation', 'token' => $this->token, 'wecha_id' => $this->wecha_id, 'pay_type' => 'Weixin', 'platform' => $pl)));
		}
		else {
			$this->error('服务器出错，稍后重试！');
		}
	}

	public function detail()
	{
		$donation = $this->check_donation();
		$this->assign('donation', $donation);
		$count_share = D('Donation_order')->field('count(distinct(wecha_id)) as people, count(distinct (sid)) as share')->where(array(
	'did'   => $this->_id,
	'token' => $this->token,
	'paid'  => 1,
	'sid'   => array('gt', 0)
	))->find();
		$count_share['people'] = $count_share['people'] ? intval($count_share['people']) : 0;
		$count_share['share'] = $count_share['share'] ? intval($count_share['share']) : 0;
		$this->assign('count_share', $count_share);
		$this->display();
	}

	public function ajax_order()
	{
		$pagesize = 20;
		$page = (isset($_GET['page']) ? intval($_GET['page']) : 1);
		$stat = ($page - 1) * $pagesize;
		$sql = 'SELECT sum(o.price) as price, o.sid, o.isanonymous, u.wechaname, u.truename, u.city, u.portrait FROM ' . C('DB_PREFIX') . 'userinfo AS u INNER JOIN ' . C('DB_PREFIX') . 'donation_share AS s ON s.wecha_id=u.wecha_id INNER JOIN  ' . C('DB_PREFIX') . 'donation_order AS o ON s.id=o.sid WHERE o.paid=1 AND o.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND o.did=\'' . $this->_id . '\' GROUP BY s.id ORDER BY price DESC LIMIT ' . $stat . ', ' . $pagesize;
		$orders = D()->query($sql);
		$donation = $this->check_donation();
		$share_ids = array();

		foreach ($orders as $ord) {
			if ($ord['sid'] && !in_array($ord['sid'], $share_ids)) {
				$share_ids[] = $ord['sid'];
			}
		}

		if ($share_ids) {
			$count_list_tmp = D('Donation_order')->field('sid, count(distinct (wecha_id)) as cnt, sum(price) as price')->where(array(
	'did'   => $this->_id,
	'token' => $this->token,
	'paid'  => 1,
	'sid'   => array('in', $share_ids)
	))->group('sid')->select();
			$count_list = array();

			foreach ($count_list_tmp as $r) {
				$count_list[$r['sid']] = $r;
			}

			$share_list_tmp = D('Donation_share')->where(array(
	'token' => $this->token,
	'id'    => array('in', $share_ids)
	))->select();
			$share_list = array();

			foreach ($share_list_tmp as $sr) {
				$share_list[$sr['id']] = $sr;
			}
		}

		foreach ($orders as &$ors) {
			$ors['people'] = isset($count_list[$ors['sid']]['cnt']) ? intval($count_list[$ors['sid']]['cnt']) : 1;
			$ors['price'] = isset($count_list[$ors['sid']]['price']) ? $count_list[$ors['sid']]['price'] : $ors['price'];
			$ors['share_content'] = isset($share_list[$ors['sid']]['content']) ? $share_list[$ors['sid']]['content'] : $donation['share_content1'];
			$ors['city'] = $ors['city'] ? $ors['city'] : '';
		}

		$page = (count($orders) < $pagesize ? 0 : $page + 1);
		exit(json_encode(array('data' => $orders, 'page' => $page)));
	}

	public function agreement()
	{
		$donation = $this->check_donation();
		$this->assign('donation', $donation);
		$set = D('Donation_set')->where(array('token' => $this->token, 'did' => $this->_id))->find();
		$set['agreement'] = isset($set['agreement']) && $set['agreement'] ? $set['agreement'] : '商家暂无协议说明';
		$this->assign('set', $set);
		$this->display();
	}

	public function share()
	{
		$sid = (isset($_GET['sid']) ? intval($_GET['sid']) : 0);

		if ($share = D('Donation_share')->where(array('id' => $sid))->find()) {
			$this->assign('share', $share);
			$userinfo = D('Userinfo')->where(array('wecha_id' => $share['wecha_id'], 'token' => $this->token))->find();
			$this->_id = $share['did'];
			$donation = $this->check_donation();
			$this->assign('userinfo', $userinfo);
			$this->assign('donation', $donation);
			$cont_order = D('Donation_order')->field('count(distinct (wecha_id)) as cnt, sum(price) as price')->where(array('did' => $share['did'], 'token' => $this->token, 'sid' => $sid, 'paid' => 1))->find();
			$cont_order['price'] = $cont_order['price'] ? $cont_order['price'] : 0;
			$this->assign('cont_order', $cont_order);
			$sql = 'SELECT sum(o.price) as price, o.dateline, u.wechaname, u.portrait FROM ' . C('DB_PREFIX') . 'donation_order AS o INNER JOIN ' . C('DB_PREFIX') . 'userinfo AS u ON o.wecha_id=u.wecha_id WHERE o.paid=1 AND o.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND o.sid=' . $sid . ' GROUP BY o.wecha_id ORDER BY o.id DESC LIMIT 0, 20';
			$orders = D()->query($sql);
			$this->assign('orders', $orders);
			$this->display();
		}
		else {
			$this->error('邀请有误', U('Donation/index', array('token' => $this->token, 'id' => $this->_id, 'wecha_id' => $this->wecha_id)));
		}
	}

	public function invitation()
	{
		$donation = $this->check_donation();
		$this->assign('donation', $donation);

		if ($share = D('Donation_share')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'did' => $this->_id))->find()) {
			$cont_order = D('Donation_order')->field('count(distinct (wecha_id)) as cnt, sum(price) as price')->where(array('did' => $this->_id, 'token' => $this->token, 'paid' => 1, 'sid' => $share['id']))->find();
		}

		$cont_order['cnt'] = isset($cont_order['cnt']) && $cont_order['cnt'] ? intval($cont_order['cnt']) : 0;
		$cont_order['price'] = isset($cont_order['price']) && $cont_order['price'] ? $cont_order['price'] : 0;
		$this->assign('share', $share);
		$this->assign('cont_order', $cont_order);
		$this->display();
	}

	public function creat_invitation()
	{
		$content = (isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '');

		if (empty($content)) {
			exit(json_encode(array('status' => true, 'info' => '邀请口号不能为空')));
		}

		if (80 < dstrlen($content)) {
			exit(json_encode(array('status' => true, 'info' => '邀请口号不能超过80个汉字')));
		}

		$data = array('wecha_id' => $this->wecha_id, 'token' => $this->token, 'content' => $content, 'did' => $this->_id);
		$data['dateline'] = time();

		if ($share = D('Donation_share')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'did' => $this->_id))->find()) {
			if (D('Donation_share')->where(array('id' => $share['id']))->save($data)) {
				exit(json_encode(array('status' => false, 'data' => $share['id'])));
			}
			else {
				exit(json_encode(array('status' => true, 'info' => '分享失败，稍后重试！')));
			}
		}
		else if ($sid = D('Donation_share')->add($data)) {
			exit(json_encode(array('status' => false, 'data' => $sid)));
		}
		else {
			exit(json_encode(array('status' => true, 'info' => '分享失败，稍后重试！')));
		}
	}

	public function invitation_success()
	{
		$sid = (isset($_GET['sid']) ? intval($_GET['sid']) : 0);

		if ($share = D('Donation_share')->where(array('token' => $this->token, 'id' => $sid))->find()) {
			$donation = $this->check_donation();
			$total = D('Donation_order')->where(array('token' => $this->token, 'paid' => 1, 'did' => $share['did'], 'sid' => $share['id']))->sum('price');
			$userinfo = D('Userinfo')->where(array('wecha_id' => $share['wecha_id'], 'token' => $this->token))->find();
			$this->assign('userinfo', $userinfo);
			$this->assign('total', $total ? $total : 0);
			$this->assign('share', $share);
			$this->assign('donation', $donation);
			$this->display();
		}
		else {
			$this->error('邀请有误', U('Donation/index', array('token' => $this->token, 'id' => $this->_id, 'wecha_id' => $this->wecha_id)));
		}
	}

	public function pay_success()
	{
		$orderid = (isset($_GET['orderid']) ? $_GET['orderid'] : '');

		if ($order = D('Donation_order')->where(array('orderid' => $orderid))->find()) {
			$this->_id = $order['did'];
			$this->assign('id', $this->_id);
			$donation = $this->check_donation();
			$total_price = D('Donation_order')->where(array('token' => $order['token'], 'wecha_id' => $order['wecha_id'], 'paid' => 1, 'did' => $order['did']))->sum('price');
			$pic = '';
			$note = '奉献一点爱心';
			$medals = D('Donation_medal')->where(array('token' => $order['token'], 'did' => $order['did']))->order('money DESC')->select();

			foreach ($medals as $medal) {
				if ($medal['money'] < $total_price) {
					$pic = $medal['pic'];
					$note = $medal['note'];
					break;
				}
			}

			$this->assign('pic', $pic);
			$this->assign('note', $note);
			$set = D('Donation_set')->where(array('token' => $order['token'], 'did' => $order['did']))->find();
			$this->assign('set', $set);
			$donation['share_pic'] = $pic ? $pic : $this->siteUrl . '/tpl/static/donation/images/gift.png';
			$donation['name'] = $donation['name'] . '我为学校' . $note;
			$this->assign('donation', $donation);
			$this->display();
		}
		else {
			$this->error('不存在的订单', U('Donation/index', array('token' => $this->token, 'id' => $this->_id, 'wecha_id' => $this->wecha_id)));
		}
	}

	public function check_pay()
	{
		$orderid = (isset($_GET['orderid']) ? $_GET['orderid'] : '');
		$order = D('Donation_order')->where(array('orderid' => $orderid))->find();
		if (isset($order['paid']) && $order['paid']) {
			exit(json_encode(array('status' => 1, 'id' => $order['did'])));
		}
		else {
			exit(json_encode(array('status' => 0)));
		}
	}

	public function payReturn()
	{
		if (isset($_GET['nohandle'])) {
			$this->redirect(U('Donation/pay_success', array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
		else {
			$out_trade_no = $_GET['orderid'];
			ThirdPayDonation::index($out_trade_no);
		}
	}

	public function info()
	{
		$type = (isset($_GET['type']) ? intval($_GET['type']) : 2);
		$this->assign('type', $type);
		$donation = $this->check_donation();
		$this->assign('donation', $donation);
		$my_order = D('Donation_order')->field('count(1) as cnt, sum(price) as price')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'paid' => 1, 'did' => $this->_id))->find();
		$my_order['cnt'] = $my_order['cnt'] ? intval($my_order['cnt']) : 0;
		$my_order['price'] = $my_order['price'] ? $my_order['price'] : 0;
		$this->assign('my_order', $my_order);

		if ($type == 3) {
			$my_value = D('Donation_value')->where(array('did' => $this->token, 'did' => $this->_id, 'wecha_id' => $this->wecha_id))->find();
			$myorder = (isset($my_value['num']) ? intval($my_value['num']) : 0);
			$this->assign('myorder', $myorder);
			$sql = 'SELECT rank FROM (SELECT wecha_id, num, (@mycnt := @mycnt +1) AS rank, id FROM ' . C('DB_PREFIX') . 'donation_value WHERE token=\'' . $this->token . '\' AND did=\'' . $this->_id . '\' ORDER BY num DESC, id ASC) AS a WHERE wecha_id=\'' . $this->wecha_id . '\'';
			D()->query('set @mycnt = 0;');
			$order = D()->query($sql);
			$this->assign('mycount', isset($order[0]['rank']) && $order[0]['rank'] ? intval($order[0]['rank']) : 0);
			$sql = 'SELECT v.num, u.portrait, u.wechaname FROM ' . C('DB_PREFIX') . 'donation_value as v INNER JOIN ' . C('DB_PREFIX') . 'userinfo AS u ON u.wecha_id=v.wecha_id WHERE v.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND v.did=\'' . $this->_id . '\'  ORDER BY v.num DESC, v.id ASC LIMIT 0, 10';
			$value_list = D()->query($sql);
			$this->assign('value_list', $value_list);
		}
		else if ($type == 2) {
			$sql = 'SELECT rank FROM (SELECT wecha_id, p, (@mycnt := @mycnt +1) AS rank FROM (select wecha_id, sum(price) as p, max(id) as id FROM ' . C('DB_PREFIX') . 'donation_order WHERE paid =1 AND token=\'' . $this->token . '\' AND did=\'' . $this->_id . '\' GROUP BY wecha_id) as a ORDER BY p DESC, id ASC) AS b WHERE wecha_id=\'' . $this->wecha_id . '\'';
			D()->query('set @mycnt = 0;');
			$order = D()->query($sql);
			$this->assign('order', isset($order[0]['rank']) && $order[0]['rank'] ? intval($order[0]['rank']) : 0);
			$shares = D('Donation_share')->where(array('token' => $this->token, 'did' => $this->_id, 'wecha_id' => $this->wecha_id))->select();
			$sids = array();

			foreach ($shares as $s) {
				$sids[] = $s['id'];
			}

			if ($sids) {
				$share_count = D('Donation_order')->field('count(1) as cnt, sum(price) as price')->where(array(
	'token' => $this->token,
	'sid'   => array('in', $sids),
	'paid'  => 1,
	'did'   => $this->_id
	))->find();
			}

			$share_count['cnt'] = isset($share_count['cnt']) ? intval($share_count['cnt']) : 0;
			$share_count['price'] = isset($share_count['price']) ? $share_count['price'] : 0;
			$this->assign('share_count', $share_count);
			$sql = 'SELECT SUM( price ) AS p, u.portrait, u.wechaname, max(o.id) as oid FROM ' . C('DB_PREFIX') . 'donation_order as o INNER JOIN ' . C('DB_PREFIX') . 'userinfo AS u ON u.wecha_id=o.wecha_id WHERE o.paid=1 AND o.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND o.did=\'' . $this->_id . '\' GROUP BY o.wecha_id ORDER BY p DESC, oid ASC LIMIT 0, 10';
			$order_list = D()->query($sql);
			$medals = D('Donation_medal')->where(array('token' => $this->token, 'did' => $this->_id))->order('money DESC')->select();

			foreach ($order_list as &$oo) {
				$oo['medal'] = '';

				foreach ($medals as $medal) {
					if ($medal['money'] < $oo['p']) {
						for ($i = 0; $i < $medal['num']; $i++) {
							$oo['medal'] .= '<i style="display: inline-block;width: 40px;height: 40px;margin-right: 4px;"><img src="' . $medal['pic'] . '"></i>';
						}

						break;
					}
				}
			}

			$this->assign('order_list', $order_list);
		}
		else {
			$sql = 'SELECT i.id, i.title, i.text, d.dateline FROM ' . C('DB_PREFIX') . 'img AS i INNER JOIN ' . C('DB_PREFIX') . 'donation_dynamic AS d ON d.image_id=i.id WHERE d.did=' . $this->_id . ' AND d.token=\'' . $this->token . '\' ORDER BY d.id DESC LIMIT 0, 10';
			$dynamic_list = D()->query($sql);
			$this->assign('dynamic_list', $dynamic_list);
		}

		$this->display();
	}

	public function ajax_list()
	{
		$type = (isset($_GET['type']) ? intval($_GET['type']) : 2);
		$page = (isset($_GET['page']) ? intval($_GET['page']) : 2);
		$pagesize = 10;
		$star = ($page - 1) * $pagesize;

		if ($type == 3) {
			$sql = 'SELECT v.num, u.portrait, u.wechaname FROM ' . C('DB_PREFIX') . 'donation_value as v INNER JOIN ' . C('DB_PREFIX') . 'userinfo AS u ON u.wecha_id=v.wecha_id WHERE v.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND v.did=\'' . $this->_id . '\'  ORDER BY v.num DESC, v.id ASC LIMIT ' . $star . ', ' . $pagesize;
			$list = D()->query($sql);

			foreach ($list as $k => &$ll) {
				$ll['ranking'] = $star + $k + 1;
			}
		}
		else if ($type == 2) {
			$sql = 'SELECT SUM( price ) AS p, u.portrait, u.wechaname, max(o.id) as oid FROM ' . C('DB_PREFIX') . 'donation_order as o INNER JOIN ' . C('DB_PREFIX') . 'userinfo AS u ON u.wecha_id=o.wecha_id WHERE o.paid=1 AND o.token=\'' . $this->token . '\' AND u.token=\'' . $this->token . '\' AND o.did=\'' . $this->_id . '\' GROUP BY o.wecha_id ORDER BY p DESC, oid ASC LIMIT ' . $star . ', ' . $pagesize;
			$list = D()->query($sql);
			$medals = D('Donation_medal')->where(array('token' => $this->token, 'did' => $this->_id))->order('money DESC')->select();

			foreach ($list as $k => &$ll) {
				$ll['medal'] = '';

				foreach ($medals as $medal) {
					if ($medal['money'] < $ll['p']) {
						for ($i = 0; $i < $medal['num']; $i++) {
							$ll['medal'] .= '<i style="display: inline-block;width: 40px;height: 40px;margin-right: 4px;"><img src="' . $medal['pic'] . '"></i>';
						}

						break;
					}
				}

				$ll['ranking'] = $star + $k + 1;
			}
		}
		else {
			$sql = 'SELECT i.id, i.title, i.text, d.dateline FROM ' . C('DB_PREFIX') . 'img AS i INNER JOIN ' . C('DB_PREFIX') . 'donation_dynamic AS d ON d.image_id=i.id WHERE d.did=' . $this->_id . ' AND d.token=\'' . $this->token . '\' ORDER BY d.id DESC LIMIT ' . $star . ', ' . $pagesize;
			$list = D()->query($sql);

			foreach ($list as $k => &$ll) {
				$ll['dateline'] = date('Y-m-d H:i:s', $ll['dateline']);
			}
		}

		$page = (count($list) == $pagesize ? $page + 1 : 0);
		exit(json_encode(array('error' => 0, 'page' => $page, 'data' => $list)));
	}
}

?>
