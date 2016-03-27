<?php
namespace Home\Controller;

class WBParaMngController extends \Think\Controller {

	/**
	 * 获得参数
	 */
	 public function getSysPara()
	 {
	 	$rs = M('sysparameters',"",getMyCon())->select();
		return $this -> ajaxReturn($rs);
	 }
	 
	 public function getWBMenu()
	 {
	 	$UserCode = I("UserCode");
		$isAdmin = M("staffs","",getMyCon())->where(array("StaffCode"=>$UserCode))->getField("IsAdmin");
		if($isAdmin)
		{
			$WBMenuType = "wbmenu_admin";
		}
		else 
		{
			$condition['StaffCode'] = $UserCode;
			$condition['RelType'] = array('neq',"部门员工");
			$isMnger = M("deptstaffs","",getMyCon())
			->where($condition)
			->Count("_Identify");
//			dump($isMnger);
			
			if($isMnger>0)
			{
				$WBMenuType = "wbmenu_mnger";
			}
			else
			{
				$WBMenuType = "wbmenu_staffs";
			}
		}

	 	$rs = M('sysparameters',"",getMyCon())->where(array("Name"=>$WBMenuType))->getField("VText");
		echo $rs;
	 }
	 

	 
	 	/**
	 * 获得参数
	 */
	 public function getSQLList()
	 {
	 	$rs = M('sqllist',"",getMyCon())->order("_identify")->select();
		return $this -> ajaxReturn($rs);
	 }
	
	
		 /**
	 * 获得参数
	 */
	 public function getDebugRecord()
	 {
	 	$rs = M('debug',"",getMyCon())->order("_identify desc")->select();
		return $this -> ajaxReturn($rs);
	 }
	 
}
?>