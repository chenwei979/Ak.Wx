<?php 
class ReasonAction extends BackAction{
	function index(){	
		$info=M('Reason')->field(true)->order('id desc')->select();
		$this->assign('info',$info);
		$this->display();
	}
	function addlist(){
		$classname=M('Node')->where(array('name'=>'Reason'))->getField('title');	
		$_POST=$this->_post();
		$_POST['create_time']=time();
		$_POST['classname']=$classname;
		$id=M('Reason')->add($_POST);
		if($id){
			$this->success('添加成功！',U('Reason/index'));
		}else{
			$this->error('添加失败！');
		}
	}
	function upsave(){
		$id=M('Reason')->save($this->_post());
		if($id){
			$this->success('修改成功！',U('Reason/index'));
		}else{
			$this->error('修改失败！');
		}		
	}
	function edit(){
		$id=intval($_GET['id']);
		$info=M('Reason')->where(array('id'=>$id))->field(true)->find();
		$this->assign('info',$info);
		$this->display('add');
	}
	function del(){
		$id=intval($_GET['id']);
		$id=M('Reason')->where(array('id'=>$id))->delete();
		if($id){
			$this->success('删除成功！',U('Reason/index'));
		}else{
			$this->error('删除失败！');
		}
	}
}
















 ?>