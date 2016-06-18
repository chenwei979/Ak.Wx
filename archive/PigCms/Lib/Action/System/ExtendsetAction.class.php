<?php 
class ExtendsetAction extends BackAction{
	public function index(){
		if($_POST){
			$data['cloose_display']=intval($_POST['cloose_display']);
			session('displayid',null);			
			if($_POST['edditid']){
				$data['id']=intval($_POST['edditid']);
				$id=M('Extendset')->save($data);
				if($id>0){
					$this->success('操作成功！',U('Extendset/index'));exit;
				}else{
					$this->error('操作失败！');
				}				
			}else{
				$id=M('Extendset')->add($data);
				if($id>0){
					$this->success('操作成功！',U('Extendset/index'));exit;
				}else{
					$this->error('操作失败！');
				}								
			}
		}else{
			$info=M('Extendset')->find();
			$this->assign('info',$info);
		}
		$this->display();
	}
	public function sup(){
		$id=intval($_GET['id']);
		session('displayid',$id);
		$this->ajaxReturn(200,'JSON');
	}
	public function clear(){
		session('displayid',null);
		$this->ajaxReturn(200,'JSON');
	}
}








 ?>