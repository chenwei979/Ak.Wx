<?php

class ShakeAction extends WapAction
{
	public $shake_model;
	public $act_type;

	public function __construct()
	{
		parent::__construct();
		$this->act_type = $this->_get('act_type', 'intval');
		$this->shake_model = M('Shake');
	}

	public function index()
	{
		if (!in_array($this->act_type, array('2', '3')) || !$this->wecha_id) {
			echo '参数错误';
			exit();
		}

		if ($this->act_type == 2) {
			$id = $this->_get('id', 'intval');
		}
		else if ($this->act_type == 3) {
			$id = M('Wechat_scene')->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('shake_id');
		}

		$where = array('wecha_id' => $this->wecha_id, 'token' => $this->token, 'act_id' => $this->_get('id', 'intval'), 'act_type' => $this->act_type);
		$member = M('wall_member')->where($where)->find();

		if (!$member) {
			header('location:' . U('Scene_member/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'id' => $where['act_id'], 'act_type' => $where['act_type'], 'name' => 'shake')));
			exit();
		}

		$info = array();
		$info['phone'] = $this->_get('phone');
		$thisShake = $this->shake_model->where(array('token' => $this->token, 'id' => $id, 'isopen' => 1))->find();
		$thisShake['rule'] = nl2br($thisShake['rule']);
		$thisShake['info'] = nl2br($thisShake['info']);
		$thisShake['act_id'] = $this->_get('id', 'intval');
		$this->assign('act_type', $this->act_type);
		if ((time() < ($thisShake['endtime'] + $thisShake['usetime'] + $thisShake['starttime'])) && ($thisShake['isact'] == 1)) {
			$thisShake['isact'] = 1;
		}
		else {
			$thisShake['isact'] = 0;
		}

		$this->assign('info', $thisShake);
		$this->display();
	}

	public function shakeActivityStatus()
	{
		$thisShake = $this->shake_model->where(array('token' => $this->token, 'id' => intval($_GET['id'])))->find();
		if ((time() < ($thisShake['endtime'] + $thisShake['usetime'] + $thisShake['starttime'])) && ($thisShake['isact'] == 1)) {
			echo '{"isact":1}';
			exit();
		}
		else {
			echo '{"isact":0}';
			exit();
		}
	}

	public function refreshScreen()
	{
		$act_type = $this->_get('act_type', 'intval');
		$where = array();
		$where['token'] = $this->_get('token');
		$where['id'] = $this->_get('id', 'intval');
		$where['isopen'] = '1';
		$thisShake = $this->shake_model->where($where)->find();

		if (empty($thisShake)) {
			echo -1;
			exit();
		}

		if ($thisShake) {
			$data = array();
			$swhere = array('shakeid' => $where['id'], 'wecha_id' => $this->wecha_id, 'round' => '0', 'token' => $this->token);

			if ($act_type == 2) {
				$swhere['is_scene'] = '0';
				$data['is_scene'] = '0';
			}
			else if ($act_type == 3) {
				$swhere['is_scene'] = '1';
				$data['is_scene'] = '1';
			}

			$shakeRt = M('Shake_rt')->where($swhere)->find();
			$data['token'] = $this->_get('token');
			$data['wecha_id'] = $this->wecha_id;
			$data['shakeid'] = $this->_get('id');
			$data['count'] = intval($_POST['count']);
			$data['round'] = 0;

			if ($data['is_scene'] == '1') {
				$scene = M('wechat_scene')->where(array('is_open' => 1, 'token' => $this->token, 'shake_id' => $this->_get('id')))->find();
				$data['scene_id'] = $scene['id'];
			}

			if ($shakeRt) {
				if (time() < ($thisShake['endtime'] + $thisShake['usetime'] + $thisShake['starttime'])) {
					M('Shake_rt')->where($swhere)->save($data);
				}
				else {
					$_POST['count'] = M('Shake_rt')->where($swhere)->getField('count');
				}

				echo intval($_POST['count']);
			}
			else {
				M('Shake_rt')->add($data);
				echo intval($_POST['count']);
			}
		}
	}
}

?>
