<?php

class ShakeLotteryReply
{
	public $item;
	public $wechat_id;
	public $siteUrl;
	public $token;

	public function __construct($token, $wechat_id, $data, $siteUrl)
	{
		$this->item = M('shakelottery')->where(array('id' => $data['pid']))->find();
		$this->wechat_id = $wechat_id;
		$this->siteUrl = $siteUrl;
		$this->token = $token;
	}

	public function index()
	{
		$thisItem = $this->item;
		$wallopen = D('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->getField('wallopen');
		if (($thisItem['other_source'] == 'scene') && ($wallopen == 0)) {
			return array('请从微现场进入活动', 'text');
		}

		if (strpos($thisItem['reply_pic'], 'http') === false) {
			$thisItem['reply_pic'] = $this->siteUrl . $thisItem['reply_pic'];
		}

		return array(
	array(
		array($thisItem['reply_title'], $thisItem['reply_content'], $thisItem['reply_pic'], $this->siteUrl . U('Wap/ShakeLottery/index', array('id' => $thisItem['id'], 'token' => $this->token, 'wecha_id' => $this->wechat_id)))
		),
	'news'
	);
	}
}


?>
