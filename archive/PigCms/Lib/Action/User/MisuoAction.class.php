<?php
class MisuoAction extends UserAction{
	

	public function index(){
	    $this->canUseFunction('Misuo');
		$where['token'] = session('token');
		$Cdata = M('Misuoreplay');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['pic'] = strip_tags($_POST['pic']);
			$data['title'] = strip_tags($_POST['title']);
			$data['keyword'] = strip_tags($_POST['keyword']);
			$data['jianjie'] = strip_tags($_POST['jianjie']);
			$data['lj'] = strip_tags($_POST['lj']);
				
			
			$res=$Cdata->where($where)->find();
			        $data1['pid']=$res['id'];
                    $data1['module']='Misuo';
                    $data1['token']=session('token');
                    $data1['keyword']=$_POST['keyword'];
					$where['module']='Misuo';
			if($info){
				$result = M('Misuoreplay')->where($where)->save($data);
				if($result){
					$res=M('Misuoreplay')->where($where)->find();
				    
					$re=M('Keyword')->where(array('module'=>'Misuo','token'=>session('token')))->find();
					if($re){
                    M('keyword')->where($where)->save($data1);
					}else ;
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('Misuoreplay')->add($data);
				$res=$Cdata->where($where)->find();
			        $data1['pid']=$res['id'];
                    $data1['module']='Misuo';
                    $data1['token']=session('token');
                    $data1['keyword']=$_POST['keyword'];
		           
					$where['module']='Misuo';
				$insert1 =M('keyword')->add($data1);
				if($insert > 0){
					$this->success('回复信息添加成功!');
				}else{
					$this->error('回复信息添加失败!');
				}
			}
		}else{
			$this->display();
		}
	
	}
	
	
	
}



?>