<?php 
/**
* 微信wifi
**/
class Dizwifi{
	public $apiurl;
	function __construct(){
		$this->apiurl = 'https://api.weixin.qq.com/bizwifi/';
	}
	//获取wifi门店列表
	public function ShopList($index = 1,$size = 10){
		if((int)$index < 1 || $index == ""){
			return $this->print_error('分页下标不能为空');
		}
		$postData = '{"pageindex":'.$index.',"pagesize":'.$size.'}';
		$extraUrl = $this->apiurl.'shop/list?access_token='.self::getAccessToken();
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//wifi门店具体信息
	public function ShopInfo($shop_id = ''){
		if($shop_id == ''){
			return $this->print_error('门店ID参数错误');
		}
		$extraUrl = $this->apiurl.'shop/get?access_token='.self::getAccessToken();
		$postData = '{"shop_id":'.$shop_id.'}';
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//给门店添加wifi设备
	public function AddDevice($shop_id = '',$ssid = '',$password = ''){
		if($shop_id == "" || $ssid == "" || $password == ""){
			return $this->print_error('参数错误');
		}
		if(strpos($ssid, 'WX') != 0){
			return $this->print_error('无线网络设备的ssid必须是WX开头');
		}
		$extraUrl = $this->apiurl.'device/add?access_token='.self::getAccessToken();
		$postData = '{
 			 "shop_id": '.$shop_id.',
 			 "ssid": "'.$ssid.'",
 			 "password": "'.$password.'"
		}';
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success('给门店添加设备成功');
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//查询设备
	public function ListDevice($shop_id ,$index = 1 ,$size = 10){
		$extraUrl = $this->apiurl.'device/list?access_token='.self::getAccessToken();
		if($shop_id){
			$postData = '{
			   "pageindex": 1,		
			   "pagesize":10,
			   "shop_id":'.$shop_id.'
			}';
		}else{
			$postData = '{
			   "pageindex": '.$index.',		
			   "pagesize":'.$size.'
			}';
		}
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//删除门店下的设备
	public function DeleteDevice($bssid = ''){
		if($bssid == ''){ return $this->print_error('无线网络设备MAC地址必填');}
		$extraUrl = $this->apiurl.'device/delete?access_token='.self::getAccessToken();
		$postData = '{
 			 "bssid":"'.$bssid.'"
		}';
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		//9002017 设备不存在,无法删除
		if($result_array['errcode'] == 0 || $result_array['errcode'] == 9002017){
			return $this->print_success('门店设备删除成功');
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//获取连网的二维码
	public function GetQrcode($shop_id = '',$img_id = 1){
		$img_id = (in_array($img_id, array(0,1))) ? $img_id : 1;
		$extraUrl = $this->apiurl.'qrcode/get?access_token='.self::getAccessToken();
		$postData = '{
			"shop_id": '.$shop_id.',		
			"img_id":'.$img_id.'
		}';
		$result_json = self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']['qrcode_url']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//获取公众号连网URL
	public function GetConnectUrl(){
		$extraUrl = $this->apiurl.'account/get_connecturl?access_token='.self::getAccessToken();
		$result_json = self::postCurl($extraUrl,'','GET');
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']['connect_url']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//设置商家主页
	public function SetHomgpage($shop_id = '', $template_id = 0 ,$url = ''){
		$template_id = (int)$template_id;
		if($template_id == 1 && $url == ''){ return $this->print_error('自定义主页时主页URL不能为空');}
		$post_data = array();
		$post_data['shop_id'] = (int)$shop_id;
		$post_data['template_id'] = $template_id;
		if($template_id == 1){
			$url = html_entity_decode($url);
			$post_data['struct']['url'] = urlencode($url);
		}
		$extraUrl = $this->apiurl.'homepage/set?access_token='.self::getAccessToken();
		$result_json = self::postCurl($extraUrl,json_encode($post_data));
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success('商家主页设置成功');
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//查询商家主页
	public function GetHomepage($shop_id = ''){
		if($shop_id == ''){
			return $this->print_error('门店ID不能为空');
		}
		$extraUrl = $this->apiurl.'homepage/get?access_token='.self::getAccessToken();
		$postData = '{"shop_id":'.$shop_id.'}';
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//设置顶部常驻入口文案
	public function SetBar($shop_id = '' ,$bar_type = ''){
		if($shop_id == '' || $bar_type == ''){
			return $this->print_error('参数错误');
		}
		$extraUrl = $this->apiurl.'bar/set?access_token='.self::getAccessToken();
		$postData = '{
			"shop_id": '.$shop_id.',
			"bar_type": '.$bar_type.'
		}';
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success('主页顶部文案设置成功');
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	//数据统计
	public function StatisticsList($begin_date  = '',$end_date ='' ,$shop_id = '-1'){
		if(strtotime($end_date) - strtotime($begin_date) > 30*24*3600){
			return $this->print_error('最长时间跨度为30天');
		}
		$postData = '{
			"begin_date": "'.$begin_date.'",
			"end_date": "'.$end_date.'",
			"shop_id": '.$shop_id.'
		}';
		$extraUrl = $this->apiurl.'statistics/list?access_token='.self::getAccessToken();
		$result_json =  self::postCurl($extraUrl,$postData);
		$result_array = json_decode($result_json,true);
		if($result_array['errcode'] == 0){
			return $this->print_success($result_array['data']);
		}else{
			return $this->print_error($result_array['errmsg']);
		}
	}
	private static function getAccessToken(){
		$myToken = session('token') ? session('token') : session('wap_token');
		if($myToken == ''){
			return $this->print_error('token获取失败');
		}
		$wxUser = M('Wxuser')->where(array('token'=>$myToken))->field('appid,appsecret')->find();
		if(empty($wxUser['appid'])){
			return $this->print_error('appid获取失败');
		}
		$apiOauth 	= new apiOauth();
		//S($wxUser['appid'],null);
		$Token = $apiOauth->update_authorizer_access_token($wxUser['appid']);
		if($Token){
			return $Token;
		}
	}
	//发送请求
	private static function postCurl($url, $data = null ,$method = 'POST'){
		$ch = curl_init();
		//$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,40);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		if(!empty($data) && $method == 'POST'){
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		$exec = curl_exec($ch);
		if($exec){
			curl_close($ch);
			return $exec;
		}else{
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			curl_close($ch);
			return json_encode(array('errcode'=>$errno,'errmsg'=>$error));
		}
	}
	//错误提示
	private function print_error($errmsg = ''){
		$error_msg = array();
		$error_msg['errcode'] = rand(1000,2000);
		$error_msg['errmsg'] = !empty($errmsg) ? (string)$errmsg : '请求失败';
		return $error_msg;
	}
	//成功提示
	private function print_success($succmsg){
		$succ_msg = array();
		$succ_msg['errcode'] = 0;
		$succ_msg['successmsg'] = $succmsg;
		return $succ_msg;
	}
}
?>
