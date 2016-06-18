<?php

class SceneAction extends UserAction
{
	public $scene_db;
	public $token;
	public $info;

	public function _initialize()
	{
		parent::_initialize();
		$action_wall = array('wall', 'wall_pic', 'ajaxWall', 'ajaxWallPic');
		$action_Shake = array('shake', 'startShake', 'getCount', 'shakeRun', 'show_shake', 'getConnectNum');

		if (in_array(ACTION_NAME, $action_wall)) {
			$Fun = 'Wall';
		}
		else if (in_array(ACTION_NAME, $action_Shake)) {
			$Fun = 'Shake';
		}
		else {
			$Fun = 'Scene';
		}

		$this->canUseFunction($Fun);
		$this->token = session('token');
		$this->scene_db = D('Wechat_scene');
		$scene_info = M('wechat_scene')->where(array('token' => $this->token, 'is_open' => '1', 'id' => $this->_get('id', 'intval')))->find();

		if ($scene_info) {
			$info = $scene_info;
			$this->assign('id', $scene_info['id']);
			if ((ACTION_NAME == 'wall') || (ACTION_NAME == 'wall_pic')) {
				$info = M('Wall')->where(array('token' => $this->token, 'id' => $scene_info['wall_id']))->find();
			}
			else if (ACTION_NAME == 'shake') {
				$info = M('Shake')->where(array('token' => $this->token, 'id' => $scene_info['shake_id']))->find();
				$info['cheer'] = json_encode(explode('|', $info['cheer']));
			}

			$info['id'] = $scene_info['id'];
			$info['open_vote'] = $scene_info['open_vote'];
			$info['open_lottery'] = $scene_info['open_lottery'];
			$info['open_zzle'] = $scene_info['open_zzle'];
			$info['wall_id'] = $scene_info['wall_id'];
			$info['shake_id'] = $scene_info['shake_id'];
			$info['title'] = $scene_info['title'];
			$info['logo'] = $scene_info['logo'];
			$info['keyword'] = $scene_info['keyword'];
			$info['notice'] = $scene_info['notice'];
			$info['red_packet_id'] = $scene_info['red_packet_id'];
			$info['jiugongge_id'] = $scene_info['jiugongge_id'];
			$info['zajindan_id'] = $scene_info['zajindan_id'];
			$info['shuiguoji_id'] = $scene_info['shuiguoji_id'];
			$info['guaguaka_id'] = $scene_info['guaguaka_id'];
			$info['dazhuanpan_id'] = $scene_info['dazhuanpan_id'];
			$info['qrcode'] = empty($scene_info['qrcode']) ? $info['qrcode'] : $scene_info['qrcode'];
			$info['background'] = $scene_info['background'];
			$info['shake_background'] = $scene_info['shake_background'];
			$info['signin_background'] = $scene_info['signin_background'];
			$info['prize_background'] = $scene_info['prize_background'];
			$info['lottery_background'] = $scene_info['lottery_background'];
			$info['supperzzle_background'] = $scene_info['supperzzle_background'];
			$info['guajiang_background'] = $scene_info['guajiang_background'];
			$info['hongbao_background'] = $scene_info['hongbao_background'];
			$info['jiugongge_background'] = $scene_info['jiugongge_background'];
			$info['luckyfruit_background'] = $scene_info['luckyfruit_background'];
			$info['vote_background'] = $scene_info['vote_background'];
			$info['goldenegg_background'] = $scene_info['goldenegg_background'];
			$action = array('shake_active' => $scene_info['shake_keyword'], 'shake' => $scene_info['shake_keyword'], 'vote' => $scene_info['vote_keyword'], 'vote_countdown' => $scene_info['vote_keyword'], 'red_packet' => $scene_info['hongbao_keyword'], 'zajindan' => $scene_info['goldenegg_keyword'], 'shuiguoji' => $scene_info['luckyfruit_keyword'], 'guaguaka' => $scene_info['guajiang_keyword'], 'dazhuanpan' => $scene_info['lottery_keyword'], 'jiugongge' => $scene_info['jiugongge_keyword']);
			$actionName = strtolower(ACTION_NAME);

			if ($action[$actionName]) {
				$info['header'] = '<li class="themeBox" style="display: none;">微信大屏幕<br>回复“' . $action[$actionName] . '”参与当前活动 </li>';
			}
			else if (ACTION_NAME == 'hudong') {
				$actionName = $_GET['type'];
				$info['header'] = '<li class="themeBox" style="display: none;">微信大屏幕<br>回复“' . $action[$actionName] . '”参与当前活动 </li>';
			}
		}

		$info['wxuser'] = M('wxuser')->where(array('token' => $this->token))->getField('weixin');
		$this->info = $info;
		$this->assign('info', $info);

		if (ACTION_NAME == 'wall') {
			$this->assign('join_count', M('Wall_message')->where(array(
	'token'    => $this->token,
	'wallid'   => $this->info['id'],
	'is_scene' => '1',
	'is_check' => 1,
	'type'     => array('neq', 'event')
	))->count());
			$this->assign('join_title', '条消息');
		}
		else {
			if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'red_packet')) || (ACTION_NAME == 'red_packet')) {
				$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
				$this->assign('join_title', '人摇红包');
			}
			else {
				if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'jiugongge')) || (ACTION_NAME == 'jiugongge')) {
					$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['jiugongge_id']))->select();
					$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
					$this->assign('join_title', '人抽奖');
				}
				else {
					if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'zajindan')) || (ACTION_NAME == 'zajindan')) {
						$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['zajindan_id']))->select();
						$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
						$this->assign('join_title', '人砸金蛋');
					}
					else {
						if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'shuiguoji')) || (ACTION_NAME == 'shuiguoji')) {
							$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['shuiguoji_id']))->select();
							$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
							$this->assign('join_title', '人抽奖');
						}
						else {
							if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'guaguaka')) || (ACTION_NAME == 'guaguaka')) {
								$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['guaguaka_id']))->select();
								$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
								$this->assign('join_title', '人刮卡');
							}
							else {
								if (((ACTION_NAME == 'hudong') && ($_GET['type'] == 'dazhuanpan')) || (ACTION_NAME == 'dazhuanpan')) {
									$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['dazhuanpan_id']))->select();
									$this->assign('join_count', M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count());
									$this->assign('join_title', '人抽奖');
								}
								else {
									if ((ACTION_NAME == 'vote') || (ACTION_NAME == 'vote_countdown')) {
										$player = M('vote_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'vid' => $this->info['vote_id']))->select();
										$this->assign('join_count', count($player));
										$this->assign('join_title', '人投票');
									}
									else {
										if ((ACTION_NAME == 'shake_active') || (ACTION_NAME == 'shake')) {
											$player = M('shake_rt')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'shakeid' => $this->info['shake_id']))->select();
											$this->assign('join_count', count($player));
											$this->assign('join_title', '人摇一摇');
										}
										else if (ACTION_NAME == 'signin') {
											$this->assign('join_count', $this->join_count());
											$this->assign('join_title', '人签到');
										}
										else {
											$this->assign('join_count', $this->join_count());
											$this->assign('join_title', '人参与');
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function index()
	{
		$where = array('token' => $this->token);
		$count = $this->scene_db->where($where)->count();
		$Page = new Page($count, 15);
		$scene_list = $this->scene_db->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('scene_list', $scene_list);
		$this->assign('page', $Page->show());
		$this->display();
	}

	public function luckrecord()
	{
		if ($_GET['type'] == 'red_packet') {
			$token = $this->_get('token', 'trim');
			$action_id = $this->scene_db->where(array('token' => $token, 'id' => $this->_get('id', 'intval')))->getField('red_packet_id');
			$prize_type = ($_GET['prize_type'] ? (int) $_GET['prize_type'] : 1);
			$where = array('token' => $token, 'aid' => $action_id);
			$search_word = $this->_post('search_word', 'trim');
			if (($this->_get('type2') == 'win') || ($this->_get('type2') == 'hongbao') || ($this->_get('type2') == '')) {
				$where['iswin'] = 1;
				$where['prize_type'] = $prize_type;
			}
			else {
				$where['iswin'] = array('neq', 1);
			}

			if (!empty($search_word)) {
				$where['wecha_name'] = array('like', '%' . $search_word . '%');
			}

			$total = M('shakelottery_record')->where($where)->count();
			$Page = new Page($total, 10);
			$list = M('shakelottery_record')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('shaketime desc')->select();
			$this->assign('list', $list);
			$this->assign('token', $token);
			$this->assign('id', $action_id);
			$this->assign('page', $Page->show());
			$this->assign('firstRow', $Page->firstRow);
			$this->assign('search_word', $search_word);
			$this->assign('type', $this->_get('type2'));
			$this->assign('prize_type', $prize_type);
			$this->assign('winxintype', $this->wxuser['winxintype']);
		}
		else {
			switch ($_GET['type']) {
			case 'dazhuanpan':
				$type = 1;
				$id = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('dazhuanpan_id');
				break;

			case 'guaguaka':
				$type = 2;
				$id = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('guaguaka_id');
				break;

			case 'jiugongge':
				$type = 10;
				$id = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('jiugongge_id');
				break;

			case 'shuiguoji':
				$type = 4;
				$id = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('shuiguoji_id');
				break;

			case 'zajindan':
				$type = 5;
				$id = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->getField('zajindan_id');
				break;
			}

			if ($id) {
				$Lottery_record_db = M('Lottery_record');
				$data = M('Lottery')->where(array('token' => $this->token, 'id' => $id, 'type' => $type))->find();
				$this->assign('thisLottery', $data);
				$recordWhere = 'token="' . $this->token . '" and islottery=1 and lid=' . $id;
				$count = $Lottery_record_db->where($recordWhere)->count();
				$page = new Page($count, 20);
				$this->assign('page', $page->show());
				$record_list = $Lottery_record_db->where($recordWhere)->order('time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				$record_jilu = array();
				$phone = array();
				$userinfo_db = M('userinfo');

				foreach ($record_list as $k => $v) {
					$record[$k] = $v;

					if ($v['phone'] == '') {
						$where_userinfo['wecha_id'] = $v['wecha_id'];
						$phone[$v['id']] = $userinfo_db->where($where_userinfo)->getField('tel');
					}
				}

				$recordcount = $data['fistlucknums'] + $data['secondlucknums'] + $data['thirdlucknums'] + $data['fourlucknums'] + $data['fivelucknums'] + $data['sixlucknums'];
				$datacount = $data['fistnums'] + $data['secondnums'] + $data['thirdnums'] + $data['fournums'] + $data['fivenums'] + $data['sixnums'];
				$sendCount = $Lottery_record_db->where(array('lid' => $id, 'sendstutas' => 1, 'islottery' => 1))->count();
				$this->assign('sendCount', $sendCount);
				$this->assign('datacount', $datacount);
				$this->assign('recordcount', $recordcount);
				$this->assign('phone', $phone);

				if ($record) {
					$i = 0;

					foreach ($record as $r) {
						switch (intval($r['prizetype'])) {
						default:
							$record[$i]['prizeName'] = $r['prize'];
							break;

						case 1:
							$record[$i]['prizeName'] = $data['fist'];
							break;

						case 2:
							$record[$i]['prizeName'] = $data['second'];
							break;

						case 3:
							$record[$i]['prizeName'] = $data['third'];
							break;

						case 4:
							$record[$i]['prizeName'] = $data['four'];
							break;

						case 5:
							$record[$i]['prizeName'] = $data['five'];
							break;

						case 6:
							$record[$i]['prizeName'] = $data['six'];
							break;

						case 7:
							$activeType = 'AppleGame';
							break;

						case 8:
							$activeType = 'Lovers';
							break;

						case 9:
							$activeType = 'Autumn';
							break;

						case 10:
							$activeType = 'Jiugong';
							break;
						}

						$i++;
					}
				}

				$this->assign('record', $record);
			}
		}

		$this->display();
	}

	public function set()
	{
		$keyword_db = M('keyword');
		$scene_info = $this->scene_db->where(array('token' => $this->token, 'id' => $this->_get('id', 'intval')))->find();

		if (IS_POST) {
			$isotheropen = $this->scene_db->where(array('token' => $this->token, 'is_open' => '1'))->find();
			if ($isotheropen && ($_POST['is_open'] == 1) && ($_POST['id'] != $isotheropen['id'])) {
				$this->error('有其他现场活动已开启，该活动不能开启！');
				exit();
			}

			if ($this->scene_db->create($_POST)) {
				if ($scene_info) {
					$id = $this->_post('id', 'intval');

					if ($this->scene_db->where(array('token' => $this->token, 'id' => $id))->save($_POST)) {
						if ($this->_post('wall_id', 'intval')) {
							$this->scene_db->where(array(
	'token' => $this->token,
	'id'    => array('neq', $id)
	))->save(array('is_open' => '0'));
							M('Wall')->where(array('token' => $this->token))->save(array('isopen' => '0'));
							M('Wall')->where(array('token' => $this->token, 'id' => $this->_post('wall_id', 'intval')))->save(array('isopen' => '1'));
						}
					}

					if (empty($_POST['is_open'])) {
						$results = M('WallMember')->where(array('token' => $this->token, 'act_type' => '3', 'act_id' => $id))->select();

						foreach ($results as $result) {
							$ids[] = $result['wecha_id'];
						}

						if ($ids) {
							M('Userinfo')->where(array(
	'token'    => $this->token,
	'wecha_id' => array('IN', $ids)
	))->setField('wallopen', '0');
						}
					}

					$keyword['pid'] = $id;
					$keyword['module'] = 'Scene';
					$keyword['token'] = $this->token;
					$keyword['keyword'] = $this->_post('keyword', 'trim');
					$keyword_db->where(array('token' => $this->token, 'pid' => $id, 'module' => 'Scene'))->save($keyword);
					$this->success('修改成功', U('Scene/index', array('token' => $this->token)));
				}
				else {
					$_POST['token'] = $this->token;
					$id = $this->scene_db->add($_POST);
					$keyword['pid'] = $id;
					$keyword['module'] = 'Scene';
					$keyword['token'] = $this->token;
					$keyword['keyword'] = $this->_post('keyword', 'trim');
					$keyword_db->add($keyword);
					$this->success('添加现场成功', U('Scene/index', array('token' => $this->token)));
				}
			}
			else {
				$this->error($this->scene_db->getError());
			}
		}
		else {
			$wall = M('Wall')->where(array('token' => $this->token))->select();
			$shake = M('Shake')->where(array('token' => $this->token, 'isopen' => 1))->select();
			$vote = M('Vote')->where(array('token' => $this->token, 'type' => 'scene', 'status' => '1'))->order('id DESC')->select();
			$red_packet = M('Shakelottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1')))->order('id DESC')->select();
			$jiugongge = M('Lottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1', 'type' => '10')))->order('id DESC')->select();
			$zajindan = M('Lottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1', 'type' => '5')))->order('id DESC')->select();
			$shuiguoji = M('Lottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1', 'type' => '4')))->order('id DESC')->select();
			$guaguaka = M('Lottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1', 'type' => '2')))->order('id DESC')->select();
			$dazhuanpan = M('Lottery')->where($this->other_source_condition(array('token' => $this->token, 'status' => '1', 'type' => '1')))->order('id DESC')->select();

			foreach ($red_packet as $rk => $rv) {
				$thisuse = M('wechat_scene')->where(array('token' => $this->token, 'red_packet_id' => $rv['id']))->find();
				if (($thisuse['id'] != $scene_info['id']) && ($thisuse != '')) {
					unset($red_packet[$rk]);
				}
			}

			foreach ($vote as $vk => $vv) {
				$thisuse2 = M('wechat_scene')->where(array('token' => $this->token, 'vote_id' => $vv['id']))->find();
				if (($thisuse2['id'] != $scene_info['id']) && ($thisuse2 != '')) {
					unset($vote[$vk]);
				}
			}

			foreach ($jiugongge as $jk => $jv) {
				$thisuse3 = M('wechat_scene')->where(array('token' => $this->token, 'jiugongge_id' => $jv['id']))->find();
				if (($thisuse3['id'] != $scene_info['id']) && ($thisuse3 != '')) {
					unset($jiugongge[$jk]);
				}
			}

			foreach ($zajindan as $zk => $zv) {
				$thisuse4 = M('wechat_scene')->where(array('token' => $this->token, 'zajindan_id' => $zv['id']))->find();
				if (($thisuse4['id'] != $scene_info['id']) && ($thisuse4 != '')) {
					unset($zajindan[$zk]);
				}
			}

			foreach ($shuiguoji as $sk => $sv) {
				$thisuse5 = M('wechat_scene')->where(array('token' => $this->token, 'shuiguoji_id' => $sv['id']))->find();
				if (($thisuse5['id'] != $scene_info['id']) && ($thisuse5 != '')) {
					unset($shuiguoji[$sk]);
				}
			}

			foreach ($guaguaka as $gk => $gv) {
				$thisuse6 = M('wechat_scene')->where(array('token' => $this->token, 'guaguaka_id' => $gv['id']))->find();
				if (($thisuse6['id'] != $scene_info['id']) && ($thisuse6 != '')) {
					unset($guaguaka[$gk]);
				}
			}

			foreach ($dazhuanpan as $dk => $dv) {
				$thisuse7 = M('wechat_scene')->where(array('token' => $this->token, 'dazhuanpan_id' => $dv['id']))->find();
				if (($thisuse7['id'] != $scene_info['id']) && ($thisuse7 != '')) {
					unset($dazhuanpan[$dk]);
				}
			}

			$this->assign('vote', $vote);
			$this->assign('id', 6);
			$this->assign('info', $scene_info);
			$this->assign('wall', $wall);
			$this->assign('shake', $shake);
			$this->assign('jiugongge', $jiugongge);
			$this->assign('zajindan', $zajindan);
			$this->assign('shuiguoji', $shuiguoji);
			$this->assign('guaguaka', $guaguaka);
			$this->assign('dazhuanpan', $dazhuanpan);
			$this->assign('red_packet', $red_packet);
			$this->display();
		}
	}

	public function del()
	{
		$id = $this->_get('id', 'intval');
		$where = array('token' => $this->token, 'id' => $id);

		if ($this->scene_db->where($where)->delete()) {
			M('keyword')->where(array('pid' => $id, 'token' => $this->token, 'module' => 'Scene'))->delete();
			M('Wall_member')->where(array('act_id' => $id, 'act_type' => '3', 'token' => $this->token))->delete();
			M('wall_supperzzle')->where(array('sceneid' => $id, 'token' => $this->token))->delete();
			M('wall_prize')->where(array('sceneid' => $id, 'token' => $this->token))->delete();
			M('wall_prize_record')->where(array('sceneid' => $id))->delete();
			$this->success('删除成功', U('Scene/index', array('token' => $this->token)));
		}
	}

	public function vote_add()
	{
		$where = array('token' => $this->token, 'type' => 'scene');
		$count = M('Vote')->where($where)->count();
		$Page = new Page($count, 5);
		$vote = M('Vote')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('page', $Page->show());
		$this->assign('vote_list', $vote);
		$this->display();
	}

	public function wall()
	{
		$sceneid = $this->_get('id', 'intval');
		$where = array('token' => $this->token, 'wallid' => $this->info['id']);
		$this->assign('sceneid', $sceneid);
		$this->display();
	}

	public function wall_pic()
	{
		$sceneid = $this->_get('sceneid', 'intval');
		$where = array('token' => $this->token, 'wallid' => $this->info['id']);
		$message = $this->_getWallList($where, 'check_time desc,time desc', 5, $sceneid, 'pic');
		$this->assign('sceneid', $sceneid);
		$this->assign('message', $message);
		$this->display();
	}

	public function ajaxWall()
	{
		$sceneid = $this->_get('sceneid', 'intval');
		$where = array(
			'token'      => $this->token,
			'wallid'     => $this->info['id'],
			'check_time' => array('gt', intval($_GET['ajax_time'])),
			'id'         => array('gt', $this->_get('lastid'))
			);
		$data = $this->_getWallList($where, 'check_time asc,time asc', '', $sceneid, 'msg');
		$result = array();

		if ($data) {
			$result['err'] = 0;
			$result['res'] = $data;
		}

		echo json_encode($result);
	}

	public function ajaxWallPic()
	{
		$data = array();
		$sceneid = $this->_get('sceneid', 'intval');
		$where = array(
			'token'      => $this->token,
			'wallid'     => $this->_get('id', 'intval'),
			'check_time' => array('gt', intval($_GET['ajax_time']))
			);
		$data = $this->_getWallList($where, 'check_time asc,time asc', '', $sceneid, 'pic');
		$result = array();

		foreach ($data as $key => $value) {
			$data[$key]['picture'] = 'index.php?g=User&m=Wechat_group&a=showExternalPic&url=' . $value['picture'] . '&wecha_id=' . $value['wecha_id'] . $value['id'];
		}

		if ($data) {
			$result['err'] = 0;
			$result['res'] = $data;
		}

		echo json_encode($result);
	}

	public function active_start()
	{
		$id = $this->_get('id', 'intval');
		$type = $this->_get('type');

		switch ($type) {
		case 'vote':
			$cache['name'] = '投票';
			$cache['type'] = $type;
			$vote = M('vote')->where(array('token' => $this->token, 'id' => $this->info['vote_id'], 'scene_state' => 0))->find();

			if ($vote) {
				$vote_save = array('scene_state' => 1, 'startdate' => time() + 5, 'enddate' => time() + $vote['scene_time']);
				M('vote')->where(array('token' => $this->token, 'id' => $this->info['vote_id']))->save($vote_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'red_packet':
			$cache['name'] = '摇一摇红包';
			$cache['type'] = $type;
			$red_packet = M('shakelottery')->where(array('token' => $this->token, 'id' => $this->info['red_packet_id'], 'scene_state' => 0))->find();

			if ($red_packet) {
				$red_packet_save = array('scene_state' => 1, 'starttime' => time() + 5, 'endtime' => time() + $red_packet['scene_time']);
				M('shakelottery')->where(array('token' => $this->token, 'id' => $this->info['red_packet_id']))->save($red_packet_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'jiugongge':
			$cache['name'] = '九宫格';
			$cache['type'] = $type;
			$jiugongge = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['jiugongge_id'], 'scene_state' => 0))->find();

			if ($jiugongge) {
				$jiugongge_save = array('scene_state' => 1, 'statdate' => time() + 5, 'enddate' => time() + $jiugongge['scene_time']);
				M('lottery')->where(array('token' => $this->token, 'id' => $this->info['jiugongge_id']))->save($jiugongge_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'zajindan':
			$cache['name'] = '砸金蛋';
			$cache['type'] = $type;
			$zajindan = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['zajindan_id'], 'scene_state' => 0))->find();

			if ($zajindan) {
				$zajindan_save = array('scene_state' => 1, 'statdate' => time() + 5, 'enddate' => time() + $zajindan['scene_time']);
				M('lottery')->where(array('token' => $this->token, 'id' => $this->info['zajindan_id']))->save($zajindan_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'shuiguoji':
			$cache['name'] = '水果机';
			$cache['type'] = $type;
			$shuiguoji = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['shuiguoji_id'], 'scene_state' => 0))->find();

			if ($shuiguoji) {
				$shuiguoji_save = array('scene_state' => 1, 'statdate' => time() + 5, 'enddate' => time() + $shuiguoji['scene_time']);
				M('lottery')->where(array('token' => $this->token, 'id' => $this->info['shuiguoji_id']))->save($shuiguoji_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'guaguaka':
			$cache['name'] = '刮刮卡';
			$cache['type'] = $type;
			$guaguaka = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['guaguaka_id'], 'scene_state' => 0))->find();

			if ($guaguaka) {
				$guaguaka_save = array('scene_state' => 1, 'statdate' => time() + 5, 'enddate' => time() + $guaguaka['scene_time']);
				M('lottery')->where(array('token' => $this->token, 'id' => $this->info['guaguaka_id']))->save($guaguaka_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		case 'dazhuanpan':
			$cache['name'] = '大转盘';
			$cache['type'] = $type;
			$dazhuanpan = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['dazhuanpan_id'], 'scene_state' => 0))->find();

			if ($dazhuanpan) {
				$dazhuanpan_save = array('scene_state' => 1, 'statdate' => time() + 5, 'enddate' => time() + $dazhuanpan['scene_time']);
				M('lottery')->where(array('token' => $this->token, 'id' => $this->info['dazhuanpan_id']))->save($dazhuanpan_save);
				$data['error'] = 0;
			}
			else {
				$data['error'] = 1;
			}

			$this->ajaxReturn($data, 'JSON');
			break;

		default:
			break;
		}

		S('scene_' . $this->token . '_' . $id, $cache);
	}

	public function hudong()
	{
		$id = $this->_get('id', 'intval');
		$type = $this->_get('type');
		$where['active.token'] = $this->token;
		$where['active.scene_id'] = $id;
		$where['user.act_type'] = '3';
		$where['active.type'] = $type;

		switch ($type) {
		case 'red_packet':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('red_packet_id');
			$red_packet = M('shakelottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($red_packet['endtime'] - time()) ? $red_packet['endtime'] - time() : 0);
			$this->assign('bg', $this->info['hongbao_background']);
			break;

		case 'jiugongge':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('jiugongge_id');
			$jiugongge = M('lottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($jiugongge['enddate'] - time()) ? $jiugongge['enddate'] - time() : 0);
			$this->assign('bg', $this->info['jiugongge_background']);
			break;

		case 'zajindan':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('zajindan_id');
			$zajindan = M('lottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($zajindan['enddate'] - time()) ? $zajindan['enddate'] - time() : 0);
			$this->assign('bg', $this->info['goldenegg_background']);
			break;

		case 'shuiguoji':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('shuiguoji_id');
			$shuiguoji = M('lottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($shuiguoji['enddate'] - time()) ? $shuiguoji['enddate'] - time() : 0);
			$this->assign('bg', $this->info['luckyfruit_background']);
			break;

		case 'guaguaka':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('guaguaka_id');
			$guaguaka = M('lottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($guaguaka['enddate'] - time()) ? $guaguaka['enddate'] - time() : 0);
			$this->assign('bg', $this->info['guajiang_background']);
			break;

		case 'dazhuanpan':
			$where['active.active_id'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('dazhuanpan_id');
			$dazhuanpan = M('lottery')->where(array('token' => $this->token, 'id' => $where['active.active_id']))->find();
			$this->assign('countdown', 0 < ($dazhuanpan['enddate'] - time()) ? $dazhuanpan['enddate'] - time() : 0);
			$this->assign('bg', $this->info['lottery_background']);
			break;
		}

		if (IS_AJAX) {
			$where['active.id'] = array('gt', $this->_post('last_id', 'intval'));
			$result = M('SceneActive')->alias('active')->field('active.*, user.nickname, user.portrait, user.truename')->join('LEFT JOIN ' . C('DB_PREFIX') . 'wall_member AS user ON active.wecha_id = user.wecha_id AND active.scene_id = user.act_id')->where($where)->order('active.id ASC')->find();
			$data['html'] = "<li>\r\n                                <div class=\"avatar\">\r\n                                    <img src=\"" . $result['portrait'] . "\" alt=\"avatar\"/>\r\n                                </div>\r\n                                <div class=\"name\">\r\n                                    <h2>" . $result['nickname'] . "</h2>\r\n                                </div>\r\n                            </li>";
			$data['id'] = $result['id'];
			exit(json_encode($data));
		}
		else {
			$results = M('SceneActive')->alias('active')->field('active.*, user.nickname, user.portrait, user.truename')->join('LEFT JOIN ' . C('DB_PREFIX') . 'wall_member AS user ON active.wecha_id = user.wecha_id AND active.scene_id = user.act_id')->where($where)->order('active.id DESC')->select();
			$this->assign('data', $results);
			$this->display();
		}
	}

	public function prize_ajax()
	{
		$id = $this->_get('id', 'intval');
		$type = $this->_get('type');
		$last_id = $this->_post('last_id', 'intval');

		switch ($type) {
		case 'red_packet':
			$where['token'] = $this->token;
			$where['aid'] = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('red_packet_id');
			$where['id'] = array('gt', $last_id);
			$where['iswin'] = '1';
			$result = M('ShakelotteryRecord')->where($where)->order('id ASC')->find();
			$wecha_id = M('ShakelotteryUsers')->where(array('id' => $result['user_id'], 'token' => $this->token))->getField('wecha_id');

			if (empty($result['prize_type'])) {
				$name = M('ShakelotteryHbrecord')->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'aid' => $result['id']))->getField('money');
				$name .= ' 元';
			}
			else {
				$name = $result['prizename'];
			}

			break;

		case 'jiugongge':
			$active_id = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('jiugongge_id');
			$result = M('LotteryRecord')->where(array(
	'id'        => array('gt', $last_id),
	'lid'       => $active_id,
	'token'     => $this->token,
	'islottery' => '1'
	))->order('id ASC')->find();
			$lottery = M('lottery')->where(array('id' => $active_id, 'token' => $this->token))->find();
			$lottery_name = array(1 => $lottery['fist'], 2 => $lottery['second'], 3 => $lottery['third'], 4 => $lottery['four'], 5 => $lottery['five'], 6 => $lottery['six']);
			$wecha_id = $result['wecha_id'];
			$name = $result['prize'] . '等奖';
			break;

		case 'zajindan':
			$active_id = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('zajindan_id');
			$result = M('LotteryRecord')->where(array(
	'id'        => array('gt', $last_id),
	'lid'       => $active_id,
	'token'     => $this->token,
	'islottery' => '1'
	))->order('id ASC')->find();
			$wecha_id = $result['wecha_id'];
			$name = $result['prize'] . '等奖';
			break;

		case 'shuiguoji':
			$active_id = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('shuiguoji_id');
			$result = M('LotteryRecord')->where(array(
	'id'        => array('gt', $last_id),
	'lid'       => $active_id,
	'token'     => $this->token,
	'islottery' => '1'
	))->order('id ASC')->find();
			$wecha_id = $result['wecha_id'];
			$name = $result['prize'] . '等奖';
			break;

		case 'guaguaka':
			$active_id = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('guaguaka_id');
			$result = M('LotteryRecord')->where(array(
	'id'        => array('gt', $last_id),
	'lid'       => $active_id,
	'token'     => $this->token,
	'islottery' => '1'
	))->order('id ASC')->find();
			$wecha_id = $result['wecha_id'];
			$name = $result['prize'] . '等奖';
			break;

		case 'dazhuanpan':
			$active_id = M('WechatScene')->where(array('id' => $id, 'token' => $this->token))->getField('dazhuanpan_id');
			$result = M('LotteryRecord')->where(array(
	'id'        => array('gt', $last_id),
	'lid'       => $active_id,
	'token'     => $this->token,
	'islottery' => '1'
	))->order('id ASC')->find();
			$wecha_id = $result['wecha_id'];
			$name = $result['prize'] . '等奖';
			break;
		}

		$data['name'] = M('WallMember')->where(array('token' => $this->token, 'act_id' => $id, 'act_type' => '3', 'wecha_id' => $wecha_id))->getField('nickname');
		$data['portrait'] = M('WallMember')->where(array('token' => $this->token, 'act_id' => $id, 'act_type' => '3', 'wecha_id' => $wecha_id))->getField('portrait');
		$info['id'] = $result['id'];
		$info['html'] = '<li class="clearfix t-row"><div class="fl"><div class="avatar"><img src="' . $data['portrait'] . '" alt="avatar"></div><div class="name"> <h3>' . $data['name'] . '</h3></div></div><em class="fr">' . $name . '</em></li>';
		exit(json_encode($info));
	}

	public function red_packet()
	{
		$id = $this->_get('id', 'intval');
		$cache = S('scene_' . $this->token . '_' . $id);
		$red_packet = M('shakelottery')->where(array('token' => $this->token, 'id' => $this->info['red_packet_id']))->find();

		if ($red_packet['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'red_packet'));
		}

		$this->display();
	}

	public function jiugongge()
	{
		$id = $this->_get('id', 'intval');
		$zajindan = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['jiugongge_id']))->find();

		if ($zajindan['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'jiugongge'));
		}

		$this->display();
	}

	public function zajindan()
	{
		$id = $this->_get('id', 'intval');
		$zajindan = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['zajindan_id']))->find();

		if ($zajindan['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'zajindan'));
		}

		$this->display();
	}

	public function shuiguoji()
	{
		$id = $this->_get('id', 'intval');
		$shuiguoji = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['shuiguoji_id']))->find();

		if ($shuiguoji['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'shuiguoji'));
		}

		$this->display();
	}

	public function guaguaka()
	{
		$id = $this->_get('id', 'intval');
		$guaguaka = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['guaguaka_id']))->find();

		if ($guaguaka['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'guaguaka'));
		}

		$this->display();
	}

	public function dazhuanpan()
	{
		$id = $this->_get('id', 'intval');
		$dazhuanpan = M('lottery')->where(array('token' => $this->token, 'id' => $this->info['dazhuanpan_id']))->find();

		if ($dazhuanpan['scene_state'] == 1) {
			$this->redirect('User/Scene/hudong', array('token' => $this->token, 'id' => $id, 'type' => 'dazhuanpan'));
		}

		$this->display();
	}

	public function _getWallList($where, $order, $limit = '', $id = '', $type = '')
	{
		$where['is_check'] = '1';
		$where['is_scene'] = '1';
		$where['type'] = array('IN', 'text,image');
		$data = M('Wall_message')->where($where)->order($order)->find();

		if (empty($data)) {
			unset($where['id']);
			$data = M('Wall_message')->where($where)->order($order)->find();
		}

		if (empty($data) || ($data['id'] == $this->_get('lastid'))) {
			return false;
		}

		$wuser_db = M('Wall_member');
		$message['id'] = $data['id'];
		$message['fakeid'] = $data['wecha_id'];
		$message['num'] = $data['check_time'];
		$message['content'] = $data['type'] == 'text' ? $data['content'] : '';
		$message['nickname'] = $wuser_db->where(array('id' => $data['uid']))->getField('nickname');
		$message['avatar'] = $wuser_db->where(array('id' => $data['uid']))->getField('portrait');
		$message['image'] = $data['type'] == 'image' ? 'index.php?g=User&m=Wechat_group&a=showExternalPic&url=' . $data['content'] . '&wecha_id=' . $data['wecha_id'] . $data['id'] : '';
		$message['fromtype'] = 'weixin';
		return $message;
	}

	public function shake()
	{
		$id = $this->_get('id', 'intval');
		$sceneid = $this->_post('sceneid', 'intval');

		if ($sceneid) {
			$is_scene = '1';
		}
		else {
			$is_scene = '0';
		}

		$round = M('Shake_rt')->where(array('token' => $this->token, 'shakeid' => $id, 'is_scene' => $is_scene))->max('round');
		$this->assign('round', $round + 1);
		$this->display();
	}

	public function startShake()
	{
		$id = $this->_get('id', 'intval');
		$result = M('Shake')->where(array('token' => $this->token, 'id' => $this->_get('shake_id', 'intval')))->save(array('isact' => 1, 'isopen' => 1, 'endtime' => time()));

		if (!$result) {
			$result = M('Shake')->where(array('token' => $this->token, 'id' => $this->_get('shake_id', 'intval')))->save(array('isact' => 1, 'isopen' => 1, 'endtime' => time()));
		}

		$qingling = M('shake_rt')->where(array('token' => $this->token, 'shakeid' => $this->_get('shake_id', 'intval'), 'round' => 0, 'is_scene' => 1))->save(array('count' => 0));

		if ($result) {
			$result = array('error' => 0);
		}
		else {
			$result = array('error' => 1, 'info' => '游戏意外中断，请重新开始');
		}

		echo json_encode($result);
	}

	public function getConnectNum()
	{
		$sceneid = $id = $this->_post('id', 'intval');

		if (in_array($_GET['this_a'], array('signin', 'lottery', 'supperzzle'))) {
			$where = array('member.token' => $this->token);

			if ($sceneid) {
				$where['member.act_type'] = '3';
				$where['member.act_id'] = $sceneid;
				$where['userinfo.wallopen'] = '1';
				$count = M('Wall_member')->alias('member')->join(C('DB_PREFIX') . "userinfo AS userinfo\r\n\t\t\t\t\tON userinfo.token = member.token\r\n\t\t\t\t\tAND userinfo.wecha_id = member.wecha_id\r\n\t\t\t\t")->group('userinfo.wecha_id')->where($where)->select();
			}
			else {
				$where['member.act_type'] = '2';
				$where['member.act_id'] = $id;
				$count = M('Wall_member')->alias('member')->where($where)->group('userinfo.wecha_id')->select();
			}

			$data['totalname'] = '人参与';
			$data['count'] = count($count);
		}

		$this->ajaxReturn($data, 'JSON');
	}

	public function getCount()
	{
		$result = M('Shake_rt')->where(array('token' => $this->_get('token'), 'shakeid' => intval($this->_get('id', 'intval'))))->limit(0, 80)->order('count desc')->select();
		$js = json_encode($result);
		echo $js;
	}

	public function shake_active()
	{
		$result = M('Shake')->where(array('token' => $this->token, 'id' => $this->info['shake_id']))->find();
		$this->assign('shake', $result);
		$this->display();
	}

	public function shakeRun()
	{
		$id = $this->_get('shake_id', 'intval');
		$sceneid = $this->_get('id', 'intval');
		$shake_db = M('Shake');
		$rt_db = M('Shake_rt');
		$member_db = M('Wall_member');

		if ($sceneid) {
			$is_scene = '1';
			$act_type = '3';
			$act_id = $sceneid;
		}
		else {
			$is_scene = '0';
			$act_type = '2';
			$act_id = $id;
		}

		$shake_info = $shake_db->where(array('id' => $id, 'token' => $this->token, 'isact' => 1, 'isopen' => 1))->find();

		if ($shake_info == '') {
			$shake_info = $shake_db->where(array('id' => $id, 'token' => $this->token, 'isact' => 1, 'isopen' => 1))->find();
		}

		$is_end = $rt_db->where(array(
	'is_scene' => $is_scene,
	'token'    => $this->token,
	'shakeid'  => $id,
	'round'    => 0,
	'count'    => array('egt', $shake_info['endshake'])
	))->find();
		$result = array();

		if (($shake_info['endtime'] + $shake_info['usetime'] + $shake_info['starttime'] + $shake_info['clienttime']) < time()) {
			$result['status'] = 1;
			$result['info'] = '游戏已经结束';
			M('Shake')->where(array('token' => $this->token, 'id' => $id))->save(array('isact' => 0));
			$user = $rt_db->where(array('shakeid' => $id, 'token' => $this->token, 'round' => 0, 'is_scene' => $is_scene))->order('count desc,id desc')->limit(3)->select();

			foreach ($user as $key => $value) {
				$uwhere = array('wecha_id' => $value['wecha_id'], 'act_id' => $act_id, 'act_type' => $act_type);
				$u_info = $member_db->where($uwhere)->find();
				$user[$key]['name'] = $u_info['nickname'];
				$user[$key]['portrait'] = $u_info['portrait'];
				$user[$key]['mLeft'] = $this->percent($value['count'], $shake_info['endshake']);
			}

			$result['res'] = $user;
			$max_round = $rt_db->where(array('token' => $this->token, 'shakeid' => $id, 'is_scene' => $is_scene))->max('round');
			$rt_db->where(array('token' => $this->token, 'shakeid' => $id, 'round' => 0, 'is_scene' => $is_scene))->save(array('round' => $max_round + 1));
		}
		else {
			$user = $rt_db->where(array('token' => $this->token, 'shakeid' => $id, 'round' => 0, 'is_scene' => $is_scene))->order('count desc,id desc')->limit($shake_info['shownum'])->select();

			foreach ($user as $key => $value) {
				$uwhere = array('wecha_id' => $value['wecha_id'], 'act_id' => $act_id, 'act_type' => $act_type);
				$u_info = $member_db->where($uwhere)->find();
				$user[$key]['name'] = $u_info['nickname'];
				$user[$key]['portrait'] = $u_info['portrait'];
				$user[$key]['mLeft'] = $this->percent($value['count'], $shake_info['endshake']);
			}

			$result['status'] = 0;
			$result['res'] = $user;
		}

		echo json_encode($result);
	}

	public function show_shake()
	{
		$id = $this->_get('shake_id', 'intval');
		$sceneid = $this->_get('id', 'intval');
		$round = $this->_get('round', 'intval');

		if ($sceneid) {
			$is_scene = '1';
		}
		else {
			$is_scene = '0';
		}

		$data = M('Shake_rt')->where(array(
	'is_scene' => $is_scene,
	'token'    => $this->token,
	'shakeid'  => $id,
	'round'    => array('neq', 0)
	))->order('round ASC, count DESC')->select();

		foreach ($data as $key => $value) {
			$where = array('token' => $this->token, 'wecha_id' => $value['wecha_id']);

			if ($sceneid) {
				$where['act_type'] = 3;
				$where['act_id'] = $sceneid;
			}
			else {
				$where['act_type'] = 2;
				$where['act_id'] = $id;
			}

			$data[$key]['name'] = M('Wall_member')->where($where)->getField('nickname');
			$data[$key]['head'] = M('Wall_member')->where($where)->getField('portrait');
			$info[$value['round']][] = $data[$key];
		}

		$this->assign('id', $id);
		$this->assign('round', $round);
		$this->assign('data', $info);
		$this->display();
	}

	public function show_fans()
	{
		$id = $this->_get('id', 'intval');
		$keyword = $this->_post('keyword', 'trim');
		$where = array('token' => $this->token, 'act_id' => $id, 'act_type' => '3');

		if (!empty($keyword)) {
			$where['nickname|truename'] = array('like', '%' . $keyword . '%');
		}

		$count = M('Wall_member')->where($where)->count();
		$Page = new Page($count, 15);
		$list = M('Wall_member')->where($where)->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$scene = M('Wechat_scene')->where(array('token' => $this->token, 'id' => $id))->field('wall_id,shake_id,vote_id')->find();
		$this->assign('sceneid', $id);
		$this->assign('scene', $scene);
		$this->assign('page', $Page->show());
		$this->assign('list', $list);
		$this->display();
	}

	public function del_fens()
	{
		$id = $this->_get('id', 'intval');
		$sceneid = $this->_get('sceneid', 'intval');
		$where = array('token' => $this->token, 'id' => $id, 'act_type' => '3');
		$wecha_id = M('Wall_member')->where($where)->getField('wecha_id');
		$scene_info = M('Wechat_scene')->where(array('token' => $this->token, 'sceneid' => $sceneid))->find();

		if (M('Wall_member')->where($where)->delete()) {
			M('Shake_rt')->where(array('token' => $this->token, 'is_scene' => '1', 'wecha_id' => $wecha_id))->delete();
			M('Wall_message')->where(array('token' => $this->token, 'is_scene' => '1', 'wallid' => $scene_info['id'], 'uid' => $id))->delete();
			M('wall_prize_record')->where(array('token' => $this->token, 'sceneid' => $sceneid, 'uid' => $id))->delete();
			$this->success('删除成功', U('Scene/show_fans', array('token' => $this->token, 'id' => $sceneid)));
		}
	}

	public function percent($p, $t, $offset = 0)
	{
		if ($t == 0) {
			$val = 1;
		}
		else {
			$val = $p / $t;
		}

		$num = sprintf('%.2f%%', ($val + $offset) * 100);
		return $num;
	}

	public function lottery()
	{
		$id = intval($this->_get('id'));
		$prize = M('Wall_prize')->where(array('token' => $this->token, 'sceneid' => $id))->order('sort desc,id asc')->select();
		$users = M('Wall_member')->where(array('token' => $this->token, 'act_type' => '3', 'act_id' => $id))->count();
		$record_count = M('wall_prize_record')->where(array('sceneid' => $id))->count();
		$prize_users = $this->prize_users($id, $prize[0]['id']);
		$this->assign('users', $users - $record_count);
		$this->assign('prize', $prize);
		$this->assign('prize_num', $prize_users['prize_num']);
		unset($prize_users['prize_num']);
		$this->assign('prize_users', $prize_users);
		$this->display();
	}

	public function lottery_prize()
	{
		$prize = M('Wall_prize')->where(array('token' => $this->token, 'sceneid' => $this->_get('id', 'intval'), 'id' => $this->_post('prize_id', 'intval')))->order('sort desc,id asc')->find();
		exit(json_encode($prize));
	}

	private function prize_users($id, $pid)
	{
		$pid = intval($pid);
		$id = intval($id);
		$prize_user = M('Wall_prize_record')->where(array('sceneid' => $id, 'prize' => $pid))->order('id desc')->select();

		foreach ($prize_user as $key => $value) {
			$user_info = M('Wall_member')->where(array('id' => $value['uid'], 'act_id' => $id, 'act_type' => '3'))->find();

			if (4 < mb_strlen($user_info['nickname'], 'utf-8')) {
				$user_info['nickname'] = mb_substr($user_info['nickname'], 0, 4, 'utf-8') . '...';
			}

			$prize_user[$key]['nickname'] = $user_info['nickname'];
			$prize_user[$key]['portrait'] = $user_info['portrait'];
		}

		$where = array('token' => $this->token, 'id' => $pid, 'sceneid' => $id);
		$data = M('Wall_prize')->where($where)->find();
		$p_num = M('Wall_prize_record')->where(array('sceneid' => $id, 'prize' => $pid))->count();
		$prize_user['prize_num'] = $data['num'] - $p_num;
		return $prize_user;
	}

	public function prize_data()
	{
		$pid = $this->_post('pid', 'intval');
		$id = $this->_get('id', 'intval');
		$where = array('token' => $this->token, 'id' => $pid, 'sceneid' => $id);
		$data = M('Wall_prize')->where($where)->find();
		$p_num = M('Wall_prize_record')->where(array('sceneid' => $id, 'prize' => $pid))->count();
		$prize_user = M('Wall_prize_record')->where(array('sceneid' => $id, 'prize' => $pid))->order('id desc')->select();

		foreach ($prize_user as $key => $value) {
			$user_info = M('Wall_member')->where(array('id' => $value['uid'], 'act_id' => $id, 'act_type' => '3'))->find();

			if (4 < mb_strlen($user_info['nickname'], 'utf-8')) {
				$user_info['nickname'] = mb_substr($user_info['nickname'], 0, 4, 'utf-8') . '...';
			}

			$prize_user[$key]['nickname'] = $user_info['nickname'];
			$prize_user[$key]['portrait'] = $user_info['portrait'];
		}

		$data['num'] = $data['num'] - $p_num;
		$data['prize_user'] = $prize_user;
		exit(json_encode($data));
	}

	public function get_lottery()
	{
		$pid = $this->_post('pid', 'intval');
		$id = $this->_get('id', 'intval');
		$iwhere = array('token' => $this->token, 'act_id' => $id, 'act_type' => '3');

		if ($this->user_in($id) != '') {
			$iwhere['id'] = array('not in', $this->user_in($id));
		}

		$info = M('Wall_member')->where($iwhere)->order('time desc')->select();

		foreach ($info as $key => $value) {
			if (4 < mb_strlen($value['nickname'], 'utf-8')) {
				$value['nickname'] = mb_substr($value['nickname'], 0, 4, 'utf-8') . '...';
			}

			$info[$key]['nickname'] = $value['nickname'];

			if (4 < mb_strlen($value['truename'], 'utf-8')) {
				$value['truename'] = mb_substr($value['truename'], 0, 4, 'utf-8') . '...';
			}

			$info[$key]['truename'] = $value['truename'];
		}

		$result = array();
		$prize_num = M('wall_prize')->where(array('id' => $pid, 'token' => $this->token, 'sceneid' => $id))->getField('num');
		$prize_user = M('wall_prize_record')->where(array('sceneid' => $id, 'prize' => $pid))->count();

		if (empty($pid)) {
			$result['err'] = 3;
			$result['info'] = '请先选择奖项';
		}

		if ($prize_num <= $prize_user) {
			$result['err'] = 2;
			$result['info'] = '该奖项名额已经用完';
			echo json_encode($result);
			exit();
		}

		if ($info) {
			$result['err'] = 0;
			$result['res'] = $info;
		}
		else {
			$result['err'] = 1;
			$result['info'] = '没有找到可中奖的用户！';
		}

		echo json_encode($result);
	}

	public function lottery_ok()
	{
		$member_db = M('Wall_member');
		$record_db = M('wall_prize_record');
		$prize_db = M('wall_prize');
		$pid = $this->_post('pid', 'intval');
		$id = $this->_get('id', 'intval');
		$uid = $this->_post('uid', 'trim');
		$uid = rtrim($uid, ',');
		$uid = ltrim($uid, ',');
		$uid = explode(',', $uid);

		for ($i = 0; $i < count($uid); $i++) {
			$data['uid'] = $uid[$i];
			$data['sceneid'] = $id;
			$data['prize'] = $pid;
			$data['time'] = time();
			$record_db->add($data);
		}

		$data['count'] = count($uid);
		$this->ajaxReturn($data, 'JSON');
	}

	public function lottery_del()
	{
		$id = $this->_get('id', 'intval');
		$uid = $this->_post('uid', 'intval');
		$del = M('wall_prize_record')->where(array('uid' => $uid, 'sceneid' => $id))->delete();

		if ($del) {
			$data['error'] = 0;
		}
		else {
			$data['error'] = 1;
		}

		$this->ajaxReturn($data, 'JSON');
	}

	public function user_in($sceneid)
	{
		$user = M('wall_prize_record')->where(array('token' => $this->token, 'act_id' => $sceneid, 'act_type' => '3'))->getField('uid', true);
		$arr = '';

		if (!empty($user)) {
			$arr = join(',', $user);
		}

		return $arr;
	}

	public function s_user_in($sceneid, $type)
	{
		$user = M('wall_supperzzle')->where(array('token' => $this->token, 'sceneid' => $sceneid))->getField($type, true);
		$arr = '';

		if (!empty($user)) {
			$arr = join(',', $user);
		}

		return $arr;
	}

	public function loadUser()
	{
		$sceneid = $this->_get('id', 'intval');
		$where = array('token' => $this->token, 'act_id' => $sceneid, 'act_type' => 3);
		$count = M('Wall_member')->where($where)->count();
		echo json_encode(array('err' => 0, 'count' => $count));
	}

	public function show_prize()
	{
		$sceneid = $this->_get('id', 'intval');
		$prize_info = M('wall_prize')->where(array('token' => $this->token, 'sceneid' => $sceneid))->order('sort desc,id desc')->select();
		$this->assign('prize_info', $prize_info);
		$this->display();
	}

	public function show_plog()
	{
		$sceneid = $this->_get('id', 'intval');
		$pid = $this->_get('pid', 'intval');
		$prize_info = M('Wall_prize')->where(array('token' => $this->token, 'sceneid' => $sceneid))->order('sort desc,id desc')->select();

		if (empty($pid)) {
			$pid = $prize_info[0]['id'];
		}

		$user = M('Wall_prize_record')->where(array('sceneid' => $sceneid, 'prize' => $pid))->select();

		foreach ($user as $key => $value) {
			$where = array('act_id' => $sceneid, 'act_type' => 3, 'id' => $value['uid']);
			$user_info = M('Wall_member')->where($where)->field('portrait,nickname')->find();

			if (4 < mb_strlen($user_info['nickname'], 'utf-8')) {
				$user_info['nickname'] = mb_substr($user_info['nickname'], 0, 4, 'utf-8') . '...';
			}

			$user[$key]['name'] = $user_info['nickname'];
			$user[$key]['head'] = $user_info['portrait'];
		}

		$this->assign('pid', $pid);
		$this->assign('user', $user);
		$this->assign('prize_info', $prize_info);
		$this->display();
	}

	public function prize()
	{
		$sceneid = $this->_get('id', 'intval');
		$where = array('sceneid' => $sceneid, 'token' => $this->token);
		$count = M('Wall_prize')->where($where)->count();
		$Page = new Page($count, 15);
		$list = M('Wall_prize')->where($where)->order('sort desc,id asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('sceneid', $sceneid);
		$this->assign('list', $list);
		$this->assign('page', $Page->show());
		$this->display();
	}

	public function signin()
	{
		$model = M('WallMember');
		$data['token'] = $this->token;
		$data['act_id'] = $this->_get('id', 'intval');
		$data['act_type'] = '3';
		$results = $model->where($data)->order('id DESC')->select();
		$this->assign('models', $results);
		$this->display();
	}

	public function signin_ajax()
	{
		$model = M('WallMember');
		$data['token'] = $this->token;
		$data['act_id'] = $this->_post('id', 'intval');
		$data['act_type'] = '3';
		$data['id'] = array('gt', $this->_post('last_id'));
		$result = $model->where($data)->order('id ASC')->find();
		exit(json_encode($result));
	}

	public function prize_set()
	{
		$prize_db = D('Wall_prize');
		$sceneid = $this->_get('sceneid', 'intval');
		$pid = $this->_get('pid', 'intval');
		$prize_info = $prize_db->where(array('token' => $this->token, 'sceneid' => $sceneid, 'id' => $pid))->find();

		if (IS_POST) {
			if ($prize_db->create()) {
				if ($prize_info) {
					$_POST['token'] = $this->token;
					$prize_db->where(array('token' => $this->token, 'id' => $this->_post('id', 'intval'), 'sceneid' => $sceneid))->save($_POST);
					$this->success('修改成功', U('Scene/prize', array('token' => $this->token, 'id' => $sceneid)));
				}
				else {
					$_POST['token'] = $this->token;
					$_POST['sceneid'] = $sceneid;
					$prize_db->add($_POST);
					$this->success('添加成功', U('Scene/prize', array('token' => $this->token, 'id' => $sceneid)));
				}
			}
			else {
				$this->error($prize_db->getError());
			}
		}
		else {
			$this->assign('info', $prize_info);
			$this->assign('sceneid', $sceneid);
			$this->display();
		}
	}

	public function prize_del()
	{
		$sceneid = $this->_get('sceneid', 'intval');
		$id = $this->_get('pid', 'intval');

		if (M('Wall_prize')->where(array('token' => $this->token, 'id' => $id, 'sceneid' => $sceneid))->delete()) {
			M('Wall_prize_record')->where(array('prize' => $id, 'sceneid' => $sceneid, 'token' => $this->token))->delete();
			$this->success('删除成功', U('Scene/prize', array('token' => $this->token, 'id' => $sceneid)));
		}
	}

	public function prizeRecords()
	{
		$where['token'] = $this->token;
		$where['prize'] = $this->_get('pid', 'intval');
		$where['sceneid'] = $this->_get('sceneid', 'intval');
		$recordsArr = M('Wall_prize_record')->where($where)->select();

		foreach ($recordsArr as $key => $value) {
			$user = M('wall_member')->where(array('act_type' => '3', 'act_id' => $where['sceneid'], 'id' => $value['uid']))->field('portrait,nickname')->find();

			if (4 < mb_strlen($user_info['nickname'], 'utf-8')) {
				$user_info['nickname'] = mb_substr($user_info['nickname'], 0, 4, 'utf-8') . '...';
			}

			$recordsArr[$key]['nickname'] = $user['nickname'];
			$recordsArr[$key]['portrait'] = $user['portrait'];
			$recordsArr[$key]['prize_name'] = M('wall_prize')->where(array('token' => $this->token, 'sceneid' => $value['sceneid'], 'id' => $value['prize']))->getField('pname');
		}

		$this->assign('empty', '没有找到相关记录');
		$this->assign('records', $recordsArr);
		$this->display();
	}

	public function vote_countdown()
	{
		$vote = M('Vote')->where(array('token' => $this->token, 'id' => $this->info['vote_id']))->find();

		if ($vote['scene_state'] == 1) {
			$this->redirect('User/Scene/vote', array('token' => $this->token, 'id' => $this->info['id']));
		}
		else {
			$this->display();
		}
	}

	public function vote()
	{
		$vote = M('Vote')->where(array('token' => $this->token, 'id' => $this->info['vote_id']))->find();
		$now = time();

		foreach ($vote_list as $key => $value) {
			if (($value['enddate'] < $now) && ($value['status'] == 0)) {
				$vote_list[$key]['is_end'] = 1;
			}
			else {
				$vote_list[$key]['is_end'] = 1;
			}
		}

		$this->assign('vote', $vote);
		$this->display();
	}

	public function get_vote()
	{
		$vote_id = $this->_get('vote_id', 'intval');
		$scene_id = $this->_get('scene_id', 'intval');
		$status = M('Vote')->where(array('token' => $this->token, 'id' => $vote_id))->getField('status');
		$vote_item = M('Vote_item')->where(array('vid' => $vote_id))->order('vcount desc,id desc')->select();
		$result = array();

		if ($vote_item) {
			$result['err'] = 0;
			$result['res'] = $vote_item;
			$result['status'] = $status;
		}
		else {
			$result['err'] = 1;
			$result['res'] = '没有找到投票选项';
		}

		echo json_encode($result);
	}

	public function vote_start()
	{
		$vote_id = $this->_get('vote_id', 'intval');
		$offset = M('Vote')->where(array('token' => $this->token, 'id' => $vote_id, 'status' => 0))->save(array('status' => 1));
		$result['err'] = 0;
		$result['msg'] = '投票已经开启';
		echo json_encode($result);
	}

	public function ajaxVcount()
	{
		$vote_id = $this->_get('vote_id', 'intval');
		$vote_info = M('Vote')->where(array('token' => $this->token, 'id' => $vote_id))->find();
		$res = M('Vote_item')->where(array('vid' => $vote_id))->field('id,vcount')->select();
		$result['res'] = $res;
		$time = time();
		if (($vote_info['statdate'] < $time) && ($time < $vote_info['enddate'])) {
			$result['flag'] = 1;
		}
		else {
			$result['flag'] = 0;
		}

		echo json_encode($result);
	}

	public function vote_stop()
	{
		$vote_id = $this->_get('vote_id', 'intval');
		$now = time();
		$offset = M('Vote')->where(array('token' => $this->token, 'id' => $vote_id))->save(array('status' => 0, 'statdate' => $now - 1, 'enddate' => $now - 1));

		if ($offset = 1) {
			$result['err'] = 0;
			$id = M('Vote_item')->where(array('vid' => $vote_id))->order('vcount desc')->getField('id', true);
			$id = array_flip($id);
			$res = M('Vote_item')->where(array('vid' => $vote_id))->order('id desc')->select();

			foreach ($res as $key => $value) {
				$res[$key]['ranks'] = $id[$value['id']] + 1;
			}

			$result['res'] = $res;
		}

		echo json_encode($result);
	}

	public function vote_count()
	{
		$vote_id = $this->_get('vote_id', 'intval');
		$result['count'] = count($this->_getMember('vote_id', $vote_id, '', 'id'));
		$result['fcount'] = M('Vote_record')->where(array('vid' => $vote_id))->count();
		echo json_encode($result);
	}

	public function show_vote()
	{
		$vote_id = $this->_post('id', 'intval');
		$now = time();
		$vote_info = M('Vote')->where(array('token' => $this->token, 'id' => $vote_id))->find();

		if ($vote_info) {
			$res = M('Vote_item')->where(array('vid' => $vote_info['id']))->order('id ASC, vcount desc')->limit(15)->select();
			$percent = M('Vote_item')->field('SUM(vcount) AS total')->where(array('vid' => $vote_info['id']))->find();
		}

		$data = $info = array();

		foreach ($res as $re) {
			$data['percent'] = $this->percent($re['vcount'], $percent['total']);
			$data['number'] = $re['vcount'];
			$data['name'] = $re['item'];
			$data['id'] = $re['id'];
			$data['pillar'] = '';
			$info['vote'][] = $data;
		}

		$info['info'] = $vote_info;
		exit(json_encode($info));
	}

	public function supperzzle()
	{
		$sceneid = $this->_get('id', 'intval');

		if ($this->s_user_in($sceneid, 'nid') != '') {
			$iwhere['id'] = array('not in', $this->s_user_in($sceneid, 'nid'));
		}

		if ($this->s_user_in($sceneid, 'vid') != '') {
			$jwhere['id'] = array('not in', $this->s_user_in($sceneid, 'vid'));
		}

		if ($this->info['ddp_is_sex'] == 1) {
			$iwhere['sex'] = 1;
			$jwhere['sex'] = 2;
			$maleCount = count($this->_getMember('id', $sceneid, '', 'list', $iwhere));
			$femaleCount = count($this->_getMember('id', $sceneid, '', 'list', $jwhere));
		}
		else {
			$count = count($this->_getMember('id', $this->_get('id', 'intval'), '', 'id'));
			$half = floor($count / 2);
			$limit = '0,' . $half;
			$limit2 = $half . ',' . ($count - 1);
			$maleCount = count($this->_getMember('id', $sceneid, $limit, 'list', $iwhere));
			$femaleCount = count($this->_getMember('id', $sceneid, $limit2, 'list', $jwhere));
		}

		$this->assign('maleCount', $maleCount);
		$this->assign('femaleCount', $femaleCount);
		$count = count($this->_getMember('id', $this->_get('id', 'intval'), '', 'id'));
		$this->assign('count', $count);
		$slist = M('wall_supperzzle')->where(array('sceneid' => $sceneid, 'token' => $this->token))->order('id desc')->select();

		foreach ($slist as $sk => $sv) {
			$slist[$sk]['ninfo'] = M('Wall_member')->where(array('id' => $sv['nid']))->find();
			$slist[$sk]['vinfo'] = M('Wall_member')->where(array('id' => $sv['vid']))->find();
		}

		$this->assign('slist', $slist);
		$this->display();
	}

	public function defUser()
	{
		$sceneid = $this->_get('id', 'intval');
		$result = array();

		if ($this->s_user_in($sceneid, 'nid') != '') {
			$iwhere['id'] = array('not in', $this->s_user_in($sceneid, 'nid'));
		}

		if ($this->s_user_in($sceneid, 'vid') != '') {
			$jwhere['id'] = array('not in', $this->s_user_in($sceneid, 'vid'));
		}

		if ($this->info['ddp_is_sex'] == 1) {
			$iwhere['sex'] = 1;
			$jwhere['sex'] = 2;
			$male = $this->_getMember('id', $sceneid, '', 'list', $iwhere);
			$female = $this->_getMember('id', $sceneid, '', 'list', $jwhere);
			$maleCount = count($this->_getMember('id', $sceneid, '', 'list', $iwhere));
			$femaleCount = count($this->_getMember('id', $sceneid, '', 'list', $jwhere));
		}
		else {
			$count = count($this->_getMember('id', $this->_get('id', 'intval'), '', 'id'));
			$half = floor($count / 2);
			$limit = '0,' . $half;
			$limit2 = $half . ',' . ($count - 1);
			$male = $this->_getMember('id', $sceneid, $limit, 'list', $iwhere);
			$female = $this->_getMember('id', $sceneid, $limit2, 'list', $jwhere);
			$maleCount = count($this->_getMember('id', $sceneid, $limit, 'list', $iwhere));
			$femaleCount = count($this->_getMember('id', $sceneid, $limit2, 'list', $jwhere));
		}

		if (empty($maleCount) || empty($femaleCount)) {
			$result['err'] = 1;
			$result['msg'] = '剩余玩家不足以配对！';
		}
		else {
			$result['err'] = 0;
			$result['data']['list']['male'] = $male;
			$result['data']['list']['female'] = $female;
		}

		$result['data']['maleCount'] = $maleCount;
		$result['data']['femaleCount'] = $femaleCount;
		echo json_encode($result);
	}

	public function add_slog()
	{
		$_POST['addtime'] = time();
		$_POST['token'] = $this->token;
		$_POST['sceneid'] = $this->_get('id', 'intval');
		$data['sid'] = M('Wall_supperzzle')->add($_POST);
		$data['error'] = 0;
		$this->ajaxReturn($data, 'JSON');
	}

	public function del_slog()
	{
		$del['token'] = $this->token;
		$del['sceneid'] = $this->_get('id', 'intval');
		$del['id'] = $this->_post('sid', 'intval');
		M('Wall_supperzzle')->where($del)->delete();
		$data['error'] = 0;
		$this->ajaxReturn($data, 'JSON');
	}

	public function set_slog()
	{
		$where['token'] = $this->token;
		$where['sceneid'] = $this->_get('id', 'intval');
		$where['id'] = $this->_post('sid', 'intval');
		M('Wall_supperzzle')->where($where)->save(array($_POST['stype'] => $_POST['uid']));
		$data['error'] = 0;
		$this->ajaxReturn($data, 'JSON');
	}

	public function supperzzle_again()
	{
		$where['token'] = $this->token;
		$where['sceneid'] = $this->_get('id', 'intval');
		M('Wall_supperzzle')->where($where)->delete();
		$data['error'] = 0;
		$this->ajaxReturn($data, 'JSON');
	}

	public function supperzzle_log()
	{
		$sceneid = $this->_get('id', 'intval');
		$sid = $this->_get('sid', 'intval');
		$count = M('Wall_supperzzle')->where(array('sceneid' => $sceneid))->order('addtime desc')->getField('id', true);

		if (empty($sid)) {
			$sid = $count[0];
		}

		$info = M('Wall_supperzzle')->where(array('sceneid' => $sceneid, 'id' => $sid))->order('addtime desc')->find();
		$n_info = $this->supperzzle_user($info['nid'], $sceneid);
		$v_info = $this->supperzzle_user($info['vid'], $sceneid);
		$info['n_name'] = $n_info['nickname'];
		$info['n_head'] = $n_info['portrait'];
		$info['v_name'] = $v_info['nickname'];
		$info['v_head'] = $v_info['portrait'];
		$this->assign('sceneid', $sceneid);
		$this->assign('info', $info);
		$this->assign('count', $count);
		$this->display();
	}

	public function supperzzle_user($id, $sceneid)
	{
		$where = array('token' => $this->token, 'act_type' => '3', 'act_id' => $sceneid, 'id' => $id);
		$user = M('wall_member')->where($where)->field('nickname,portrait')->find();
		return $user;
	}

	public function _getMember($field, $id, $limit = '', $return = 'list', $ext = '')
	{
		$member_db = M('Wall_member');
		$scene_db = M('Wechat_scene');
		$act_id = $scene_db->where(array('token' => $this->token, 'is_open' => '1', $field => $id))->getField('id');

		if ($act_id) {
			$where = array('token' => $this->token, 'act_id' => $act_id, 'act_type' => '3');
		}
		else if ($field == 'shake_id') {
			$where = array('token' => $this->token, 'act_id' => $id, 'act_type' => '2');
		}
		else if ($field == 'wall_id') {
			$where = array('token' => $this->token, 'act_id' => $id, 'act_type' => '1');
		}

		if ($ext) {
			$where = array_merge($where, $ext);
		}

		if ($return == 'list') {
			$user = $member_db->where($where)->limit($limit)->select();
		}
		else if ($return == 'id') {
			$user = $member_db->where($where)->limit($limit)->getField('id', true);
		}

		return $user;
	}

	public function header()
	{
		$this->display();
	}

	public function footer()
	{
		$this->display();
	}

	public function check_msg()
	{
		$id = $this->_get('id', 'intval');
		$uid = $this->_get('uid', 'intval');
		$status = $this->_post('status', 'intval');
		$keyword = $this->_post('keyword', 'trim');
		$where = array('token' => $this->token, 'wallid' => $id);

		if ($status == 1) {
			$where['is_check'] = '0';
		}
		else if ($status == 2) {
			$where['is_check'] = '1';
		}

		if (!empty($keyword)) {
			$where['content'] = array('like', '%' . $keyword . '%');
		}

		if (!empty($uid)) {
			$where['uid'] = $uid;
		}

		$list = M('Wall_message')->where($where)->order('time desc')->select();

		foreach ($list as $key => $value) {
			$user = M('Wall_member')->where(array('token' => $this->token, 'id' => $value['uid']))->find();
			$list[$key]['username'] = $user['nickname'];

			if ($value['type'] == 'event') {
				unset($list[$key]);
			}
		}

		$ck_msg = M('WechatScene')->where(array('token' => $this->token, 'id' => $id))->getField('wall_check');
		$this->assign('id', $id);
		$this->assign('uid', $uid ? $uid : 0);
		$this->assign('ck_msg', $ck_msg);
		$this->assign('now', time());
		$this->assign('list', $list);
		$this->display();
	}

	public function laodMsg()
	{
		$id = $this->_get('id', 'intval');
		$uid = $this->_get('uid', 'intval');
		$loadtime = $this->_get('loadtime');
		$where = array(
			'token'  => $this->token,
			'wallid' => $id,
			'time'   => array('gt', $loadtime)
			);

		if (!empty($uid)) {
			$where['uid'] = $uid;
		}

		$result = array();
		$msg = M('Wall_message')->where($where)->order('time asc')->select();

		foreach ($msg as $key => $value) {
			$uwhere = array('token' => $this->token, 'id' => $value['uid']);
			$msg[$key]['username'] = M('Wall_member')->where($uwhere)->getField('nickname');
			$msg[$key]['time'] = date('Y-m-d H:i:s', $value['time']);

			if ($value['type'] == 'event') {
				unset($msg[$key]);
			}
		}

		if ($msg) {
			$result['err'] = 0;
			$result['loadtime'] = time();
			$result['res'] = $msg;
		}

		echo json_encode($result);
	}

	public function is_check()
	{
		$result = array();
		$wallid = $this->_post('wallid');
		$mid = $this->_post('mid', 'intval');
		$checked = $this->_post('checked', 'intval');
		$ck_msg = M('WechatScene')->where(array('token' => $this->token, 'id' => $wallid))->getField('wall_check');

		if ($ck_msg == '0') {
			$result['err'] = 1;
			$result['info'] = '如需审核消息，请开启微信墙审核状态';
			echo json_encode($result);
			exit();
		}

		$idArr = explode(',', $mid);
		$where = array(
			'token'  => $this->token,
			'wallid' => $wallid,
			'id'     => array('in', $idArr)
			);
		$msg_info = M('Wall_message')->where($where)->field('time,check_time')->find();
		$update = array('is_check' => $checked);
		if (($checked == 1) && ($msg_info['time'] == $msg_info['check_time'])) {
			$update['check_time'] = time();
		}

		if (M('Wall_message')->where($where)->save($update)) {
			$result['err'] = 0;
			$result['info'] = '';
			echo json_encode($result);
			exit();
		}
	}

	public function del_msg()
	{
		$wallid = $this->_get('id', 'intval');
		$mid = $this->_get('mid', 'intval');
		$where = array('token' => $this->token, 'wallid' => $wallid, 'id' => $mid);

		if (M('Wall_message')->where($where)->delete()) {
			echo true;
		}
	}

	private function join_count()
	{
		$id = intval($this->_request('id'));
		$where = array('token' => $this->token, 'act_id' => $id, 'act_type' => '3');
		return M('Wall_member')->where($where)->count();
	}

	public function join_count_ajax()
	{
		if (in_array($_GET['this_a'], array('signin', 'lottery', 'supperzzle'))) {
			$data['totalname'] = '人参与';
			$data['count'] = $this->join_count();
		}
		else if ($_GET['this_a'] == 'wall') {
			$count = M('Wall_message')->where(array('token' => $this->token, 'wallid' => $this->info['id']))->count();
			$data['count'] = $count;
			$data['totalname'] = '条消息';
		}

		if ($_GET['this_a'] == 'wall') {
			$data['count'] = M('Wall_message')->where(array(
	'token'    => $this->token,
	'wallid'   => $this->info['id'],
	'is_scene' => '1',
	'is_check' => 1,
	'type'     => array('neq', 'event')
	))->count();
			$data['totalname'] = '条消息';
		}
		else {
			if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'red_packet')) || ($_GET['this_a'] == 'red_packet')) {
				$data['count'] = M('shakelottery_users')->where(array('token' => $this->token, 'aid' => $this->info['red_packet_id']))->count();
				$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
				$data['totalname'] = '人摇红包';
			}
			else {
				if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'jiugongge')) || ($_GET['this_a'] == 'jiugongge')) {
					$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['jiugongge_id']))->select();
					$data['count'] = count($player);
					$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
					$data['totalname'] = '人抽奖';
				}
				else {
					if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'zajindan')) || ($_GET['this_a'] == 'zajindan')) {
						$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['zajindan_id']))->select();
						$data['count'] = count($player);
						$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
						$data['totalname'] = '人砸金蛋';
					}
					else {
						if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'shuiguoji')) || ($_GET['this_a'] == 'shuiguoji')) {
							$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['shuiguoji_id']))->select();
							$data['count'] = count($player);
							$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
							$data['totalname'] = '人抽奖';
						}
						else {
							if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'guaguaka')) || ($_GET['this_a'] == 'guaguaka')) {
								$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['guaguaka_id']))->select();
								$data['count'] = count($player);
								$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
								$data['totalname'] = '人刮卡';
							}
							else {
								if ((($_GET['this_a'] == 'hudong') && ($_GET['type'] == 'dazhuanpan')) || ($_GET['this_a'] == 'dazhuanpan')) {
									$player = M('lottery_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'lid' => $this->info['dazhuanpan_id']))->select();
									$data['count'] = count($player);
									$data['count'] = M('scene_active')->where(array('token' => $this->token, 'active_id' => $this->info[$_GET['type'] . '_id'], 'scene_id' => $this->info['id'], 'type' => $_GET['type']))->count();
									$data['totalname'] = '人抽奖';
								}
								else {
									if (($_GET['this_a'] == 'vote') || ($_GET['this_a'] == 'vote_countdown')) {
										$player = M('vote_record')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'vid' => $this->info['vote_id']))->select();
										$data['count'] = count($player);
										$data['totalname'] = '人投票';
									}
									else {
										if (($_GET['this_a'] == 'shake_active') || ($_GET['this_a'] == 'shake')) {
											$player = M('shake_rt')->Distinct(true)->field('wecha_id')->where(array('token' => $this->token, 'shakeid' => $this->info['shake_id']))->select();
											$data['count'] = count($player);
											$data['totalname'] = '人摇一摇';
										}
										else if ($_GET['this_a'] == 'signin') {
											$data['count'] = $this->join_count();
											$data['totalname'] = '人签到';
										}
										else {
											$data['count'] = $this->join_count();
											$data['totalname'] = '人参与';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$this->ajaxReturn($data, 'JSON');
	}

	private function other_source_condition($data)
	{
		$data['other_source'] = 'scene';
		return $data;
	}
}

?>
