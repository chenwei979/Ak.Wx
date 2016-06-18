<?php

class GuajiangAction extends LotteryBaseMoreAction
{
	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
		$is_syn = M('Wxuser')->where(array('token' => $this->_get('token')))->getField('is_syn');
		$this->assign('is_syn', $is_syn);
		$agent = $_SERVER['HTTP_USER_AGENT'];

		if (!strpos($agent, 'icroMessenger')) {
		}

		$token = $this->_get('token');
		$wecha_id = $this->wecha_id;

		if (!$wecha_id) {
		}

		$id = $this->_get('id');

		if ($id == '') {
			$this->error('不存在的活动');
		}

		$redata = M('Lottery_record');
		$where = array('token' => $token, 'wecha_id' => $wecha_id, 'lid' => $id);
		$record = $redata->where(array('token' => $token, 'wecha_id' => $wecha_id, 'lid' => $id, 'islottery' => 1))->order('time desc')->select();
		$record2 = $redata->where($where)->order('id DESC')->find();
		$this->assign('record', $record);
		$Lottery = M('Lottery')->where(array('id' => $id, 'token' => $token, 'type' => 2, 'status' => 1))->find();

		if (!$Lottery) {
			$this->error('不存在的活动');
		}

		if (!$Lottery['guanzhu'] && !$this->isSubscribe()) {
			$this->memberNotice('', 1);
		}
		else {
			if ($Lottery['needreg'] && empty($this->fans['tel'])) {
				$this->memberNotice();
			}
		}

		$Lottery['renametel'] = $Lottery['renametel'] ? $Lottery['renametel'] : '手机号';
		$Lottery['renamesn'] = $Lottery['renamesn'] ? $Lottery['renamesn'] : 'SN码';

		if (0 < $Lottery['daynums']) {
			$Lottery['numtype'] = 1;
		}

		$data = $Lottery;
		$data['info'] = nl2br($data['info']);
		$data['endinfo'] = nl2br($data['endinfo']);
		$data['info'] = str_replace('&lt;br&gt;', '<br>', $data['info']);
		$data['endinfo'] = str_replace('&lt;br&gt;', '<br>', $data['endinfo']);
		$this->assign('Guajiang', $data);

		if (time() < $Lottery['statdate']) {
			$data['usenums'] = 9;
			$data['winprize'] = '还没开始';
		}
		else {
			if ($this->wecha_id) {
			}

			if ($Lottery['enddate'] < time()) {
				$data['usenums'] = 3;
				$data['endinfo'] = $Lottery['endinfo'];
				$this->assign('Guajiang', $data);
				$this->display();
				exit();
			}

			if ($return['end'] == 3) {
				$data['usenums'] = 2;
				$data['winprize'] = '中奖次数已用完';
			}
			else if ($return['end'] == 4) {
				$data['usenums'] = 2;
				$data['winprize'] = '今天已中奖';
			}
			else if ($return['end'] == -1) {
				$data['usenums'] = 1;
				$data['winprize'] = '抽奖次数已用完';
			}
			else if ($return['end'] == -2) {
				$data['usenums'] = 1;
				$data['winprize'] = '当天次数已用完';
			}
			else if ($return['end'] == -3) {
				$data['usenums'] = 1;
				$data['winprize'] = '分享次数不够';
			}
			else {
				$data['zjl'] = $return['zjl'];
				$data['sn'] = $return['sn'];
				$data['rid'] = $return['rid'];
				$data['wecha_id'] = $wecha_id;
				$data['lid'] = $id;
				$data['winprize'] = $this->getPrizeName($Lottery, $return['prizetype']);
				$data['winprize'] = $data['winprize'] != '7' ? $data['winprize'] : '谢谢参与';
			}
		}

		$data['usecout'] = intval($record2['usenums']) + 1;
		$data['sharecount'] = intval($record2['sharecount']);
		$this->assign('Guajiang', $data);
		$prizeStr = '<p>一等奖: ' . $Lottery['fist'];

		if ($Lottery['displayjpnums']) {
			$prizeStr .= '奖品数量:' . $Lottery['fistnums'];
		}

		$prizeStr .= '</p>';

		if ($Lottery['second']) {
			$prizeStr .= '<p>二等奖: ' . $Lottery['second'];

			if ($Lottery['displayjpnums']) {
				$prizeStr .= '奖品数量:' . $Lottery['secondnums'];
			}

			$prizeStr .= '</p>';
		}

		if ($Lottery['third']) {
			$prizeStr .= '<p>三等奖: ' . $Lottery['third'];

			if ($Lottery['displayjpnums']) {
				$prizeStr .= '奖品数量:' . $Lottery['thirdnums'];
			}

			$prizeStr .= '</p>';
		}

		if ($Lottery['four']) {
			$prizeStr .= '<p>四等奖: ' . $Lottery['four'];

			if ($Lottery['displayjpnums']) {
				$prizeStr .= '奖品数量:' . $Lottery['fournums'];
			}

			$prizeStr .= '</p>';
		}

		if ($Lottery['five']) {
			$prizeStr .= '<p>五等奖: ' . $Lottery['five'];

			if ($Lottery['displayjpnums']) {
				$prizeStr .= '奖品数量:' . $Lottery['fivenums'];
			}

			$prizeStr .= '</p>';
		}

		if ($Lottery['six']) {
			$prizeStr .= '<p>六等奖: ' . $Lottery['six'];

			if ($Lottery['displayjpnums']) {
				$prizeStr .= '奖品数量:' . $Lottery['sixnums'];
			}

			$prizeStr .= '</p>';
		}

		$this->assign('prizeStr', $prizeStr);
		$this->display();
	}

	public function getajax()
	{
		$token = $this->_get('token');
		$wecha_id = $this->_get('oneid');
		$id = intval($this->_get('id'));
		$Lottery = M('Lottery')->where(array('id' => $id))->find();
		if (($Lottery['other_source'] == 'scene') && ($Lottery['scene_state'] == 0)) {
			exit('{"error":"1","msg":"活动未开始"}');
		}

		if (time() < $Lottery['statdate']) {
			echo '{"error":1,"msg":"活动未开始"}';
			exit();
		}

		if ($Lottery['enddate'] < time()) {
			echo '{"error":1,"msg":"活动已结束"}';
			exit();
		}

		$data = $this->prizeHandle($token, $wecha_id, $Lottery);

		if ($data['end'] == 3) {
			$sn = $data['sn'];
			$uname = $data['wecha_name'];
			$prize = $data['prize'];
			$tel = $data['phone'];
			$msg = '中奖次数已用完';
			echo '{"error":1,"msg":"' . $msg . '"}';
			exit();
		}

		if ($data['end'] == 4) {
			$msg = '今天已中奖';
			echo '{"error":1,"msg":"' . $msg . '"}';
			exit();
		}

		if ($data['end'] == -1) {
			$msg = '抽奖次数已用完';
			echo '{"error":1,"msg":"' . $msg . '"}';
			exit();
		}

		if ($data['end'] == -2) {
			$msg = '当天次数已用完';
			echo '{"error":1,"msg":"' . $msg . '"}';
			exit();
		}

		if ($data['end'] == -3) {
			$msg = '还需分享' . $data['sharediff'] . '次';
			echo '{"error":1,"msg":"' . $msg . '"}';
			exit();
		}

		if ((1 <= $data['prizetype']) && ($data['prizetype'] <= 6)) {
			echo '{"success":1,"sn":"' . $data['sncode'] . '","prizetype":"' . $data['prizetype'] . '","usenums":"' . $data['usenums'] . '","rid":"' . $data['rid'] . '"}';
		}
		else {
			echo '{"success":0,"prizetype":"","usenums":"' . $data['usenums'] . '","msg":"谢谢参与"}';
		}

		exit();
	}

	private function scene_start($id)
	{
		$scene_id = M('Wechat_scene')->where(array('is_open' => '1', 'token' => $this->token, 'guaguaka_id' => $id))->getField('id');
		$cache = S('scene_' . $this->token . '_' . $scene_id);
		return $cache['type'] == 'guaguaka';
	}
}

?>
