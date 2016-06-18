<?php

class chat
{
	public $keyword;
	public $my;

	public function __construct($keyword)
	{
		$this->keyword = $keyword;
		$this->my = C('site_my');
	}

	public function index()
	{
		$name = $this->keyword;

		if (!(strpos($name, '你是') === false)) {
			return '咳咳，我是只能微信机器人';
		}

		if (($name == '你叫什么') || ($name == '你是谁')) {
			return '咳咳，我是聪明与智慧并存的美女,人家刚交男朋友,你不可追我啦';
		}
		else if ($name == '糗事') {
			$name = '笑话';
		}

		$url = 'http://liaotian.404.cn/pgicms_api/api.php?key=free&server_key=' . base64_encode(C('server_key')) . '&server_topdomain=' . C('server_topdomain') . '&appid=0&msg=' . urlencode($name);
		$json = $this->curl_get_contents($url);

		if (empty($json)) {
			return '';
		}

		$json = json_decode($json, true);
		$str = str_replace('菲菲', $this->my, str_replace('提示：', $this->my . '提醒您:', str_replace('{br}', "\n", $json['content'])));
		return $str . "\n[我是聊天机器人--" . $this->my . ']';
	}

	public function curl_get_contents($url, $timeout = 3, $options = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if (($options != '') && is_array($options)) {
			foreach ($options as $option => $value) {
				curl_setopt($ch, $option, $value);
			}
		}

		$rt = curl_exec($ch);
		curl_close($ch);
		return $rt;
	}
}

echo "\r\n";

?>
