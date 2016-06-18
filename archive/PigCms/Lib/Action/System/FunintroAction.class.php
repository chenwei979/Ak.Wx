<?php
class FunintroAction extends BackAction{
	public function index(){
		$firstNode=M('Node')->where(array('name'=>'Funintro','title'=>'功能介绍'))->order('id ASC')->find();
		$nodeExist=M('Node')->where(array('pid'=>$firstNode['id']))->find();
		if (!$nodeExist){
			$row2=array(
			'name'=>'add',
			'title'=>'添加',
			'status'=>1,
			'remark'=>'0',
			'pid'=>$firstNode['id'],
			'level'=>3,
			'sort'=>0,
			'display'=>2
			);
			M('Node')->add($row2);
		}
		//
		$map = array('type'=>0);
		$UserDB = D('Funintro');
		$count = $UserDB->where($map)->count();
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$list = $UserDB->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key=>$val){
			$list[$key]['content'] = html_entity_decode($list[$key]['content']);
		}
		$this->assign('list',$list);
		$this->assign('page',$show);// 赋值分页输出
		$pid=$this->_get('pid','intval');
		$db = D('Funclass');
		$where['list']='0';
		$data=$db->where($where)->select();
		$this->assign('data',$data);
		$this->assign('pid',$pid);
		$this->assign('list_agind','1');
		$holi_arr=M('Funclass_holi')->select();
		$this->assign('holi_arr',$holi_arr);
		
		$this->display();


	}
	public function add(){
		// dump($_POST);exit;
		$dbs=M('Funclass');
		$db=D('Funintro');
		$fin=$dbs->where(array('agentid'=>'0'))->find();
		if($fin == ''){
			$this->error('请先添加分类');
		}
		if(IS_POST){
			$pid=$this->_POST('pid','intval');
			$first_kind_id=$this->_POST('first_kind');
			$first_holi_id=$_POST['first_holi'];
			$two_kind_id=$_POST['two_kind'];
			$list = $dbs->where(array('id'=>$first_kind_id))->field('name,list')->find();
			if($first_kind_id == 0){
				$this->error('请选择分类');
			}
			if(!empty($_POST['public_id'])){//勾选公共分类的情况下
				$two_list=$dbs->where(array('id'=>$two_kind_id))->field('name,list')->find();
				if(empty($_POST['two_kind'])){
					$this->error('请选择公共分类的名称！！！');
				}
				if($list['list']==1){//如果是节日营销，必须要选择节日名
					$_POST['class']=$list['name'].'||'.$two_list['name'];
					$_POST['classid']=$two_kind_id;
					$_POST['holi_id']=$first_holi_id;
					$edit_id=$db->add($_POST);
					if($edit_id>0){
						$this->chear_cc();
						$this->success('添加成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}else{
					//在公共分类下，第一个分类必是节日营销栏目
					$this->error('该分类不能同时添加到两个顶级栏目下面！');exit;
				}
			}else{//没有勾选
				if($list['list']==1){//如果是节日营销，必须要选择节日名
					if(empty($first_holi_id)){
						$this->error('请选择对应的二级分类名称');exit;
					}
					$_POST['class']=$list['name'];
					$_POST['classid']=$first_kind_id;
					$_POST['holi_id']=$first_holi_id;
					$_POST['public_id']=0;
					$edit_id=$db->add($_POST);
					if($edit_id>'0'){
						$this->chear_cc();
						$this->success('添加成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}else{
					$_POST['class']=$list['name'];
					$_POST['classid']=$first_kind_id;
					$_POST['holi_id']=0;
					$_POST['public_id']=0;
					$edit_id=$db->add($_POST);
					if($edit_id>'0'){
						$this->chear_cc();
						$this->success('添加成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}
			}
		}else{
			$pid=$this->_GET('pid','intval');
			$funs=D('Funclass');
			$where='';
			$data=$funs->where($where)->select();
			$this->assign('data',$data);
			foreach ($data as $k => $v) {
				if($v['list'] ==1){
					$list_id=$v['id'];
				}
			}
			$this->assign('list_id',$list_id);
			$this->assign('pid',$pid);
			$this->assign('info',array('isnew'=>0));
			
			$list_holi=M('Funclass_holi')->select();
			$this->assign('list_holi',$list_holi);
			$this->assign('agent_id','1');
			
			if(!empty($_SESSION['uid']) && !empty($_SESSION['token'])){
				$this->assign('fun_zw','1');
			}
			$public_arr=M('Funclass')->where(array('list'=>0))->select();
			$this->assign('public_arr',$public_arr);
			$disp=M('Extendset')->limit(1)->field(true)->find();
			$this->assign('displayid',$disp['cloose_display']);
			$this->display();
		}
	}
	public function edit(){
		if(IS_POST){
			$pid=$this->_POST('pid','intval');
			$db=D('Funintro');
			$dbs=D('Funclass');
			$first_kind_id=$this->_POST('first_kind');
			$first_holi_id=$_POST['first_holi'];
			$two_kind_id=$_POST['two_kind'];
			$list = $dbs->where(array('id'=>$first_kind_id))->field('name,list')->find();
			if(empty($first_kind_id)){
				$this->error('请选择分类名称');exit;
			}
			if(!empty($_POST['public_id'])){//勾选公共分类的情况下
				$two_list=$dbs->where(array('id'=>$two_kind_id))->field('name,list')->find();
				if(empty($_POST['two_kind'])){
					$this->error('请选择公共分类的名称！！！');
				}
				if($list['list']==1){//如果是节日营销，必须要选择节日名
					if(empty($first_holi_id)){
						$this->error('请选择对应的二级分类名称');exit;
					}
					$_POST['class']=$list['name'].'||'.$two_list['name'];
					$_POST['classid']=$two_kind_id;
					$_POST['holi_id']=$first_holi_id;
					$edit_id=$db->save($_POST);
					if($edit_id>'0'){
						$this->chear_cc();
						$this->success('修改成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}else{
					$this->error('该分类不能同时添加到两个顶级栏目下面！');exit;
				}
			}else{//没有勾选
				if($list['list']==1){//如果是节日营销，必须要选择节日名
					if(empty($first_holi_id)){
						$this->error('请选择对应的二级分类名称');exit;
					}
					$_POST['class']=$list['name'];
					$_POST['classid']=$first_kind_id;
					$_POST['holi_id']=$first_holi_id;
					$_POST['public_id']=0;
					$edit_id=$db->save($_POST);
					if($edit_id>'0'){
						$this->chear_cc();
						$this->success('修改成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}else{
					$_POST['class']=$list['name'];
					$_POST['classid']=$first_kind_id;
					$_POST['holi_id']=0;
					$_POST['public_id']=0;
					$edit_id=$db->save($_POST);
					if($edit_id>'0'){
						$this->chear_cc();
						$this->success('修改成功',U("Funintro/index",array('pid'=>$pid,'level'=>3)));exit;
					}else{
						$this->error('添加失败！');
					}
				}
			}
		}else{
			$pid=$this->_get('pid','intval');
			$fun=M('Funintro')->where(array('id'=>intval($_GET['id'])))->find();
			
			$list_holi=M('Funclass_holi')->select();
			$this->assign('list_holi',$list_holi);
			$fun_holi=M('Funclass_holi')->where(array('id'=>$fun['holi_id']))->find();
			$classid=$fun_holi['classid'];
			$this->assign('classid',$classid);
			$this->assign('agent_id','1');
			
			$funs=D('Funclass');
			$where='';
			$data=$funs->where($where)->select();
			$this->assign('data',$data);
			$this->assign('info',$fun);
			$this->assign('pid',$pid);
			if(!empty($_SESSION['uid']) && !empty($_SESSION['token'])){
				$this->assign('fun_zw','1');
			}
			foreach ($data as $k => $v) {
				if($v['list'] ==1){
					$list_id=$v['id'];
				}
			}
			$public_arr=$funs->where(array('list'=>0))->select();
			$this->assign('public_arr',$public_arr);
			$this->assign('list_id',$list_id);
			$disp=M('Extendset')->limit(1)->field(true)->find();
			$this->assign('displayid',$disp['cloose_display']);			
			$this->display('add');
		}
	}
	function check_ajax(){
		$first_kind_id=$this->_POST('first_kind');
		$first_holi_id=$_POST['first_holi'];
		$two_kind_id=$_POST['two_kind'];
		if(empty($first_kind_id)){
			$mesage.="请选择分类名称";
			$this->ajaxReturn($mesage,'JSON');exit;
		}
		$db=D('Funintro');
		$dbs=D('Funclass');
		$list = $dbs->where(array('id'=>$first_kind_id))->field('name,list')->find();
		if(!empty($_POST['public_id'])){//勾选公共分类的情况下
			$two_list=$dbs->where(array('id'=>$two_kind_id))->field('name,list')->find();
			if(empty($two_kind_id)){
				$mesage.="请选择公共分类的名称！！！";
				$this->ajaxReturn($mesage,'JSON');exit;
			}
			if($list['list']==1){//如果是节日营销，必须要选择节日名
				if(empty($first_holi_id)){
					$mesage.="请选择对应的二级分类名称";
					$this->ajaxReturn($mesage,'JSON');exit;
				}
				$this->ajaxReturn('200','JSON');exit;
			}else{
				$mesage="该功能不能同时添加到两个顶级栏目下面！";
				$this->ajaxReturn($mesage,'JSON');exit;
			}
		}else{//没有勾选
			if($list['list']==1){//如果是节日营销，必须要选择节日名
				if(empty($first_holi_id)){
					$mesage="请选择对应的二级分类名称";
					$this->ajaxReturn($mesage,'JSON');exit;
				}
				$this->ajaxReturn('200','JSON');exit;
			}else{
				$this->ajaxReturn('200','JSON');exit;
			}
		}

	}
	public function del(){
		if(IS_POST){
			$this->all_save();
		}else{
			$id=$this->_get('id','intval',0);
			if($id==0)$this->error('非法操作');
			$this->assign('tpltitle','编辑');
			$fun=M('Funintro')->where(array('id'=>$id))->delete();
			if($fun==false){
				$this->error('删除失败');
			}else{
				$this->chear_cc();
				$this->success('删除成功');
			}
		}
	}
	public function addclass(){
		$id=$this->_get('pid');
		$this->assign('id',$id);
		$top_arr=M('Funclass')->where(array('list'=>'1'))->select();
		$this->assign('top_arr',$top_arr);
		$list=M('Funclass')->where(array('list'=>'1'))->find();
		$this->assign('list_id',$list['id']);
		if(!empty($_SESSION['uid']) && !empty($_SESSION['token'])){
			$this->assign('fun_zw','1');
		}
		
		$this->assign('agent_id','1');
		
		$this->display();
	}
	public function adds(){
		$db=M('Funclass');
		$id=$this->_post('pid');
		$name=$this->_POST('name');
		$data=$db->where(array('name'=>$name))->find();
		if($data != ''){
			$this->error('已有此分类',U('Funintro/addclass'));exit;
		}
		
		$classid=$_POST['classid'];
		if(!empty($classid)){
			$lss=$db->where(array('id'=>$classid))->field('name,list')->find();
			if($lss['list'] == '1'){
				$_POST['class']=$lss['name'];
				$list_id=M('Funclass_holi')->add($_POST);
				if($list_id){
					$this->chear_cc();
					$this->success('操作成功',U('Funintro/holi_indexs',array('pid'=>$id,'level'=>3)));exit;
				}else{
					$this->error('操作失败',U('Funintro/addclass',array('pid'=>$id,'level'=>3)));exit;
				}
			}
		}
		
		$menu_icon=$_POST['menu_icon'];
		$list_id=$_POST['list_id'];
		if(!empty($list_id)){
			$list_a=M('Funclass')->where(array('list'=>'1'))->select();
			if(!empty($list_a)){
				$this->error('抱歉，目前只能添加一个支持二级分类的项！',U('Funintro/addclass',array('pid'=>$id,'level'=>3)));exit;
			}else{
				$_POST['list']='1';
			}
		}
		$list=$db->add($_POST);
		if($list){
			$this->chear_cc();
			$this->success('操作成功',U('Funintro/indexs',array('pid'=>$id,'level'=>3)));
		}else{
			$this->error('操作失败',U('Funintro/addclass',array('pid'=>$id,'level'=>3)));
		}
	}
	public function indexs(){
		$pid=$this->_get('pid','intval');
		$db=M('Funclass');
		$count=$db->count();
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数
		$show       = $Page->show();// 分页显示输出
		$where='';
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->assign('pid',$pid);
		if(C('server_topdomain') == 'pigcms.cn'){
			$this->assign('list_agind','1');
		}
		$this->display();
	}
	public function upsaves(){
		$id=$this->_POST('id','intval');
		$pid=$this->_POST('pid','intval');
		$list_id=$_POST['list_id'];
		if(empty($_POST['list_id'])){
			$list_id=0;
		}else{
			$list_id=$_POST['list_id'];
		}
		$menu_icon=$_POST['menu_icon'];
		if(!empty($list_id)){
			$list_a=M('Funclass')->where(array('list'=>1))->find();
			if($list_a['id']!=$id){//修改提交的ID和查询到是列表的ID不一致
				if(!empty($list_a)){
					$this->error('抱歉，目前只能添加一个支持二级分类的项！',U('Funintro/indexs',array('pid'=>88,'level'=>3)));exit;
				}
			}
		}
		$_POST['list']=$list_id;//没有将节日表里面的name修改
		$list=M('Funclass')->where(array('id'=>$id))->save($_POST);
		if($list){
			$list_b=M('Funclass')->where(array('id'=>$id))->find();
			if($list_b['list']==1){//该功能为节日营销
				//假如子功能有共享功能
				$cc_arr=M('Funintro')->where("holi_id != 0")->field('id,class,holi_id,public_id')->select();
				foreach($cc_arr as $k=>$v){
					if($v['public_id']!=1){
						$news_data['id']=$v['id'];
						$news_data['class']=$this->_POST('name');
						M('Funintro')->save($news_data);
					}else{
						$class_arr=explode("||", $v['class']);
						$class_arr[0]=$this->_POST('name');
						$new_data['class']=implode("||", $class_arr);
						$new_data['id']=$v['id'];
						M('Funintro')->save($new_data);
					}
				}
			}else{
				$cc_arr=M('Funintro')->where(array('classid'=>$id))->field('id,class,holi_id,public_id')->select();
				foreach($cc_arr as $k=>$v){
					if($v['public_id']!=1){
						$news_data['id']=$v['id'];
						$news_data['class']=$this->_POST('name');
						M('Funintro')->save($news_data);
					}else{
						$class_arr=explode("||", $v['class']);
						$class_arr[1]=$this->_POST('name');
						$new_data['class']=implode("||", $class_arr);
						$new_data['id']=$v['id'];
						M('Funintro')->save($new_data);
					}

				}
			}
			
			$hid_arr=M('Funclass')->where(array('id'=>$id))->find();
			if($hid_arr['list']==1){
				$holi_class['class']=$this->_POST('name');
				$where='';
				M('Funclass_holi')->where($where)->save($holi_class);
			}
			
			$this->chear_cc();
			$this->success('操作成功',U('Funintro/indexs',array('pid'=>$pid,'level'=>3)));
		}else{
			$this->error('操作失败',U('Funintro/edits',array('pid'=>$pid,'id'=>$id)));
		}
	}
	public function edits(){
		$id=$this->_get('pid','intval');
		$pid=$this->_get('id','intval');
		$info=D('Funclass')->where(array('id'=>$pid))->find();
		$this->assign('info',$info);
		$this->assign('id',$id);
		if($info['list']>0){
			$this->assign('check_id',$info['list']);
		}
		if(!empty($_SESSION['uid']) && !empty($_SESSION['token'])){
			$this->assign('fun_zw','1');
		}
		
		$this->assign('agent_id','1');
		
		$this->display('addclass');
	}
	//批量设置分类，以及批量删除功能。
	public function search(){
		if(!empty($_POST['set_kind'])){
			$n=strpos($_POST['class'],',');
			if($n){//批量设置成节日分类
				$l_arr=explode(',',$_POST['class']);
				$test = $this->_POST('test');
				$holi_id= $this->_POST('class');
			    	if($test == 0){
			    		$this->success('请选择分类',U('Funintro/index',array('pid'=>$pid,'level'=>3)));exit;
			    	}
			    	$l_name=D('Funclass_holi')->where(array('id'=>$holi_id))->find();
			    	$data['class']=$l_name['class'];
			    	$data['classid']=$l_name['classid'];
			    	$data['holi_id']=$l_name['id'];
			    	$infor_id = D('Funintro')->where(array('id'=>array('in',$test)))->save($data);
			    	if($infor_id>0){
			    		$this->chear_cc();
			    		$this->success('设置成功！',U('Funintro/index',array('pid'=>$pid,'level'=>3)));
			    	}else{
			    		$this->error('操作失败！',U('Funintro/index',array('pid'=>$pid,'level'=>3)));
			    	}

			}else{
				$db = D('Funintro');
				$pid=$this->_POST('pid');
			    	$test = $this->_POST('test');
			    	$class= $this->_POST('class');
			    	$dbs=D('Funclass');
			    	$l_name=$dbs->where(array('id'=>$class))->field('name,list')->find();
			  	$where['id']= array('in',$test);
			    	if($test == 0){
			    		$this->success('请选择分类',U('Funintro/index',array('pid'=>$pid,'level'=>3)));exit;
			    	}
			    	if($l_name['list'] != '1'){
			    		$data['holi_id']='0';
			    	}
			    	$data['classid']=$class;
			    	$data['class']=$l_name['name'];
			    	$data['public_id']=0;
			    	$list = $db->where($where)->save($data);
			    	if($list){
			    		$this->chear_cc();
			    		$this->success('设置成功！',U('Funintro/index',array('pid'=>$pid,'level'=>3)));
			    	}else{
			    		$this->error('操作失败！',U('Funintro/index',array('pid'=>$pid,'level'=>3)));
			    	}
			}

		}else{
			$test = $this->_POST('test');
			$db = D('Funintro');
			if(empty($test)){
				$this->error('请选择删除选项！！！');
			}
			$del_count=$db->where(array('id'=>array('in',$test)))->delete();
			if($del_count > 0){
				$this->chear_cc();
				$this->success('批量删除成功！',U('Funintro/index',array('pid'=>88,'level'=>3)));
			}else{
				$this->error('批量删除失败！！！');
			}
		}
	}
	public function dels(){
		$id=$this->_get('id','intval',0);
		if($id==0)$this->error('非法操作');
		$list=M('Funintro')->field('id,holi_id,classid')->select();
		$list_id=M('Funclass')->where(array('id'=>$id))->getField('list');
		if($list_id == 1){
			foreach($list as $k=>$v){
				if($v['holi_id']!=0){
					$k_arr[]=$v['id'];
				}
			}

		}else{
			foreach ($list as $k => $v) {
				if($v['classid']==$id){
					$k_arr[]=$v['id'];
				}
			}
		}
		if(!empty($k_arr)){
			$kkk=M('Funintro')->where(array('id'=>array('in',$k_arr)))->delete();
			if($kkk>0){
				$mesage.='删除栏目下功能成功！';
			}else{
				$mesage.='删除栏目下功能失败！';
			}
		}
		
		$list_arr=M('Funclass')->where(array('id'=>$id))->find();//如果删除的顶级栏目有一个是节日营销栏目，子栏目也要一起删除
		$count_holi=M('Funclass_holi')->count();
		if($list_arr['list']==1 && $count_holi>0){
			$kkk_id=M('Funclass_holi')->where(array('classid'=>$id))->delete();
			if($kkk_id>0){
				$mesage.='删除子分类成功！';
			}else{
				$mesage.='删除子分类失败！';
			}
		}
		
		$fun=M('Funclass')->where(array('id'=>$id))->delete();
		if($fun>0){
			$mesage.='删除顶级分类成功！';
		}else{
			$mesage.='删除顶级分类失败！';
		}
		$this->chear_cc();
		$this->success($mesage,U('Funintro/indexs',array('pid'=>88,'level'=>3)));
	}
	public function bath_del(){
		$test=$_POST['test'];
		if(empty($test)){
			$this->error('请选择需要删除的栏目！！！');exit;
		}
		$fun=M('Funintro')->field('id,holi_id,classid')->select();
		$list_arr_holi=M('Funclass')->where(array('id'=>array('in',$test)))->field('id,list')->select();
		foreach($list_arr_holi as $k=>$v){
			if($v['list']==1){
				$list_id_holi=$v['id'];
			}
		}
		if(!empty($list_id_holi)){//classid不一定是节日营销
			foreach($fun as $k => $v){
				if($v['holi_id'] != 0){
					$ss_arr[]=$v['id'];
				}
				if(in_array($v['classid'], $test)){
					$bb_arr[]=$v['id'];
				}
			}
				if(!empty($ss_arr)){
					$list_id=M('Funintro')->where(array('id'=>array('in',$ss_arr)))->delete();
				}
				if(!empty($bb_arr)){
					$bb_list_id=M('Funintro')->where(array('id'=>array('in',$bb_arr)))->delete();
				}
				if($list_id>0 || $bb_list_id>0){
					$mesage.='批量删除功能成功！';
				}else{
					$mesage.='批量删除功能失败！';
				}
		}else{
			foreach ($fun as $k => $v) {
				if(in_array($v['classid'], $test)){
					$k_arr[]=$v['id'];
				}
			}
			if(!empty($k_arr)){
				$list_id=M('Funintro')->where(array('id'=>array('in',$k_arr)))->delete();
				if($list_id>0){
					$mesage.='批量删除功能成功！';
				}else{
					$mesage.='批量删除功能失败！';
				}
			}
		}
		
		

		
		$del_id=M('Funclass')->where(array('id'=>array('in',$test)))->delete();
		if($del_id>0){
			$mesage.='批量删除顶级分类成功！';
		}else{
			$mesage.='批量删除顶级分类失败！';
		}
		$this->chear_cc();
		$this->success($mesage,U('Funintro/indexs',array('pid'=>88,'level'=>3)));
	}
	public function holi_dels(){
		$id=$this->_get('id','intval',0);
		if($id==0)$this->error('非法操作');
		$fun=M('Funclass_holi')->where(array('id'=>$id))->delete();
		$list=M('Funintro')->field('id,holi_id')->select();
		foreach ($list as $k => $v) {
			if($v['holi_id']==$id){
				$k_arr[]=$v['id'];
			}
		}
		if(empty($k_arr)){
			if($fun>0){
				$this->chear_cc();
				$this->success('删除成功！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));exit;
			}else{
				$this->error('删除失败！');
			}
		}else{
			$kkk=M('Funintro')->where(array('id'=>array('in',$k_arr)))->delete();
			if($kkk>0){
				$this->chear_cc();
				$this->success('删除成功！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));exit;
			}else{
				$this->error('删除失败！');
			}
		}
	}
	public function holi_dels_s(){
		$test=$_POST['test'];
		if(empty($test)){
			$this->error('请选择需要删除的分类！');exit;
		}
		$del_id=M('Funclass_holi')->where(array('id'=>array('in',$test)))->delete();
		$fun=M('Funintro')->field('id,holi_id')->select();
		foreach ($fun as $k => $v) {
			if(in_array($v['holi_id'], $test)){
				$k_arr[]=$v['id'];
			}
		}
		if(empty($k_arr)){
			if($del_id>0){
				$this->chear_cc();
				$this->success('批量删除成功！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));exit;
			}else{
				$this->error('批量删除失败！');
			}
		}else{
			$list_id=M('Funintro')->where(array('id'=>array('in',$k_arr)))->delete();
			if($list_id>0 && $del_id>0){
				$this->chear_cc();
				$this->success('批量删除成功！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));exit;
			}else{
				$this->error('批量删除失败！');
			}
		}
	}
	public function holi_edits(){
		if($_POST){
			$n_id=M('Funclass_holi')->save($_POST);
			if($n_id>0){
				$this->chear_cc();
				$this->success('修改成功！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));
			}else{
				$this->error('修改失败！',U('Funintro/holi_indexs',array('pid'=>88,'level'=>3)));
			}
		}else{
			$pid=$this->_get('id','intval');
			$info=D('Funclass_holi')->where(array('id'=>$pid))->find();
			$this->assign('info',$info);
			$top_arr=M('Funclass')->where(array('list'=>'1'))->find();
			$this->assign('top_arr',$top_arr);
			$this->display('holi_edits');
		}
	}
	public function holi_indexs(){
		$list_holi=M('Funclass_holi')->field('id,name')->order("id desc")->select();
		$this->assign('list_holi',$list_holi);
		if(C('server_topdomain') == 'pigcms.cn'){
			$this->assign('list_agind','1');
		}
		$this->display();
	}
	function link_ajax(){
		$id=$_POST['id'];
		if($id>'0'){
			if(!empty($_SESSION['uid']) && !empty($_SESSION['token']) ){
				$this->ajaxReturn('1','JSON');
			}else{
				$this->ajaxReturn('0','JSON');
			}
		}
	}
	public function chear_cc(){
		S('home_arr',null);//内页内容全部缓存
		$home_arr=M('Funintro')->order("id desc")->select();
		S('home_arr',$home_arr,3600);

		S('top_arr',null);//第一层一级导航缓存
		$top_arr=M('Funclass')->order("id desc")->select();
		S('top_arr',$top_arr,3600);

		
		S('top_jieri',null);//第三层节日导航缓存
		$top_jieri=M('Funclass_holi')->order("id desc")->select();
		S('top_jieri',$top_jieri,3600);
		
	}
}
?>