<?php
class QRCodeAction extends UserAction{
	public function _initialize(){
		parent::_initialize();
		$diymen=M('Diymen_set')->where(array('token'=>$_SESSION['token']))->find();
		if($diymen ==false){
			$this->error('必须审请微信高级接口认证<br />若您已经审请了该接口，请填写高级接口配置文件',U('Set/index'));
			//$this->error('只有微信官方认证的高级服务号才能使用本功能','?g=User&m=Index&a=edit&id='.$this->thisWxUser['id']);
		}
	}
	
	public function index(){
		$post=array();
		if(IS_POST){
			$post['empid']=array('like','%'.$this->_post('empid').'%');
			$post['empname']=array('like','%'.$this->_post('empname').'%');
		}
		$db=D('Empqrcode');
		$where=array('token'=>session('token'));
		$count=$db->where(array_merge($post,$where))->count();
		$page=new Page($count,25);
		$list=$db->where(array_merge($post,$where))->limit($page->firstRow.','.$page->listRows)->order('id asc')->select();
		$this->assign('page',$page->show());
		$this->assign('list',$list);
		$this->assign('empid',$this->_post('empid'));
		$this->assign('empname',$this->_post('empname'));
		$this->display();
	}

	public function add(){
		if(IS_POST){
			$empid=$this->_post('empid','intval');
			if(M('Empqrcode')->where(array(
				'empid'=>$empid,
				'token'=>$this->token
			))->count()>0){
				$this->error('渠道编号已存在');
			}else{
				$this->all_insert('Empqrcode');
			}
		}else{
			$this->display();
		}
	}
	
	public function singleday(){
		$rpttypeid=$this->_post('rpttypeid','intval');
		switch($rpttypeid){
			case 0:
				$rpttype='日';
				break;
			case 1:
				$rpttype='周';
				break;
			case 2:
				$rpttype='月';
				break;
			case 3:
				$rpttype='年';
				break;
			default:
				$rpttype='日';
				break;
		}
		$startdate=date('Y-m-d');
		$enddate=date('Y-m-d',time()+86400);
		if(IS_POST){
			$startdate=$this->_post('startdate');
			$enddate=$this->_post('enddate');
		}
		$datewhere=array(
			'date'=>array(array('egt',$startdate),array('lt',$enddate))
		);
		$db=D('Empqrcode');
		$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
		$data=$db->where($where)->field('empid,empname')->find();
		if($data==false){
			$this->error('查询渠道信息失败');	
		}
		$empid=$data['empid'];
		$empname=$data['empname'];
		$report=D('EmpqrcodeReport');
		$where=array('parentid'=>$this->_get('id'),'type'=>$rpttype,'token'=>session('token'));
		$page=new Page($count,50);
		$list=$report->where(array_merge($where,$datewhere))->field('date,subscribe,unsubscribe')
			->limit($page->firstRow.','.$page->listRows)->order('id desc')
			->select();
		if($data==false){
			$this->error('查询详细信息失败');	
		}
		$this->assign('id',$this->_get('id',intval));
		$this->assign('empid',$empid);
		$this->assign('empname',$empname);
		$this->assign('rpttypeid',$rpttypeid);
		$this->assign('rpttype',$rpttype);
		$this->assign('startdate',$startdate);
		$this->assign('enddate',$enddate);
		$this->assign('page',$page->show());
		$this->assign('list',$list);
		$this->display();
	}
	public function detail(){
		$emp=D('Empqrcode');
		$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
		$data=$emp->where($where)->field('empid,empname')->find();
		if($data==false){
			$this->error('查询渠道信息失败');	
		}
		$empid=$data['empid'];
		$empname=$data['empname'];
		$db=D('EmpqrcodeItem');
		$where=array(
			'parentid'=>$this->_get('id','intval'),
			'token'=>session('token')
		);
		$page=new Page($count,50);
		$list=$db->where($where)->field('openid,wechaname,subscribe,createtime')
			->limit($page->firstRow.','.$page->listRows)->order('id desc')
			->select();
		$this->assign('id',$this->_get('id',intval));
		$this->assign('empid',$empid);
		$this->assign('empname',$empname);
		$this->assign('page',$page->show());
		$this->assign('list',$list);
		$this->display();
	}
	/*
	* 自助获取二维码内容
	*/
	public function getcode(){
		if(IS_POST){
			$token=$this->_post('token');
			$where=array(
				'empid'=>$this->_post('empid','intval'),
				'token'=>$token
			);
			$GetDb=M('Empqrcode');
			//获取内容
			$info=$GetDb->where($where)->field('id,ticket')->find();
			if($info==false){
				$this->error('未查询到渠道编码信息!');	
			}
			if($info['ticket']!=''){
				redirect('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$info['ticket'],2,'二维码已生成,跳转中...');
				//$this->success('二维码已生成');
				//$this->success('二维码已生成','https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$info['ticket']);
			}
			//查询appid appkey是否存在
			$api=M('Diymen_set')->where(array('token'=>$token))->find();
			if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
			//获取微信认证
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.trim($api['appid']).'&secret='.trim($api['appsecret']);
			$json=json_decode($this->curlGet($url_get));
			$qrcode_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$json->access_token;
			$data['action_name']='QR_LIMIT_STR_SCENE';
			$data['action_info']['scene']['scene_str']='qrcodex_'.$info['id'];
			$post=$this->api_notice_increment($qrcode_url,json_encode($data));
			if($post ==false ) $this->error('微信接口返回信息错误，请联系管理员');
			$update=$GetDb->where($where)->save(array('ticket'=>$post));
			if($update !=false){
				$this->success('获取成功','https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$post);
			}else{
				$this->error('操作失败');
			}
		}else{
			$this->assign('token',$this->_get('token'));
			$this->display();	
		}
	}
	public function get_code(){
			$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
			$GetDb=M('Empqrcode');
			//查询appid appkey是否存在
			$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
			if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
			//获取微信认证
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.trim($api['appid']).'&secret='.trim($api['appsecret']);
			$json=json_decode($this->curlGet($url_get));
			$qrcode_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$json->access_token;
			//{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
			$data['action_name']='QR_LIMIT_STR_SCENE';
			$data['action_info']['scene']['scene_str']='qrcodex_'.$this->_get('id','intval');
			$post=$this->api_notice_increment($qrcode_url,json_encode($data));
			if($post ==false ) $this->error('微信接口返回信息错误，请联系管理员');
			$update=$GetDb->where($where)->save(array('ticket'=>$post));
			if($update !=false){
				$this->success('获取成功');
			}else{
				$this->error('操作失败');
			}
	}
	public function del(){
		//这里需要连续删除3张表的数据
		M()->startTrans();
		try{
			//删除主表数据
			$data=M('Empqrcode');
			$where['id']=$this->_get('id','intval');
			if($where['id']==false) $this->error('非法操作');
			$where['token']=$this->token;
			$back=$data->where($where)->delete();
			if($back===false){
				throw new Exception('删除主数据失败'); 
			}
			$del=array(
				'parentid'=>$where['id'],
				'token'=>$where['token']	   
			);
			//删除关注信息数据
			$back=M('EmpqrcodeItem')->where($del)->delete();
			if($back===false){
				throw new Exception('删除关注数据失败'); 
			}
			//删除报表信息数据
			$back=M('EmpqrcodeReport')->where($del)->delete();
			if($back===false){
				throw new Exception('删除报表数据失败'); 
			}
			M()->commit();
			$this->success('操作成功');
		}catch(Exception $e){
			M()->rollback();
			$this->error($e->getMessage());
		}
	}	
	public function status(){
		$data=D('Empqrcode');
		$where['id']=$this->_get('id','intval');
		if($where['id']==false) $this->error('非法操作');
		$where['token']=session('token');
		$type=$this->_get('type','intval');
		if($type==0){
			$back=$data->where($where)->setDec('status');
		}else{
			$back=$data->where($where)->setInc('status');
		}
		if($back==false){
			$this->error('操作失败');
		}else{
			$this->success('操作成功');
		}
	}
	public function test(){
		$data=array(
			'EventKey'=>'qrscene_qrcodex_'		
		);
		$sub=new QRCodeSub($this->token,'hello world',$data);
		$sub->sub();
	}
	function api_notice_increment($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			$this->error('发生错误：curl error'.$errorno);
			
		}else{

			$js=json_decode($tmpInfo,1);
			
			if (!$js['errcode']){
				return $js['ticket'];
			}else {
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
			}
		}
	}
	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
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