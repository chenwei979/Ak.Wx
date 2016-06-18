<?php

class FunctionLibrary_Donation
{
	public $sub;
	public $token;

	public function __construct($token, $sub)
	{
		$this->sub = $sub;
		$this->token = $token;
	}

	public function index()
	{
		if (!$this->sub) {
			return array('name' => '募捐', 'subkeywords' => 1, 'sublinks' => 1);
		}
		else {
			$db = M('Donation');
			$where = array('token' => $this->token);
			$items = $db->where($where)->order('id DESC')->select();
			$arr = array(
				'name'        => '募捐',
				'subkeywords' => array(),
				'sublinks'    => array()
				);

			if ($items) {
				foreach ($items as $v) {
					$arr['subkeywords'][$v['id']] = array('name' => $v['name'], 'keyword' => $v['keyword']);
					$arr['sublinks'][$v['id']] = array('name' => $v['name'], 'link' => '{siteUrl}/index.php?g=Wap&m=Donation&a=index&token=' . $this->token . '&wecha_id={wechat_id}&id=' . $v['id']);
				}
			}

			return $arr;
		}
	}
}


?>
