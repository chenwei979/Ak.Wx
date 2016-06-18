<?php
class FunctionAction extends UserAction{
	public $game;
	public function _initialize(){
		$this->game=new game();
		$this->assign('Game_url',$this->game->getServerUrl());
		parent::_initialize();
	}
	function index(){
		$array = trim($array['id']["uid"]);

		$id=$this->_get('id','intval');

		if (!$id){
			//$token=$this->token;
			$info=M('Wxuser')->find(array('where'=>array('token'=>$this->token)));
		}else {
			$info=M('Wxuser')->find($id);
		}

		$token=$this->_get('token','trim');
		if($info==false||$info['token']!=$token){
			$this->error('非法操作',U('Home/Index/index'));
		}
		$this->redirect("User/Function/welcome",array('token'=>$token,'id'=>$id));exit;

		session('token',$token);
		session('wxid',$info['id']);
		session('companyid', null);
		//第一次登陆　创建　功能所有权
		$token_open=M('Token_open');

		$toback        =$token_open->field('id,queryname')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
		$open['uid']   =session('uid');
		$open['token'] =session('token');
		//遍历功能列表
		if (!C('agent_version')){
			$group=M('User_group')->field('id,name,func')->where('status=1 AND id = '.session('gid'))->order('id ASC')->find();
			$funcs = M('Function')->where("1 = 1")->select();
		}else {
			$group=M('User_group')->field('id,name,func')->where('status=1 AND agentid='.$this->agentid.' AND id = '.session('gid'))->order('id ASC')->find();
			$funcs = M('Agent_function')->where(array('agentid'=>$this->agentid))->select();
		}

		$check=explode(',',trim($toback['queryname'],','));
/*
		foreach ($check as $ck => $cv){
			if(strpos($group['func'],$cv) === false){
				$group['func'] .= ','.$cv;
			}

		}


*/
		$group['func'] = explode(',',$group['func']);

			foreach ($group['func'] as $gk=>$gv){

					$open_func = M('Token_open')->where(array('token'=>session('token'),'uid'=>session('uid')))->getField('queryname');

					if(strpos($open_func,$gv) === false){
						$where = array('funname'=>$gv,'status'=>1);
					}else{
						$where = array('funname'=>$gv);
					}

					if (C('agent_version')&&$this->agentid){
						$where['agentid'] = $this->agentid;
						$group['func'][$gk] = M('Function')->where($where)->field('id,funname,name,info')->find();
					}else{
						$where['status'] = 1;
						$group['func'][$gk] = M('Function')->where($where)->field('id,funname,name,info')->find();
					}

				if($group['func'][$gk] == NULL){
					unset($group['func'][$gk]);
				}
			}


		$this->assign('fun',$group);


		$wecha=M('Wxuser')->field('wxname,wxid,headerpic,weixin')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
		$this->assign('wecha',$wecha);
		$this->assign('token',session('token'));
		$this->assign('check',$check);
		$this->display();
	}

	function welcome(){
		$id=$this->_get('id','intval');
		if (!$id){
			$info=M('Wxuser')->find(array('where'=>array('token'=>$this->token)));
		}else {
			$info=M('Wxuser')->find($id);
		}

		$token=$this->_get('token','trim');

		if($info==false||$info['token']!=$token){
			$this->error('非法操作',U('Home/Index/index'));
		}
		session('companyid', 0);
		session('token',$token);
		session('wxid',$info['id']);
		$wecha=M('Wxuser')->field('wxname,wxid,headerpic,weixin')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
		$this->assign('wecha',$wecha);


		$this->handleKeyword(1,'waphelp','waphelp',1);
		// 模板 0 不让进
		if ($this->usertplid == 0) {
			//$this->error('您需要选择使用新的模板才能进入此页');
			$this->redirect('User/Home/set',array('token'=>$token));exit;
		}

		$data = array();

		$data['mp']     = M('Wxuser')->where(array('uid'=>intval(session('uid'))))->count();
		$data['card']   = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>array('neq','')))->count();
		$data['active'] = M('Lottery')->where(array('token'=>$this->token))->count();
		$data['img']    = M('Img')->where(array('token'=>$this->token))->count();
		$this->assign('data',$data);

			$month=date('m');
			$year=date('Y');

		$this->assign('month',$month);
		$this->assign('year',$year);

		//手写功能名m必须为小写，默认区分大小写
		$uid=session('uid');
		$gid=M('Users')->where("id=$uid")->getField('gid');
		$where['status']=1;
		$where['id']=$gid;
		$func=M('User_group')->where($where)->getField('func');
		$arr_func0=explode(",",strtolower($func));
		//dump($arr_func0);
		//exit;
		$q_funname=M('Function')->field('funname,status')->select();
		foreach($q_funname as $k => $v){
				$q_arr[]=$v['funname'];
				if($v['status']==0){
					$C_not_status[]=$q_funname[$k]['funname'];
				}

		}

		$C=strtolower(implode(',',$C_not_status));
		$not_status=explode(',',$C);
		$arr_func=array_diff($arr_func0,$not_status);//状态没有开启=>没有选中
		$c_site=C('APP_GROUP_LIST');//判断有没有三网通功能,没有三网通功能=>没有勾选对应的PC网站功能
		$group_list=explode(',',$c_site);
		if(!in_array('Web',$group_list)){
			if(in_array('website',$arr_func)){
				foreach($arr_func as $k2=>$v2){
					if($v2=='website'){
						unset($arr_func[$k2]);
					}
				}
			}
		}
		if(!in_array('Fuwu',$group_list)){//判断有没有服务窗功能
			if(in_array('fuwu',$arr_func)){
				foreach($arr_func as $k3=>$v3){
					if($v3=='fuwu'){
						unset($arr_func[$k3]);
					}
				}
			}
		}
		$lower_q_arr=strtolower(implode(',',$q_arr));
		$all_quc=explode(',',$lower_q_arr);
		foreach($arr_func as $k4 => $v4){//判断功能模块被删除，也不能显示（被删除，不在function表中）
			if(!in_array($v4,$all_quc)){
				unset($arr_func[$k4]);
			}
		}
		if(session('role_name') == 'staff'){
			$staff_func = M('company_staff')->where(array('id'=>intval(session('staff_id'))))->getField('func');
			$func_allow = explode(",",strtolower($staff_func));
			$not_exists = array('tianqi','qiushi','jishuan','langdu','jiankang','kuaidi','xiaohua','changtoushi','peiliao','liaotian','mengjian','yuyinfanyi','huoche','gongjiao','shenfenzheng','shouji','yinle','fujin','geci','fanyi','api','suanming','baike','caipiao','Zhida','whois','Fuwu','Assistente');
			if($this->isFuwu){
				unset($not_exists['Fuwu']);
			}
			foreach($arr_func as $key=>$v){
				if(!in_array($v,$not_exists) && in_array($v,$func_allow)){
					$staff[$key] = $v;
				}
			}
			$arr_func = $staff;
		}
	//关注和请求数统计
		$where=array('token'=>$this->token,'month'=>$month,'year'=>$year);
		$list=M('Requestdata')->where($where)->limit(31)->order('id ASC')->select();
		if($list){
			foreach ($list as $k => $v){
				$charts['xAxis']  .= '"'.$v['day'].'日",';
				$charts['follow'] .= '"'.$v['follownum'].'",';
				$charts['text']   .= '"'.$v['textnum'].'",';

			}
		}else{
			for($i=0;$i<30;$i++){
				$charts['xAxis']  .= '"'.($i+1).'日",';
				$charts['follow'] .= '"'.rand(1,100).'",';
				$charts['text']   .= '"'.rand(100,300).'",';

			}
				$charts['ifnull'] = 1;
		}


		function trim_map($val){
			return rtrim($val,',');
		}
		$charts = array_map('trim_map',$charts);
		$this->assign('charts',$charts);

	//粉丝行为数据统计

		$days=7;
		$this->assign('days',$days);
		$modules = R('User/Wechat_behavior/_modules');
		$where=array('token'=>$this->token);
		$where['enddate']=array('gt',time()-$days*24*3600);
		$behaviorDB=M('Behavior');
		$where['model']=array('neq','');
		$items=$behaviorDB->where($where)->order('num DESC')->select();

	if($items){
		$datas=array();
		if ($items){
			foreach ($items as $item){
				$module=strtolower($item['model']);
				if (key_exists($module,$datas)){
					$datas[$module]+=$item['num'];
				}else {
					$datas[$module]=$item['num'];
				}
			}
		}
		asort($datas);

		if ($datas){
			foreach ($datas as $k=>$v){
				if($modules[$k]['name']){
					//$pie['legend'] .= "'".$modules[$k]['name']."',";
					$pie['series'] .= "{value:$v, name:'".$modules[$k]['name']."'},";
				}


			}
		}
		$pie = array_map('trim_map',$pie);
	}else{
		$pie = array(
					'ifnull' => 1,
					'series' => "{value:".rand(1,100).", name:'万能表单'},{value:".rand(1,100).", name:'商城'},{value:".rand(1,100).", name:'全景'},{value:".rand(1,100).", name:'关注'},{value:".rand(1,100).", name:'文本请求'},{value:".rand(1,100).", name:'图文消息'},{value:".rand(1,100).", name:'通用订单'},{value:".rand(1,100).", name:'投票'},{value:".rand(1,100).", name:'婚庆喜帖'},{value:".rand(1,100).", name:'会员卡'},{value:".rand(1,100).", name:'推广活动'}");
	}

		$this->assign('pie',$pie);

	//会员性别统计

	$man = M('Userinfo')->where(array('token'=>$this->token,'sex'=>1))->count();
	$woman = M('Userinfo')->where(array('token'=>$this->token,'sex'=>2))->count();
	$other = M('Userinfo')->where(array('token'=>$this->token,'sex'=>3))->count();

	if($man == 0 && $woman == 0 && $other == 0){
		$man	=	rand(1,50);
		$woman	=	rand(1,100);
		$other	=	rand(1,10);
		$sex_series['ifnull'] = 1;
	}
		$sex_series['series'] = "{value:$man, name:'男'},"."{value:$woman, name:'女'},"."{value:$other, name:'其他'}";


		$this->assign('sex_series',$sex_series);
		$this->assign('token',session('token'));
		$this->display();
	}
	function show(){
		$id=$this->_get('id','intval');
		if (!$id){
			$info=M('Wxuser')->find(array('where'=>array('token'=>$this->token)));
		}else {
			$info=M('Wxuser')->find($id);
		}

		$token=$this->_get('token','trim');

		if($info==false||$info['token']!=$token){
			$this->error('非法操作',U('Home/Index/index'));
		}
		session('companyid', 0);
		session('token',$token);
		session('wxid',$info['id']);
		$wecha=M('Wxuser')->field('wxname,wxid,headerpic,weixin')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
		$this->assign('wecha',$wecha);


		$this->handleKeyword(1,'waphelp','waphelp',1);
		// 模板 0 不让进
		if ($this->usertplid == 0) {
			//$this->error('您需要选择使用新的模板才能进入此页');
			$this->redirect('User/Home/set',array('token'=>$token));exit;
		}
		$this->assign('token',session('token'));
		$this->display();
	}
	function show_ajax(){
		if (S('menus_'.$token)) {
			$menus = S('menus_'.$token);
		}else{
			$menus = S('menus_'.session('token'));
		}
		$class = M('Funclass');
		$classlist = $class->limit(7)->order('id desc')->select();
		$class_name = array();
		foreach ($classlist as $key => $value) {
			$class_id[] = $value['id'];
		}
		$classcount = count($classlist) +2;
		switch ($classcount) {
			case '2':$classwidth = '49.95%'; break;
			case '3':$classwidth = '33.33%'; break;
			case '4':$classwidth = '24.95%'; break;
			case '5':$classwidth = '19.95%'; break;
			case '6':$classwidth = '16.66%'; break;
			case '7':$classwidth = '14.28%'; break;
			case '8':$classwidth = '12.49%'; break;
			default :$classwidth = '11.11%'; break;
		}
		$this->assign('classwidth',$classwidth);
		$this->assign('classlist',$classlist);
		$funcdb = M('Funintro');
		$functions = M('Funclass_holi')->select();
		$cats = $this->game->gameCats();
		if ($this->_get('search')) {
			$what_game = $this->_get('search');
			$game_list = $this->game->gameList($catid,$what_game);
			foreach ($game_list as $key => $value) {
				if (!empty($value['intropic'])) {
					$intropic = explode(",", $value['intropic']);
					foreach ($intropic as $k => $v) {
						$value['rule_pic'] .='<img src="'.$v.'" style="width:48%;margin-right:1%;float:left;margin-top:10px;height: 500px;">';
					}
				}
				$value['cat_id'] = $cats[$value['catid']]['name'];
				$value['game'] = "1";
				$game_list[$key] = $value;
			}
			$where['title'] = array('like','%'.$what_game.'%');
			$where['nick_title'] = array('like','%'.$what_game.'%');
			$where['_logic'] = 'OR';
			$func_list = $funcdb->where($where)->select();
			foreach ($func_list as $key => $value) {
				$value['class'] = explode("||",$value['class']);
				if ($value['holi_id'] > '0') {
					$claaa = M('Funclass_holi')->find($value['holi_id']);
					$value['class'][2] = $claaa['name'];
				}
				$func_list[$key] = $value;
			}
			$classfun = array();
		    foreach ($menus as $key => $value) {
		        foreach ($value['subs'] as $k => $v) {
		            $classfun[str_replace('微信－', '微信-', $v['name'])] = $v['link'];
		        }
		    }
 			foreach($func_list as $key=>$value){
	            if(array_key_exists($value['nick_title'],$classfun)){

	            }else{
	                unset($func_list[$key]);
	            }
	        }
	        if (empty($func_list)) {
	        	$list = $game_list;
	        }else{
	        	$list = array_merge($func_list,$game_list);
	        }
			if (empty($list)) {
				$this->assign('what','1');
			}
		}else{
			if ($this->_get('class') == '0') {
				$game_list = $this->game->gameList($catid,$what_game);
				foreach ($game_list as $key => $value) {
					if (!empty($value['intropic'])) {
						$intropic = explode(",", $value['intropic']);
						foreach ($intropic as $k => $v) {
							$value['rule_pic'] .='<img src="'.$v.'" style="width:48%;margin-right:1%;float:left;margin-top:10px;height: 500px;">';
						}
					}
					$value['cat_id'] = $cats[$value['catid']]['name'];
					$value['game'] = "1";
					$game_list[$key] = $value;
				}
				$func_list = $funcdb->where($where)->select();

				foreach ($func_list as $key => $value) {
					$value['class'] = explode("||",$value['class']);
					if ($value['holi_id'] > '0') {
						$claaa = M('Funclass_holi')->find($value['holi_id']);
						$value['class'][2] = $claaa['name'];
					}
					$func_list[$key] = $value;
				}

				$classfun = array();
			    foreach ($menus as $key => $value) {
			        foreach ($value['subs'] as $k => $v) {
			            $classfun[str_replace('微信－', '微信-', $v['name'])] = $v['link'];
			        }
			    }
			    
	 			foreach($func_list as $key=>$value){
		            if(array_key_exists($value['nick_title'],$classfun)){

		            }else{
		                unset($func_list[$key]);
		            }
		        }
		        if (empty($func_list)) {
		        	$list = $game_list;
		        }else{
		        	$list = array_merge($func_list,$game_list);
		        }

			}else{
				if ($this->_get('class') == 'game') {
					$catid = isset($_GET['catid'])?intval($_GET['catid']):0;
					$this->assign('catid',$catid);
					$list = $this->game->gameList($catid,$what_game);
					foreach ($list as $key => $value) {
						if (!empty($value['intropic'])) {
							$intropic = explode(",", $value['intropic']);
							foreach ($intropic as $k => $v) {
								$value['rule_pic'] .='<img src="'.$v.'" style="width:48%;margin-right:1%;float:left;margin-top:10px;height: 500px;">';
							}
						}
						$value['cat_id'] = $cats[$value['catid']]['name'];
						$list[$key] = $value;
					}
					$count = count($list);
					$this->assign('game',1);
				}else{
					$where['classid']  = array('in',$class_id);
					if ($this->_get('holi_id')) {
						$where['holi_id'] = $this->_get('holi_id');
					}else{
						if ($this->_get('class') !== '') {
							if ($this->_get('class') !== '0') {
								$where['class'] = array('like','%'.$this->_get('class').'%');
							}
						}
					}
					$this->assign('id',$this->_get('id'));
					$list = $funcdb->where($where)->select();
					$count = count($list);
					foreach ($list as $key => $value) {
						$value['class'] = explode("||",$value['class']);
						if ($value['holi_id'] > '0') {
							$claaa = M('Funclass_holi')->find($value['holi_id']);
							$value['class'][2] = $claaa['name'];
						}
						$list[$key] = $value;
					}
					$classfun = array();
				    foreach ($menus as $key => $value) {
				        foreach ($value['subs'] as $k => $v) {
				            $classfun[str_replace('微信－', '微信-', $v['name'])] = $v['link'];
				        }
				    }

				    
			        foreach($list as $key=>$value){
			            if(array_key_exists($value['nick_title'],$classfun)){

			            }else{
			                unset($list[$key]);
			            }
			        }
				    
				}
			}
		}
		if ($this->_get('class') == '') {
			$this->assign('class','0');
		}else{
			$this->assign('class',$this->_get('class'));
		}
		if ($this->_get('p') > 0) {
			$this->assign('game_p',$this->_get('p'));
		}else{
			$this->assign('game_p',1);
		}
		$this->assign('search',$this->_get('search'));
		$this->assign('num',$this->_post('num'));
		$this->assign('tiao_input',$this->_post('tiao_input'));
		$this->assign('classfun',$classfun);
		$this->assign('list',$list);
		$this->assign('cats',$cats);
		$this->assign('functions',$functions);
		$this->display();
	}

}
?>