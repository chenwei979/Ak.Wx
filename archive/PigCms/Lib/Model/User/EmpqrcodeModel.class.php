<?php
class EmpqrcodeModel extends Model{
	protected $_validate = array(
			array('empid','require','渠道编号必须填写',1),
			array('empname','require','渠道名称必须填写',1),

	 );
	protected $_auto = array (		
		array('token','getToken',Model:: MODEL_BOTH,'callback'),
	);
	function getToken(){	
		return $_SESSION['token'];
	}
}

?>
