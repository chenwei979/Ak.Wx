<?php
/**
 * User 微医疗
**/
class MedicalAction extends UserAction{

    public function _initialize() {
        parent::_initialize();
        $function=M('Function')->where(array('funname'=>'medical'))->find();
        if (intval($this->user['gid'])<intval($function['gid'])){
            $this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>$this->token)));
        }

            $this->canUseFunction('medical');
    }

    public function index(){
        //echo session('token');
        //exit();
        $tb_reservation = D('Reservation');
        $where = array('token'=>session('token'),'addtype'=>'medical');
        $reslist=$tb_reservation->where($where)->find();
        $_POST['addtype']   =   'medical';
        if(IS_POST){
            $_POST['token']=session('token');
            if($reslist==false){
                if($tb_reservation->create()){
                    if($tb_reservation->add()){
                        $this->success('操作成功');exit;
                    }else{
                        $this->error('操作失败');
                    }
                }else{
                    $this->error($tb_reservation->getError());
                }

            }else{
                $id=(int)$_POST['id'];
                $where = array('token'=>session('token'),'addtype'=>'medical','id'=>$id);
                if($tb_reservation->create()){
                    if($tb_reservation->where($where)->save()!=false){
                        $this->success('操作成功');exit;
                    }else{
                        $this->error('操作失败');
                    }
                }else{
                    $this->error($tb_reservation->getError());
                }
            }
        }else{
            $this->assign('reslist',$reslist);
            $this->display();
        }
    }

    public function aboutus(){

        $t_company = M('Company');
        $token = session('token');
        $where =  array('token'=>$token,'shortname'=>'Medical');
        $check = $t_company->where($where)->find();

        $this->assign('set',$check);

        if(IS_POST){

            if($check == null){

                    if($t_company->add($_POST)){
                        $this->success('添加成功',U('Medical/aboutus',array('token'=>session('token'))));
                        exit;
                    }else{
                        $this->error('服务器繁忙,请稍候再试');exit;
                    }
           }else{
             $wh = array('id'=>$this->_post('id'));

             if($t_company->where($wh)->save($_POST)){
                    $this->success('修改成功',U('Medical/aboutus',array('token'=>session('token'))));
                    exit;
                }else{
                    $this->error('操作失败');exit;
                }
           }
        }

        $this->display();
    }

    public function setIndex(){
        $Photo = M("Photo");
        $photo = $Photo->where(array('token'=>session('token')))->order('id desc')->select();
        $this->assign('photo',$photo);
        $data = D('Medical_set');
        $where = array('token'=>session('token'));
        $classify = M('Classify')->where($where)->field('id as cid,name')->order('id DESC')->select();
        $this->assign('classify',$classify);
        $medicalSet = $data->where($where)->find();

        if(IS_POST){
            if($medicalSet == NULL){
                      if($data->create()!=false){

                        if($id=$data->data($_POST)->add()){
                                $data1['pid']=$id;
                                $data1['module']='medicalSet';
                                $data1['token']=session('token');
                                $data1['keyword']=trim($_POST['keyword']);
                                M('Keyword')->add($data1);
                                $this->success('添加成功',U('Medical/setIndex',array('token'=>session('token'))));
                                 exit;
                        }else{
                            $this->error('添加操作失败,请检查是否有空项,或者重复体提交!');exit;
                        }

                    }else{
                        $this->error($data->getError());exit;
                    }
            }else{
                // change
            $_id =filter_var($this->_post('id'),FILTER_VALIDATE_INT);
            $wh = array('token'=>session('token'),'id'=>(int)$_id);
             if($data->where($wh)->save($_POST)){
                    $data1['pid']=$_id;
                    $data1['module']='medicalSet';
                    $data1['token']=session('token');
                    $da['keyword']=trim($this->_post('keyword'));
                    M('Keyword')->where($data1)->save($da);
                    $this->success('修改成功',U('Medical/setIndex',array('token'=>session('token'))));exit;
                }else{
                    $this->error('修改操作失败,请检查是否有空项,或者重复提交!');exit;
                }
            }
        }else{
          $this->assign('medicalSet',$medicalSet);
           $this->display();
        }
    }

    public function reser_manage(){
        $t_reservebook = M('Medical_user');
        $where = array('token'=>session('token'),'type'=>'medical');
        $count      = $t_reservebook->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $officelist = M('medical_addtype')->where(array('token'=>session('token')))->select();
        $office = array();
        foreach ($officelist as $k => $v) {
           $office[$v['id']] = $v['typename'];
        }
        $books = $t_reservebook->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('iid DESC')->select();
        foreach ($books as $key => $value) {
            if($office[$value['yyks']] != ""){
                 $books[$key]['yyks'] = $office[$value['yyks']];
            }
            if($office[$value['yyzj']] != ""){
              $books[$key]['yyzj'] = $office[$value['yyzj']];
            }
        }
        $this->assign('page',$show);
        $this->assign('books',$books);
        $this->assign('count',$t_reservebook->where($where)->count());
        $this->assign('ok_count',$t_reservebook->where(array('token'=>session('token'),'remate'=>1))->count());
        $this->assign('lose_count',$t_reservebook->where(array('token'=>session('token'),'remate'=>2))->count());
        $this->assign('call_count',$t_reservebook->where(array('token'=>session('token'),'remate'=>0))->count());
        $this->display();
    }

    public function reser_uinfo(){
        $iid = $this->_get('iid');
        $token = $this->_get('token');
        $where = array('iid'=>$iid,'token'=>$token);
        $t_reservebook = M('Medical_user');
        $userinfo = $t_reservebook->where($where)->find();
        $officelist = M('medical_addtype')->where(array('token'=>$token))->select();
        $office = array();
        foreach ($officelist as $k => $v) {
           $office[$v['id']] = $v['typename'];
        }
        if($office[$userinfo['yyks']] != ""){
             $userinfo['yyks'] = $office[$userinfo['yyks']];
        }
        if($office[$userinfo['yyzj']] != ""){
         $userinfo['yyzj'] = $office[$userinfo['yyzj']];
        }
        $this->assign('userinfo',$userinfo);
        if(IS_POST){
            $iid = $this->_post('iid');
            $token = session('token');
            $where =  array('iid'=>$iid,'token'=>$token);
            $ok = $t_reservebook->where($where)->save($_POST);
            if($ok){
                $this->assign('ok',1);
            }else{
                     $this->assign('ok',2);
            }
        }
        $this->display();
    }

     public function manage_del(){
        $iid = $this->_get('iid');
        $t_reservebook = M('Medical_user');
        $where = array('iid'=>$iid,'token'=>$this->_get('token'));
        $check  = $t_reservebook->where($where)->find();
        if(!empty($check)){
                $t_reservebook->where(array('iid'=>$check['iid']))->delete();
                $this->success('删除成功',U('Medical/reser_manage',array('token'=>session('token'))));
                exit;
        }else{
            $this->error('非法操作！');
            exit;
        }
    } 
    //科室主页
    public function officeindex(){
        $token = trim($_GET['token']);
        $where = array('tid'=>0,'token'=>$token);
        $total = M('medical_addtype')->where($where)->count();
        $Page = new Page($total,10);
        $list = M('medical_addtype')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('firstRow',$Page->firstRow);
        $this->assign('token',$token);
        $this->display();
    }
    //添加/修改科室    
    public function officeset(){
        if(IS_POST){
            if($_POST['typename'] == ""){
                $this->error('科室名称不能为空');
            }
            $data = array('typename'=>trim($_POST['typename']),'tid'=>0);
            if($_POST['id']){
                if(M('medical_addtype')->where(array('id'=>(int)$_POST['id']))->save($data)){
                    $this->success('修改科室成功',U('Medical/officeindex',array('token'=>$_POST['token'])));exit;
                }else{
                    $this->error('修改科室失败');
                }
            }else{
                $data['token'] = trim($_POST['token']);
                if($data['token'] == ""){
                    $this->error('参数错误');
                }
                if(M('medical_addtype')->add($data)){
                    $this->success('添加科室成功',U('Medical/officeindex',array('token'=>$_POST['token'])));exit;
                }else{
                    $this->error('添加科室失败');
                }
            }
        }
        $id = (int)$_GET['id'];
        if($id){
            $office = M('medical_addtype')->where(array('id'=>$id))->find();
            $this->assign('set',$office);
        }
        $this->display();
    } 
    //删除科室
    public function officedel(){
        $id = $this->_get('id','intval');
        $token = $this->_get('token','trim');
        if(!$id){
            $this->error('id不能为空');
        }
        $exists = M('medical_addtype')->where(array('id'=>$id))->find();
        if(empty($exists)){
            $this->error('未找到删除项');
        }
        $docotor_count = M('medical_addtype')->where(array('tid'=>$id))->count();
        if($docotor_count > 0){
             $this->error('该科室已安排坐诊专家,请先删除坐诊专家再删除该科室吧');exit;
        }
        $del = M('medical_addtype')->where(array('id'=>$id))->delete();
        if($del){
            $this->success('删除成功',U('Medical/officeindex',array('token'=>$token)));exit;
        }else{
            $this->error('删除失败');exit;
        }
    }
    //专家列表
    public function doctorlist(){
        $tid = (int)$_GET['tid'];
        $token = trim($_GET['token']);
        $officename = trim($_GET['officename']);
        $where = array(C('DB_PREFIX').'medical_addtype.token'=>$token,'tid'=>$tid);
        $total = M('medical_addtype')->where($where)->count();
        $Page = new Page($total,10);
        $list = M('medical_addtype')->where($where)->join(array('LEFT JOIN '.C('DB_PREFIX').'medical_level ON '.C('DB_PREFIX').'medical_level.lid = '.C('DB_PREFIX').'medical_addtype.level'))->limit($Page->firstRow.','.$Page->listRows)->order(C('DB_PREFIX').'medical_addtype.id desc')->select();
       // echo  M('medical_addtype')->getLastSql();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('firstRow',$Page->firstRow);
        $this->assign('token',$token);
        $this->assign('officename',$officename);
        $this->assign('tid',$tid);
        $this->display();
    }
    //添加/修改专家
    public function addtype(){
        if(IS_POST){
            if(trim($_POST['typename']) == ""){
                $this->error('专家名称不能为空');
            }
            if(intval($_POST['level']) == ""){
                $this->error('所属职称不能为空');
            }
            if($_POST['token'] == ""){
                $this->error('参数错误');
            }
            $office = M('medical_addtype')->where(array('id'=>(int)$_POST['tid']))->find();
           if($_POST['tid'] == "" || empty($office)){
                $this->error('所属科室错误');
           }
           $data = array('typename'=>trim($_POST['typename']),'tid'=>(int)$_POST['tid'],'level'=>(int)$_POST['level']);
           if((int)$_POST['id']){
                if(M('medical_addtype')->where(array('id'=>(int)$_POST['id']))->save($data)){
                    //修改duty姓名
                    M('medical_duty')->where(array('did'=>(int)$_POST['id']))->save(array('docter'=>trim($_POST['typename'])));
                    $this->success('修改专家成功',U('Medical/doctorlist',array('tid'=>$office['id'],'officename'=>$office['typename'],'token'=>$_POST['token'])));exit;
                }else{
                    $this->error('修改专家失败');
                }
           }else{
                $data['token'] = trim($_POST['token']);
                 $data['onduty'] = 0;
                if(M('medical_addtype')->add($data)){
                    $this->success('添加专家成功',U('Medical/doctorlist',array('tid'=>$office['id'],'officename'=>$office['typename'],'token'=>$_POST['token'])));exit;
                }else{
                    $this->error('添加专家失败');
                }   
           }
       }
       $id = (int)$_GET['id'];
       $tid = (int)$_GET['tid'];
       $officename = trim($_GET['officename']);
       $token = trim($_GET['token']);
        if($id){
          $docotorinfo = M('medical_addtype')->where(array('id'=>$id))->find();
          $this->assign('set',$docotorinfo);
       }
         $this->assign('tid',$tid);   
       $levellist = M('medical_level')->where(array('token'=>$token))->select(); 
       $this->assign('officename',$officename);         
       $this->assign('level',$levellist);      
       $this->display(); 
    }
    //删除专家
    public function doctordel(){
        $id = $this->_get('id','intval');
        $tid = $this->_get('tid','intval');
        $officename = $this->_get('officename','trim');
        $token = $this->_get('token','trim');
        if(!$id){
            $this->error('id不能为空');
        }
        $exists = M('medical_addtype')->where(array('id'=>$id))->find();
        if(empty($exists)){
            $this->error('未找到删除项');
        }
        $del = M('medical_addtype')->where(array('id'=>$id))->delete();
        if($del){
            $this->success('删除成功',U('Medical/doctorlist',array('tid'=>$tid,'token'=>$token,'officename'=>$officename)));exit;
        }else{
            $this->error('删除失败');exit;
        }
    }
    //
    public function addtypeSava(){
        $pigcms_Medical_addtype=M("Medical_addtype");   
        $id = (int)$_POST['id'];
        $token = trim($_POST['token']);
        if($token == ""){
            $this->error('token不能为空');
         }
        if($id){
            $tid = (int)$_POST['tid'];
            $savedata = array();
            $savedata['typename'] = trim($_POST['typename']);
            if($tid){
                $savedata['tid'] = intval($_POST['tid']);
                $savedata['level'] = intval($_POST['level']);
            }
            if($pigcms_Medical_addtype->where(array('id'=>(int)$_POST['id']))->save($savedata)){
                $this->success('修改成功',U('Medical/typeShow',array('token'=>$token)));exit;
              }else{
                $this->error('修改失败');
              } 
        }else{
            $typename = trim($_POST['typename']);
            if($typename == ""){
                $this->error('科室名称不能为空');
            }
            $adddata = array('typename'=>$typename,'token'=>$token);
            if($pigcms_Medical_addtype->add($adddata)){
                $this->success('添加成功',U('Medical/typeShow',array('token'=>$token)));exit;
            }else{
                $this->error('添加失败');
             } 
        }             
    }
    //
    public function Level(){
        $pigcms_Medical_Level=M("Medical_level");
        $count= $pigcms_Medical_Level->where(array('token'=>trim($_GET['token'])))->count();
        $Page = new Page($count,10);
        $show = $Page->show();    
        $res = $pigcms_Medical_Level->where(array('token'=>trim($_GET['token'])))->limit($Page->firstRow.','.$Page->listRows)->order('lid desc')->select();
        $this->assign('res', $res ); // 赋值数据集
        $this->assign('page', $show);
        $this->assign('firstRow',$Page->firstRow);
        $this->display();   
    }
    public function LevelAdd(){
        $id = (int)$_GET['id'];
        $token = trim($_GET['token']);
        if($id){
            $level = M('medical_level')->where(array('lid'=>$id))->find();
            $this->assign('set',$level);
        }
        $this->assign('token',$token);
        $this->display();   
    }
    public function LevelAdd_save(){
        $pigcms_Medical_LevelAdd=M("Medical_level");       
        $data["level"]=trim($this->_post('level'));
        $data["lprice"]=round($_POST['lprice'],2);
        $data["token"]=trim($this->_post('token'));
        if($data["level"] == ""){
            $this->error('职称名称不能为空');
        }
        if($data["lprice"] <= 0){
            $this->error('预约费用请输入大于0的数字');
        }
        if($_POST['id']){
             if($pigcms_Medical_LevelAdd->where(array('lid'=>(int)$_POST['id']))->save($data)){
                $this->success('修改成功',U('Medical/Level',array('token'=>$data['token'])));exit;
             }else{
                $this->error('修改失败');
             }
        }else{
            if($pigcms_Medical_LevelAdd->add($data)){
                 $this->success('添加成功',U('Medical/Level',array('token'=>$data['token'])));exit;
              }else{
                 $this->error('添加失败');
              }
        }
    }
    public function LevelDel(){
        $id = $this->_get('id','intval');
        $token = $this->_get('token','trim');
        if(!$id){
            $this->error('id不能为空');
        }
        $exists = M('medical_level')->where(array('lid'=>$id))->find();
        if(empty($exists)){
            $this->error('未找到删除项');
        }
        $del = M('medical_level')->where(array('lid'=>$id))->delete();
        if($del){
            $this->success('删除成功',U('Medical/Level',array('token'=>$token)));exit;
        }else{
            $this->error('删除失败');exit;
        }
    }
    public function dutyAdd(){ 
        $token = trim($_GET['token']);   
        $doctor = M("Medical_addtype")->where(array('token'=>$token,'tid'=>array('neq',0),'onduty'=>0))->order('id desc')->select();    
        $id = (int)$_GET['id'];
        if($id){
           $duty = M('medical_duty')->where(array('id'=>$id))->find();
            $this->assign('res',$duty);   
         }
        $this->assign('doctor',$doctor);              
        $this->display();
    }
    public function dutyAddSava(){
        if(IS_POST){
            if($_POST['did'] == ""){
                $this->error('请选择专家');
            }
            if((int)$_POST['resernumber1'] <=0){
                $this->error('上午预约量请输入大于0的整数');
            }
            if((int)$_POST['resernumber2'] <=0){
                $this->error('下午预约量请输入大于0的整数');
            }
            $medical_duty=M("medical_duty");
            $data["docter"] = $this->_post('docter');
            $data["did"] = $this->_post('did');
            $data["week1"] = $_POST['week1'] ? (int)$_POST['week1'] : 13;
            $data["week2"] = $_POST['week2'] ? (int)$_POST['week2'] : 23;
            $data["week3"] = $_POST['week3'] ? (int)$_POST['week3'] : 33;
            $data["week4"] = $_POST['week4'] ? (int)$_POST['week4'] : 43;
            $data["week5"] = $_POST['week5'] ? (int)$_POST['week5'] : 53;
            $data["week6"] = $_POST['week6'] ? (int)$_POST['week6'] : 63;
            $data["week7"] = $_POST['week7'] ? (int)$_POST['week7'] : 73;
            $data["resernumber1"]=$this->_post('resernumber1','intval');
            $data["resernumber2"]=$this->_post('resernumber2','intval');
            $data["intro"]=trim($this->_post('intro'));
            $data["addtime"]=time(); 
            $data['token'] = trim($_POST['token']);
            if($_POST['id']){
                $orginal_data = $medical_duty->where(array('id'=>(int)$_POST['id']))->find();
                if($medical_duty->where(array('id'=>(int)$_POST['id']))->save($data)){
                        if($orginal_data['did'] != $data['did']){
                             M('medical_addtype')->where(array('id'=>(int)$orginal_data['did']))->save(array('onduty'=>0));
                              M('medical_addtype')->where(array('id'=>(int)$data['did']))->save(array('onduty'=>1));
                         }
                        $this->success('修改成功',U('Medical/dutyShow',array('token'=>$_POST['token'])));
                     }else{
                         $this->error('修改失败');
                    } 
            }else{
                if($medical_duty->add($data)){
                    M('medical_addtype')->where(array('id'=>(int)$data["did"]))->save(array('onduty'=>1));
                    $this->success('添加成功',U('Medical/dutyShow',array('token'=>$_POST['token'])));
                 }else{
                     $this->error('添加失败');
                }   
          }
        }                                   
    }
//
    public function dutyShow(){
        $medical_duty=M("Medical_duty");
        $where = array('type'=>'medical','token'=>$this->_get('token'));
        $count = $medical_duty->where($where)->count();
        $Page  = new Page($count,10);
        $show  = $Page->show();
        $res = $medical_duty->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id DESC')->select();
        $this->assign('page',$show);      
        $this->assign('res',$res);                      
        $this->display(); 
    }
    public function dutydel(){
        $id = $this->_get('id','intval');
        $token = $this->_get('token','trim');
        if(!$id){
            $this->error('id不能为空');
        }
        $exists = M('medical_duty')->where(array('id'=>$id))->find();
        if(empty($exists)){
            $this->error('未找到删除项');
        }
        $del = M('medical_duty')->where(array('id'=>$id))->delete();
        if($del){
            M('medical_addtype')->where(array('id'=>$exists['did']))->save(array('onduty'=>0));
            $this->success('删除成功',U('Medical/dutyShow',array('token'=>$token)));exit;
        }else{
            $this->error('删除失败');exit;
        }
    }
    public function detail(){   
        $duty_id = (int)$_GET['duty_id'];
        $duty = M("Medical_duty")->where(array('did'=>$duty_id))->find(); 
        $this->assign('duty',$duty);   
        $this->assign('tid',$_GET['tid']);  
        $this->assign('officename',$_GET['officename']);                     
        $this->display();                           
    }
    public function exExcel(){
        $t_reservebook = M('Medical_user');
        $where = array('token'=>session('token'),'type'=>'medical');
        $officelist = M('medical_addtype')->where(array('token'=>session('token')))->select();
        $office = array();
        foreach ($officelist as $k => $v) {
           $office[$v['id']] = $v['typename'];
        }
        $books = $t_reservebook->where($where)->order('iid DESC')->select();
        foreach ($books as $key => $value) {
            if($office[$value['yyks']] != ""){
                 $books[$key]['yyks'] = $office[$value['yyks']];
            }
            if($office[$value['yyzj']] != ""){
              $books[$key]['yyzj'] = $office[$value['yyzj']];
            }
        }
        $remate_array = array('0'=>'待回复','1'=>'确认','2'=>'拒绝');
        if(!empty($books)){
            $data = array();
            foreach($books as $key=>$val){
                $data[$key]['truename'] = $val['truename'];
                $data[$key]['utel'] = (!empty($val['utel'])) ? $val['utel'] : '---';
                $data[$key]['yyks'] = $val['yyks'];
                $data[$key]['yyzj'] = $val['yyzj'];
                $data[$key]['yybz'] = $val['yybz'];
                $data[$key]['reservation_time'] = date('Y-m-d H:i:s',$val['reservation_time']);
                $data[$key]['orderid'] = '编号:'.$val['orderid'];
                $data[$key]['remate'] = $remate_array[$val['remate']];
            }
            $title = array('姓名','手机号','预约科室','预约专家','预约病种','预约日期','订单号','订单状态');
            $this->exportexcel($data,$title,'预约挂号订单统计_'.date('YmdHis'));
        }else{
            $this->error('导出错误,没有获取要导出的数据');
        }
    }
    //导出
    function exportexcel($data=array(),$title=array(),$filename='medical'){
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)){
            foreach ($title as $k => $v) {
                $title[$k]=iconv("UTF-8", "GBK//IGNORE",$v);
            }
            $title= implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)){
            foreach($data as $key=>$val){
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck]=iconv("UTF-8", "GBK//IGNORE", $cv);
                }
                
                $data[$key]=implode("\t", $data[$key]);
            }
            echo implode("\n",$data);
        }
    }
}
?>