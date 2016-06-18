<?php

class MisuoAction extends WapAction {
    
	public function index(){
	   $token=$this->_get('token');
	   $info = M('Misuoreplay')->where(array('token'=>$token))->find();
	   $this->assign('token',$token);
	   $this->assign('info',$info);
	   $this->display();
	}
}