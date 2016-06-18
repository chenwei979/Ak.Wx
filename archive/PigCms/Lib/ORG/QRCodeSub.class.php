<?php
/*
*	员工二维码渠道统计
*	Author:aLoNe.Adams.K # aLoNe IT Develop Studio
*	Createdate:2015-04-01
*/
class QRCodeSub {

	public $token;
	public $wecha_id;
	public $data;
	public $thisWxUser = array();

	public function __construct($token,$wecha_id,$data){
		$this->token      = $token;
		$this->wecha_id   = $wecha_id;
		$this->data		  = $data;
	}
	
	public function sub(){
		//$sceneid现在实际上是Empqrcode的自增id值
		$sceneid=str_replace ( 'qrscene_', '', $this->data ['EventKey'] );
		if ((strpos ( $sceneid, 'qrcodex_' ) === FALSE)) {
			return;//非本功能所指定的参数
		}else{
			$sceneid=str_replace ( 'qrcodex_', '', $sceneid );
		}
		if($sceneid=='') return;
		M()->startTrans();
		try{
			//查询对应的Empqrcode的id
			$emp=M('Empqrcode');
			$id=$emp->where(array(
				'token'=>$this->token,
				'id'=>$sceneid
			))->field('status')->find();
			if($id==false){ //return;
				throw new Exception('查询渠道数据失败<'.$this->token.':'.$sceneid.'>');
			}
			if($id['status']!=1){ //return;//当前禁用该渠道功能
				throw new Exception('当前渠道已被停用<'.$sceneid.'>');
			}
			//查询粉丝昵称
			$user=M('Userinfo');
			$userinfo=$user->where(array(
				'token'=>$this->token,
				'wecha_id'=>$this->wecha_id
			))->field('wechaname')->find();
			if($userinfo==false){ //return;
				throw new Exception('粉丝数据不存在<'.$this->thoken.':'.$this->wecha_id.'>');
			}
			$wechaname=$userinfo['wechaname'];
			//添加关注明细
			$item=M('EmpqrcodeItem');
			$data=array(
				'token'=>$this->token,
				'parentid'=>$sceneid,
				'openid'=>$this->wecha_id,
				'wechaname'=>$wechaname,
				'subscribe'=>true,
				'createtime'=>time()
			);
			$item->add($data);
			//更新员工总数据中关注总数
			$emp->where(array(
				'token'=>$this->token,
				'id'=>$sceneid
			))->setInc('subscribe');
			//更新员工关注报表
			$field='subscribe';
			//处理日报表
			$this->doReport($sceneid,date('Y-m-d'),'日',$field);
			//处理周报表 date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400)); 
			$timestamp=time();
			$this->doReport($sceneid, date('Y-m-d', $timestamp-86400*date('w',$timestamp)
				+(date('w',$timestamp)>0?86400:-518400)),'周',$field);
			//处理月报表
			$this->doReport($sceneid,date('Y-m-01'),'月',$field);
			//处理年报表
			$this->doReport($sceneid,date('Y-01-01'),'年',$field);
			M()->commit();
		}catch(Exception $e){
			M()->rollback();
			file_put_contents('./Conf/logs/Temp/qrcode_error.log\n',$e->getMessage(),FILE_APPEND);
		}
	}

	public function unsub(){
		M()->startTrans();
		try{
			$item=M('EmpqrcodeItem');
			//查询该用户对应的关注用户
			$id=$item->where(array(
				'token'=>$this->token,
				'openid'=>$this->wecha_id
			))->field('parentid,subscribe')->order('id desc')->find();
			if($id==false||$id['subscribe']==false){
				throw new Exception('未查询到用户渠道关注信息<'.$this->token.':'.$this->wecha_id.'>');	
			}
			$sceneid=$id['parentid'];
			//查询粉丝昵称
			$user=M('Userinfo');
			$userinfo=$user->where(array(
				'token'=>$this->token,
				'wecha_id'=>$this->wecha_id
			))->field('wechaname')->find();
			if($userinfo==false){ //return;
				$wechaname='';
			}else{
				$wechaname=$userinfo['wechaname'];
			}
			//添加取消关注明细
			$data=array(
				'token'=>$this->token,
				'parentid'=>$sceneid,
				'openid'=>$this->wecha_id,
				'wechaname'=>$wechaname,
				'subscribe'=>false,
				'createtime'=>time()
			);
			$item->add($data);
			//更新员工总数据中取消关注总数
			$emp=M('Empqrcode');
			$emp->where(array(
				'token'=>$this->token,
				'id'=>$sceneid
			))->setInc('unsubscribe');
			//更新员工关注报表
			$field='unsubscribe';
			//处理日报表
			$this->doReport($sceneid,date('Y-m-d'),'日',$field);
			//处理周报表 date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400)); 
			$timestamp=time();
			$this->doReport($sceneid, date('Y-m-d', $timestamp-86400*date('w',$timestamp)
				+(date('w',$timestamp)>0?86400:-518400)),'周',$field);
			//$this->doReport($sceneid, date('Y-m-d', $timestamp()-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-518400)),'周',$field);
			//处理月报表
			$this->doReport($sceneid,date('Y-m-01'),'月',$field);
			//处理年报表
			$this->doReport($sceneid,date('Y-01-01'),'年',$field);
			M()->commit();
		}catch(Exception $e){
			M()->rollback();
			file_put_contents('./Conf/logs/Temp/qrcode_error.log\n',$e->getMessage(),FILE_APPEND);
		}
	}
	
	private function doReport($id,$date,$type,$field){
		$report=M('EmpqrcodeReport');
		$where=array(
			'token'=>$this->token,
			'parentid'=>$id,
			'date'=>$date,
			'type'=>$type
		);
		$sub=array();
		if($field=='subscribe'){
			$sub['subscribe']=1;
			$sub['unsubscribe']=0;
		}else if($field=='unsubscribe'){
			$sub['subscribe']=0;
			$sub['unsubscribe']=1;
		}
		$count=$report->where($where)->count();
		if($count==0){//无数据，则插入
			$report->add(array_merge($where,$sub));	
		}else{//有数据，则更新
			$report->where($where)->setInc($field);
		}
	}
}
