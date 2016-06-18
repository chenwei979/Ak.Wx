<?php

class ConditionalmenuAction extends UserAction
{
	public $wxuser;

	public function _initialize()
	{
		parent::_initialize();
		$this->wxuser = M('wxuser')->where(array('token' => $this->token))->find();
	}

	public function index()
	{
		$where['token'] = $this->token;
		$where_page['token'] = $this->token;

		if (!empty($_GET['search'])) {
			$search = trim($_GET['search']);
			$where['name'] = array('like', '%' . $search . '%');
			$where_page['search'] = $search;
		}

		$count = M('conditionalmenu')->where($where)->count();
		$page = new Page($count, 10);

		foreach ($where_page as $key => $val) {
			$page->parameter .= $key . '=' . urlencode($val) . '&';
		}

		$show = $page->show();
		$menu_list = M('conditionalmenu')->where($where)->order('time desc,addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('page', $show);
		$this->assign('menu_list', $menu_list);
		$groups = M('wechat_group')->where(array('token' => $this->token))->order('id ASC')->select();

		foreach ($groups as $vo) {
			$group[$vo['wechatgroupid']] = $vo['name'];
		}

		$this->assign('group', $group);
		$this->display();
	}

	public function matchrule_set()
	{
		if (($this->wxuser['winxintype'] != 3) && ($this->wxuser['winxintype'] != 4)) {
			$this->error('此功能只有认证的公众号才能用的哟~');
			exit();
		}

		$id = intval($_GET['id']);
		$set = M('conditionalmenu')->where(array('token' => $this->token, 'id' => $id))->find();

		if (IS_POST) {
			if ($set == '') {
				$_POST['token'] = $this->token;
				$_POST['addtime'] = time();
				$id = M('conditionalmenu')->add($_POST);

				if (0 < $id) {
					$this->success('添加成功', U('User/Conditionalmenu/index', array('token' => $this->token)));
				}
			}
			else {
				if ($set['state'] == 1) {
					$_POST['state'] = 2;
				}

				M('conditionalmenu')->where(array('token' => $this->token, 'id' => $id))->save($_POST);
				$this->success('修改成功', U('User/Conditionalmenu/index', array('token' => $this->token)));
			}
		}
		else {
			if ($set == '') {
				$apiOauth = new apiOauth();
				$access_token = $apiOauth->update_authorizer_access_token($this->wxuser['appid'], $this->wxuser);
				$menu_url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $access_token;
				$menu_return = json_decode($this->https_request($menu_url), true);

				if (0 < $menu_return['errcode']) {
					if ($menu_return['errcode'] == 46003) {
						$this->error('请先添加并生成默认菜单', U('User/Diymen/index', array('token' => $token)));
					}
					else {
						$errmsg = GetErrorMsg::wx_error_msg($menu_return['errcode']);

						if ($errmsg != '') {
							$this->error($menu_return['errcode'] . '：' . $errmsg);
						}
						else {
							$this->error($menu_return['errcode'] . '：' . $menu_return['errmsg']);
						}
					}
				}
			}

			$this->assign('set', $set);
			$groups = M('wechat_group')->where(array('token' => $this->token))->order('id ASC')->select();
			$this->assign('groups', $groups);
			$province_list = M('weixin_region')->distinct(true)->field('province')->select();
			$this->assign('province_list', $province_list);
			$city_html = '<option value="">--请选择城市--</option>';

			if ($set['province'] != '') {
				$city_list = M('weixin_region')->where(array('province' => $set['province']))->select();

				foreach ($city_list as $list) {
					if ($list['city'] == $set['city']) {
						$city_html .= '<option value="' . $list['city'] . '" selected>' . $list['city'] . '</option>';
					}
					else {
						$city_html .= '<option value="' . $list['city'] . '">' . $list['city'] . '</option>';
					}
				}
			}

			$this->assign('city_html', $city_html);
			$this->display();
		}
	}

	public function city()
	{
		if (IS_POST) {
			$city_list = M('weixin_region')->where(array('province' => $_POST['province']))->select();
			$data['city_list'] = '<option value="">--请选择城市--</option>';

			foreach ($city_list as $list) {
				$data['city_list'] .= '<option value="' . $list['city'] . '">' . $list['city'] . '</option>';
			}

			$this->ajaxReturn($data, 'JSON');
		}
	}

	public function menu_set()
	{
		if (IS_POST) {
		}
		else {
			$id = intval($_GET['cid']);
			$conditionalmenu = M('conditionalmenu')->where(array('token' => $this->token, 'id' => $id))->find();
			$this->assign('conditionalmenu', $conditionalmenu);
			$class = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => 0, 'cid' => $id))->order('sort desc, id ASC')->select();
			$i = 0;
			$count = 3;

			foreach ($class as $key => $vo) {
				if ($vo['is_show']) {
					$i++;
					$count = (4 < $i ? 4 : $i);
					$displayMenu[] = $vo;
				}

				$c = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => $vo['id'], 'cid' => $id))->order('sort desc, id ASC')->select();
				$class[$key]['class'] = $c;

				foreach ($c as $k => $v) {
					if ($v['is_show'] && $vo['is_show']) {
						$displayMenu[$i - 1]['class'][] = $v;
					}
				}
			}

			$this->assign('class', $class);
			$this->assign('show', array('is_show' => 1));
			$this->assign('wxsys', $this->_get_sys());
			$this->assign('displayMenu', $displayMenu);
			$this->assign('count', $count);
			$this->display();
		}
	}

	public function class_add()
	{
		if (IS_POST) {
			if (($_POST['menu_type'] == 2) && ($_POST['url'] != '')) {
				if (!D('Img')->isOpenSync($this->wxuser)) {
					if ($this->dwzQuery(array('tinyurl' => $_POST['url']))) {
						$this->error('禁止使用短网址');
					}

					$_POST['url'] = $this->replaceUrl($_POST['url'], array(
	'query' => array('wecha_id' => '{wechat_id}')
	));
				}
			}

			$type = array(
				1 => 'keyword',
				2 => 'url',
				3 => 'wxsys',
				4 => 'tel',
				5 => array('longitude', 'latitude')
				);

			foreach ($type as $key => $value) {
				if ($_POST['menu_type'] != $key) {
					if (is_array($value)) {
						foreach ($value as $v) {
							unset($_POST[$v]);
						}
					}
					else {
						unset($_POST[$value]);
					}
				}
			}

			$this->all_insert('Conditionalmenu_class', '/menu_set?token=' . $this->token . '&cid=' . $_POST['cid']);
		}
	}

	public function class_del()
	{
		$class = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => $this->_get('id'), 'cid' => $_GET['cid']))->order('sort desc')->find();

		if ($class == false) {
			$back = M('conditionalmenu_class')->where(array('token' => session('token'), 'id' => $this->_get('id')))->delete();

			if ($back == true) {
				$this->success('删除成功', U('User/Conditionalmenu/menu_set', array('token' => $this->token, 'cid' => $_GET['cid'])));
			}
			else {
				$this->error('删除失败');
			}
		}
		else {
			$class2 = M('conditionalmenu_class')->where(array('token' => session('token'), 'id' => $this->_get('id'), 'cid' => $_GET['cid']))->order('sort desc')->find();

			if (0 != $class2['pid']) {
				$back = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => $this->_get('id'), 'cid' => $_GET['cid']))->delete();
				$back2 = M('conditionalmenu_class')->where(array('token' => session('token'), 'id' => $this->_get('id'), 'cid' => $_GET['cid']))->delete();
				if (($back == true) && ($back2 == true)) {
					$this->success('删除成功', U('User/Conditionalmenu/menu_set', array('token' => $this->token, 'cid' => $_GET['cid'])));
				}
				else {
					$this->error('删除失败');
				}
			}
			else {
				$this->error('请先删除子菜单');
			}
		}
	}

	public function class_edit()
	{
		$this->assign('wxsys', $this->_get_sys());

		if (IS_POST) {
			$_POST['id'] = $this->_get('id');
			if (($_POST['menu_type'] == 1) && ($_POST['keyword'] != '')) {
				$set = array('url' => '', 'wxsys' => '', 'tel' => '', 'nav' => '');
				unset($_POST['wxsys']);
				unset($_POST['url']);
				unset($_POST['tel']);
				unset($_POST['longitude']);
				unset($_POST['latitude']);
			}
			else {
				if (($_POST['menu_type'] == 2) && ($_POST['url'] != '')) {
					if (!D('Img')->isOpenSync($this->wxuser)) {
						if ($this->dwzQuery(array('tinyurl' => $_POST['url']))) {
							$this->error('禁止使用短网址');
						}

						$_POST['url'] = $this->replaceUrl($_POST['url'], array(
	'query' => array('wecha_id' => '{wechat_id}')
	));
					}

					$set = array('keyword' => '', 'wxsys' => '', 'tel' => '', 'nav' => '');
					unset($_POST['keyword']);
					unset($_POST['wxsys']);
					unset($_POST['tel']);
					unset($_POST['longitude']);
					unset($_POST['latitude']);
				}
				else {
					if (($_POST['menu_type'] == 3) && ($_POST['wxsys'] != '')) {
						$set = array('keyword' => '', 'url' => '', 'tel' => '', 'nav' => '');
						unset($_POST['keyword']);
						unset($_POST['url']);
						unset($_POST['tel']);
						unset($_POST['longitude']);
						unset($_POST['latitude']);
					}
					else {
						if (($_POST['menu_type'] == 4) && ($_POST['tel'] != '')) {
							$set = array('keyword' => '', 'url' => '', 'wxsys' => '', 'nav' => '');
							unset($_POST['keyword']);
							unset($_POST['url']);
							unset($_POST['wxsys']);
							unset($_POST['longitude']);
							unset($_POST['latitude']);
						}
						else {
							if (($_POST['menu_type'] == 5) && ($_POST['longitude'] != '') && ($_POST['latitude'] != '')) {
								$set = array('keyword' => '', 'url' => '', 'wxsys' => '', 'tel' => '');
								unset($_POST['keyword']);
								unset($_POST['url']);
								unset($_POST['wxsys']);
								unset($_POST['tel']);
							}
						}
					}
				}
			}

			$matchrule = M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->find();

			if ($matchrule['state'] == 1) {
				M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->save(array('state' => 2));
			}

			M('conditionalmenu_class')->where(array('id' => $_POST['id']))->save($set);
			$this->all_save('Conditionalmenu_class', '/menu_set?token=' . $this->token . '&cid=' . $this->_get('cid') . '&id=' . $this->_get('id'));
		}
		else {
			$data = M('conditionalmenu_class')->where(array('token' => session('token'), 'id' => $this->_get('id'), 'cid' => $this->_get('cid')))->find();

			if ($data == false) {
				$this->error('您所操作的数据对象不存在！');
			}

			$class = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => 0, 'cid' => $this->_get('cid')))->order('sort desc')->select();

			if ($data['keyword'] != '') {
				$type = 1;
			}
			else if ($data['url'] != '') {
				$type = 2;
			}
			else if ($data['wxsys'] != '') {
				$type = 3;
			}
			else if ($data['tel'] != '') {
				$type = 4;
			}
			else if ($data['nav'] != '') {
				$type = 5;
				list($data['longitude'], $data['latitude']) = explode(',', $data['nav']);
			}

			$class2 = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => $this->_get('id'), 'cid' => $this->_get('cid')))->order('sort desc')->find();

			foreach ($class as $key => $value) {
				if ($this->_get('id') == $value['id']) {
					unset($class[$key]);
				}
			}

			if ((0 != $data['pid']) || ($class2 == false)) {
				$this->assign('class', $class);
			}

			$this->assign('show', $data);
			$this->assign('type', $type);
			$json['html'] = $this->fetch();
			$json['status'] = 200;
			$json['url'] = U('User/Conditionalmenu/class_edit', array('id' => $this->_get('id'), 'cid' => $this->_get('cid')));
			exit(json_encode($json));
		}
	}

	public function _get_sys($type = '', $key = '')
	{
		$wxsys = array('扫码带提示', '扫码推事件', '系统拍照发图', '拍照或者相册发图', '微信相册发图', '发送位置');

		if ($type == 'send') {
			$wxsys = array('扫码带提示' => 'scancode_waitmsg', '扫码推事件' => 'scancode_push', '系统拍照发图' => 'pic_sysphoto', '拍照或者相册发图' => 'pic_photo_or_album', '微信相册发图' => 'pic_weixin', '发送位置' => 'location_select');
			return $wxsys[$key];
			exit();
		}

		return $wxsys;
	}

	public function class_send()
	{
		if (IS_GET) {
			$model = M('Wxuser')->where(array('token' => session('token')))->find();

			if (empty($model['appid'])) {
				$this->error('AppId 不能为空，请填写保存后再进行生成菜单');
			}
			else if (empty($model['appsecret'])) {
				if (1 != $model['type']) {
					$this->error('非托管用户 AppSecret 不能为空，请填写保存后再进行生成菜单');
				}
			}

			$apiOauth = new apiOauth();
			$access_token = $apiOauth->update_authorizer_access_token($this->wxuser['appid'], $this->wxuser);
			$this->access_token = $access_token;
			$data = '{"button":[';
			$class = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => 0, 'is_show' => 1, 'cid' => intval($_GET['cid'])))->limit(3)->order('sort DESC, id ASC')->select();
			$kcount = count($class);

			if ($kcount < 1) {
				$this->error('未设置菜单内容', U('User/Conditionalmenu/menu_set', array('token' => $this->token, 'cid' => $_GET['cid'])));
			}

			$k = 1;

			foreach ($class as $key => $vo) {
				$data .= '{"name":"' . $vo['title'] . '",';
				$c = M('conditionalmenu_class')->where(array('token' => session('token'), 'pid' => $vo['id'], 'is_show' => 1, 'cid' => intval($_GET['cid'])))->limit(5)->order('sort DESC, id ASC')->select();
				$count = count($c);
				$vo['url'] = $this->convertLink($vo['url']);
				$tel = $this->convertLink('{siteUrl}' . U('Wap/Index/tel', array('tel' => $vo['tel'], 'token' => session('token'))));
				$amap = $this->convertLink('{siteUrl}' . U('Wap/Index/map', array('nav' => $vo['nav'], 'name' => $vo['title'], 'token' => session('token'))));

				if ($c != false) {
					$data .= '"sub_button":[';
				}
				else if ($vo['keyword']) {
					if (D('Img')->isOpenSync($model)) {
						$data .= '"type":"view_limited","media_id":"' . $vo['keyword'] . '"';
					}
					else {
						$data .= '"type":"click","key":"' . $vo['keyword'] . '"';
					}
				}
				else if ($vo['url']) {
					if (D('Img')->isOpenSync($model)) {
						$data .= '"type":"media_id","media_id":"' . $vo['url'] . '"';
					}
					else {
						$data .= '"type":"view","url":"' . $vo['url'] . '"';
					}
				}
				else if ($vo['wxsys']) {
					$data .= '"type":"' . $this->_get_sys('send', $vo['wxsys']) . '","key":"' . $vo['wxsys'] . '"';
				}
				else if ($vo['tel']) {
					$data .= '"type":"view","url":"' . $tel . '"';
				}
				else if ($vo['nav']) {
					$data .= '"type":"view","url":"' . $amap . '"';
				}

				$i = 1;

				foreach ($c as $voo) {
					$voo['url'] = $this->convertLink($voo['url']);
					$tel = $this->convertLink($this->siteUrl . U('Wap/Index/tel', array('tel' => $voo['tel'], 'token' => session('token'))));
					$amap = $this->convertLink('{siteUrl}' . U('Wap/Index/map', array('nav' => $voo['nav'], 'name' => $voo['title'], 'token' => session('token'))));

					if ($i == $count) {
						if ($voo['keyword']) {
							if (D('Img')->isOpenSync($model)) {
								$data .= '{"type":"view_limited","name":"' . $voo['title'] . '","media_id":"' . $voo['keyword'] . '"}';
							}
							else {
								$data .= '{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"}';
							}
						}
						else if ($voo['url']) {
							if (D('Img')->isOpenSync($model)) {
								$data .= '{"type":"media_id","name":"' . $voo['title'] . '","media_id":"' . $voo['url'] . '"}';
							}
							else {
								$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'] . '"}';
							}
						}
						else if ($voo['wxsys']) {
							$data .= '{"type":"' . $this->_get_sys('send', $voo['wxsys']) . '","name":"' . $voo['title'] . '","key":"' . $voo['wxsys'] . '"}';
						}
						else if ($voo['tel']) {
							$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $tel . '"}';
						}
						else if ($voo['nav']) {
							$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $amap . '"}';
						}
					}
					else if ($voo['keyword']) {
						if (D('Img')->isOpenSync($model)) {
							$data .= '{"type":"view_limited","name":"' . $voo['title'] . '","media_id":"' . $voo['keyword'] . '"},';
						}
						else {
							$data .= '{"type":"click","name":"' . $voo['title'] . '","key":"' . $voo['keyword'] . '"},';
						}
					}
					else if ($voo['url']) {
						if (D('Img')->isOpenSync($model)) {
							$data .= '{"type":"media_id","name":"' . $voo['title'] . '","media_id":"' . $voo['url'] . '"},';
						}
						else {
							$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $voo['url'] . '"},';
						}
					}
					else if ($voo['wxsys']) {
						$data .= '{"type":"' . $this->_get_sys('send', $voo['wxsys']) . '","name":"' . $voo['title'] . '","key":"' . $voo['wxsys'] . '"},';
					}
					else if ($voo['tel']) {
						$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $tel . '"},';
					}
					else if ($voo['nav']) {
						$data .= '{"type":"view","name":"' . $voo['title'] . '","url":"' . $amap . '"},';
					}

					$i++;
				}

				if ($c != false) {
					$data .= ']';
				}

				if ($k == $kcount) {
					$data .= '}';
				}
				else {
					$data .= '},';
				}

				$k++;
			}

			$data .= '],"matchrule":{';
			$matchrule = M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->find();
			$data .= '"group_id":"' . ($matchrule['group_id'] == -1 ? '' : $matchrule['group_id']) . '",';
			$data .= '"sex":"' . $matchrule['sex'] . '",';
			$data .= '"country":"中国",';
			$data .= '"province":"' . $matchrule['province'] . '",';
			$data .= '"city":"' . $matchrule['city'] . '",';
			$data .= '"client_platform_type":"' . $matchrule['client_platform_type'] . '"}';
			$data .= '}';

			if ($matchrule['menuid'] != '') {
				$del_url = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=' . $this->access_token;
				$del_data = '{"menuid":"' . $matchrule['menuid'] . '"}';
				$del_return = json_decode($this->https_request($del_url, $del_data), true);
			}

			$add_url = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=' . $this->access_token;
			$add_return = json_decode($this->https_request($add_url, $data), true);

			if (0 < $add_return['menuid']) {
				M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->save(array('time' => time(), 'menuid' => $add_return['menuid'], 'state' => 1));
				$this->success('生成菜单成功');
			}
			else {
				$errmsg = GetErrorMsg::wx_error_msg($add_return['errcode']);

				if ('480001' == $add_return['errcode']) {
					$this->error('没有生成自定义菜单的权限！');
				}
				else if ($errmsg != '') {
					$this->error($add_return['errcode'] . ':' . $errmsg . '!');
				}
				else {
					$this->error($add_return['errcode'] . ':' . $add_return['errmsg'] . '!');
				}
			}
		}
		else {
			$this->error('非法操作');
		}
	}

	public function menu_del()
	{
		$menu = M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->find();

		if ($menu['menuid'] != '') {
			$apiOauth = new apiOauth();
			$access_token = $apiOauth->update_authorizer_access_token($this->wxuser['appid'], $this->wxuser);
			$this->access_token = $access_token;
			$del_url = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=' . $this->access_token;
			$del_data = '{"menuid":"' . $menu['menuid'] . '"}';
			$del_return = json_decode($this->https_request($del_url, $del_data), true);
		}

		M('conditionalmenu')->where(array('token' => $this->token, 'id' => intval($_GET['cid'])))->delete();
		M('conditionalmenu_class')->where(array('token' => $this->token, 'cid' => intval($_GET['cid'])))->delete();
		$this->success('删除成功');
	}

	protected function https_request($url, $data = NULL)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}

?>
