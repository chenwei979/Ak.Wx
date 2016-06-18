<?php

class GameAction extends WapAction
{
	public $jump_url;

	public function _initialize()
	{
		parent::_initialize();
		$this->game = new game();
		$this->jump_url = $this->game->getServerUrl() . 'index.php?m=Game&c=start&a=index';
	}

	public function jump()
	{
		$data = $this->clear_html($_GET);

		if ($this->isSubscribe()) {
			$data['attention'] = 1;
		}
		else {
			$data['attention'] = 2;
		}

		if (isset($data['code'])) {
			unset($data['code']);
		}

		if (isset($data['state'])) {
			unset($data['state']);
		}

		if (empty($data['url'])) {
			$data['url'] = $_SERVER['SERVER_NAME'];
		}

		if (empty($data['id'])) {
			$data['id'] = $this->_get('id');
		}

		if (empty($data['uid'])) {
			$data['uid'] = $this->_get('uid');
		}

		if (empty($data['ugameid'])) {
			$data['ugameid'] = $this->_get('ugameid');
		}

		if (empty($data['wecha_id'])) {
			$data['wecha_id'] = $this->wecha_id;
		}

		$data['fwid'] = $this->_get('wid');

		if (empty($data['ffans'])) {
			$data['ffans'] = $this->_get('fans_id');
		}

		$jump_url = $this->jump_url . '&' . http_build_query($data);
		header('Location:' . $jump_url);
		exit();
	}

	final public function clear_html($array)
	{
		if (!is_array($array)) {
			return trim(htmlspecialchars($array, ENT_QUOTES));
		}

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$this->clear_html($value);
			}
			else {
				$array[$key] = trim(htmlspecialchars($value, ENT_QUOTES));
			}
		}

		return $array;
	}

	public function link()
	{
		$id = $this->_get('id');
		$wecha_id = $this->wecha_id;
		$siteUrl = $this->_get('siteurl');
		$item = M('Games')->where(array('id' => $id))->find();

		if ($item) {
			$game = new game();
			$url = $game->getLink($item, $wecha_id, $siteUrl);
			header('Location:' . $url);
		}
	}

	public function card()
	{
		$card = M('Cards')->where(array('id' => $this->_get('id')))->find();
		$url = 'http://www.meihua.com/index.php?m=Card&c=index&a=index&unique={unique}&crid={cardid}&usercardid={id}&token={token}';
		$unique = base64_encode($_SERVER['SERVER_NAME'] . '_pigcms_' . $this->token);
		$url = strtr($url, array('{token}' => $this->token, '{id}' => $card['id'], '{unique}' => $unique, '{cardid}' => $card['cardid']));
		header('Location:' . $url);
	}

	public function isAttent()
	{
		$token = htmlspecialchars($_POST['token']);
		$wecha_id = htmlspecialchars($_POST['openid']);

		if (D('Userinfo')->issub($token, $wecha_id)) {
			echo 1;
		}
		else {
			echo 0;
		}
	}
}

?>
