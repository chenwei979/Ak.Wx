<?php
class InviteAction extends WapAction{
	public function index(){
		if (IS_POST) {
			$t_invite = M("Invite_enroll");
			$data['name'] = $this->_post('name');
			$data['email'] = $this->_post('email');
			$data['post'] = $this->_post('post');
			$data['mobile'] = $this->_post('mobile');
			$data['comp'] = $this->_post('comp');
			foreach ($data as $value){
				   if($value==""){
				    echo "<script>alert('�� * �ı�����');location.reload()</script>";
				    exit;
				     }	
				 }
			$data['token'] = $this->token;
			$yid = $data['yid'] = $this->_get('yid');
			$wecha_id = $data['wecha_id'] = $this->wecha_id;
			$list = $t_invite->where(array('wecha_id'=>"$wecha_id",'yid'=>"$yid"))->select();
			
			if ($list) {
				echo "<script>alert('���Ѿ���������');location.reload()</script>";
			}else{
				$ok = $t_invite->add($data);
				if ($ok) {
					echo "<script>alert('�����ɹ���');location.reload()</script>";
				}else{
					echo "<script>alert('�������ύ��Ϣ');location.reload()</script>";	
				}
			}
		}else{
		$token = $this->token;
		$yid = $this->_get('yid');
		$Invite = M("Invite");
		$Invites = $Invite->where(array('token'=>"$token",'id'=>"$yid"))->find();
		$this->assign('Invite',$Invites);

		$User = M("Invite_user");
    	$Users = $User->limit(8)->where(array('token'=>"$token",'yid'=>"$yid"))->select();
		$this->assign('Userlist',$Users);

		$Meet = M("Invite_meeting");
    	//$Meets = $Meet->where(array('token'=>"$token",'yid'=>"$yid"))->order('time')->select();
    	$Meets = $Meet->where(array('token'=>"$token",'yid'=>"$yid"))->order('time asc,ytime asc')->select();
    	$firsttime = $Meet->where(array('token'=>"$token",'yid'=>"$yid"))->order('time')->getField('time');
    	$lasttime = $Meet->where(array('token'=>"$token",'yid'=>"$yid"))->order('time desc')->getField('time');
    	$this->assign('firsttime',$firsttime);
    	$this->assign('lasttime',$lasttime);
		$this->assign('Meetlist',$Meets);

		$Part = M("Invite_partner");
    	$Parts = $Part->where(array('token'=>"$token",'yid'=>"$yid"))->select();
		$this->assign('Partlist',$Parts);

		$this->assign('token',$token);
		$this->assign('yid',$yid);
		$this->display();
	}
}

}