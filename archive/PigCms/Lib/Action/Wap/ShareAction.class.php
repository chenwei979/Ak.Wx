<?php

class ShareAction extends WapAction
{
	public function __construct()
	{
		parent::_initialize();
	}

	public function shareData()
	{
		if (isset($_POST['wecha_id']) || isset($_GET['wecha_id'])) {
			$row = array();
			$row['token'] = $this->token;
			$row['wecha_id'] = $this->wecha_id;
			$row['to'] = $this->_post('to') ? $this->_post('to') : $this->_get('to');
			$row['module'] = $this->_post('module') ? $this->_post('module') : $this->_get('module');
			$row['moduleid'] = intval($this->_post('moduleid')) ? intval($this->_post('moduleid')) : intval($this->_get('moduleid'));
			$row['time'] = time();

			if ($this->_post('url')) {
				$row['url'] = $this->_post('url');
			}

			F('s', $row);
			S('s', $row);
			M('share')->add($row);
			$shareSet = M('Share_set')->where(array('token' => $this->token))->find();

			if ($shareSet) {
				$row2 = array();
				$row2['token'] = $this->token;
				$row2['wecha_id'] = $this->wecha_id;
				$where = array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cat' => 98);
				$now = time();
				$year = date('Y', $now);
				$month = date('m', $now);
				$day = date('d', $now);
				$firstSecond = mktime(0, 0, 0, $month, $day, $year);
				$where['time'] = array('gt', $firstSecond);
				$recordsCount = M('Member_card_use_record')->where($where)->count();

				if ($recordsCount < $shareSet['daylimit']) {
					$row2['expense'] = 0;
					$row2['time'] = $now;
					$row2['cat'] = 98;
					$row2['staffid'] = 0;
					$row2['score'] = intval($shareSet['score']);
					M('Member_card_use_record')->add($row2);
					M('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->setInc('total_score', $row2['score']);
				}
			}
		}
	}

	public function ShareNum()
	{
		$ShareNumData = explode(',', $_POST['ShareNumData']);
		$table = D($ShareNumData[0]);
		$tableWhere = explode(';', $ShareNumData[1]);
		$tableWhereData = explode(';', $ShareNumData[2]);

		foreach ($tableWhere as $tk => $to) {
			$where[$to] = $tableWhereData[$tk];
		}

		$where['token'] = $_POST['token'] ? $_POST['token'] : $_GET['token'];
		$dbinfo = $table->where($where)->find();
		$table_up = $table->where($where)->save(array($ShareNumData[3] => $dbinfo[$ShareNumData[3]] + 1));
		$data['error'] = 0;
		$this->ajaxReturn($data, 'JSON');
	}

	public function shareNumber()
	{
		if (isset($_POST['options']) && isset($_POST['openid']) && isset($_POST['token'])) {
			$token = htmlspecialchars(addslashes($_POST['token']));
			$wecha_id = htmlspecialchars(addslashes($_POST['openid']));
			$num = $_POST['options'];
			$this->sendText($token, $wecha_id, '恭喜您,获得' . $num . '次游戏机会');
		}
	}

	public function shareScore()
	{
		if (isset($_POST['options']) && isset($_POST['openid']) && isset($_POST['token'])) {
			$token = htmlspecialchars(addslashes($_POST['token']));
			$wecha_id = htmlspecialchars(addslashes($_POST['openid']));
			$score = $_POST['options'];

			if ($this->_shareScore($token, $wecha_id, $score)) {
				exit(json_encode(array('status' => 200, 'msg' => '')));
			}
		}
	}

	private function _shareScore($token, $wecha_id, $score)
	{
		$info = M('Userinfo')->where(array('token' => $token, 'wecha_id' => $wecha_id))->order('id DESC')->find();
		if ($info && $info['getcardtime']) {
			if ($score < 0) {
				$sharescore = M('Userinfo')->where(array('id' => $info['id']))->setDec('total_score', abs($score));
			}
			else {
				$sharescore = M('Userinfo')->where(array('id' => $info['id']))->setInc('total_score', $score);
			}

			$total_score = $info['total_score'] + $score;
			$model = new templateNews();
			$tempKey = 'TM00335';
			$dataArr = array('href' => '', 'wecha_id' => $wecha_id, 'first' => '您有新积分到账，详情如下。', 'account' => $info['wechaname'], 'time' => date('Y年m月d日H时i分s秒'), 'type' => '微游戏邀请朋友获赠积分', 'creditChange' => '到账', 'number' => $score, 'creditName' => '账户积分', 'amount' => $total_score, 'remark' => '您可以进入会员卡中心，随时查询账户余额。');
			$model->sendTempMsg($tempKey, $dataArr);
			return true;
		}

		return false;
	}

	public function shareRedpacket()
	{
		if (!empty($_POST) && isset($_POST['options']) && isset($_POST['openid']) && isset($_POST['token'])) {
			$fansinfo = array('openid' => htmlspecialchars(addslashes($_POST['openid'])));
			$options = explode('#', $_POST['options']);
			$params = array('min_money' => round($options[0]), 'max_money' => round($options[1]), 'hb_type' => 1);

			if ($money = $this->_shareRedpacket($fansinfo, $params, htmlspecialchars(addslashes($_POST['token'])))) {
				exit(json_encode(array('status' => 200, 'msg' => $money)));
			}
		}
	}

	private function _shareRedpacket($fansinfo = array(), $params = array(), $token)
	{
		if (($fansinfo['openid'] == '') || ($token == '')) {
			return false;
		}

		if (($params['min_money'] == '') || ($params['max_money'] == '')) {
			return false;
		}

		$config = array();
		$config['send_name'] = $params['send_name'] ? $params['send_name'] : '分享红包';
		$config['wishing'] = $params['wishing'] ? $params['wishing'] : '恭喜发财,大吉大利';
		$config['act_name'] = $params['act_name'] ? $params['act_name'] : '分享红包';
		$config['remark'] = $params['remark'] ? $params['remark'] : '分享红包';
		$config['token'] = $token;
		$config['openid'] = $fansinfo['openid'];
		$config['money'] = rand_money($params['min_money'], $params['max_money']);

		if ($params['hb_type'] == 1) {
			$config['nick_name'] = $params['send_name'];
			$hb = new Hongbao($config);
			$res = json_decode($hb->send(), true);
		}
		else if ($params['hb_type'] == 2) {
			$config['total_num'] = $params['group_nums'];
			$hb = new Hongbao($config);
			$res = json_decode($hb->FissionSend(), true);
		}

		if ($res['status'] == 'SUCCESS') {
			return $config['money'];
		}

		return false;
	}

	public function shareCard()
	{
		$card_id = htmlspecialchars(addslashes($_POST['options']));
		$wecha_id = htmlspecialchars(addslashes($_POST['openid']));
		$token = htmlspecialchars(addslashes($_POST['token']));

		if ($this->_shareCard($card_id, $wecha_id, $token)) {
			exit(json_encode(array('status' => 200, 'msg' => '')));
		}
	}

	private function _shareCard($card_id = '', $wecha_id = '', $token = '')
	{
		if (($token == '') || ($wecha_id == '') || ($card_id == '')) {
			return false;
		}

		$thisWxUser = M('Wxuser')->field('appid,appsecret,winxintype')->where(array('token' => $token))->find();
		$apiOauth = new apiOauth();
		$access_token = $apiOauth->update_authorizer_access_token($thisWxUser['appid']);

		if ($access_token == '') {
			return flase;
		}

		$msgtype = 'wxcard';
		$postData = '{"touser":"' . $wecha_id . '","msgtype":"' . $msgtype . '","' . $msgtype . '":{"card_id":"' . $card_id . '"}}';
		$extraUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token;
		$result_json = $this->curlGet($extraUrl, 'POST', $postData);
		$result_array = json_decode($result_json, true);

		if ($result_array['errcode'] == 0) {
			return true;
		}
		else {
			return false;
		}

		return false;
	}

	public function curlGet($url, $method = 'get', $data = '')
	{
		$ch = curl_init();
		$header = 'Accept-Charset: utf-8';
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
}

?>
