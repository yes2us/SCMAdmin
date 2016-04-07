<?php
namespace Home\Controller;

class WBUserMngController extends \Think\Controller {
	
	public function getUserList(){
		$rs = M("buser","",getMyCon())
		->page("1,10000")
		->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	
	public function getUserRole(){
		$condition["UserCode"] = getInputValue("UserCode");
		
		$rs = M("buserrole","",getMyCon())
		->page("1,10000")
		->where($condition)
		->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	
	//获得staff信息：基本资料，部门，角色，相关人，事件
	public function getUserInfo() {
		$dbm = D('StaffObject');

		$condition['StaffCode'] = $_POST['StaffCode'];
		//'Ricky';
		$userObject['mybasic'] = $dbm -> getStaffBasicInfo($condition);
		$userObject['mydepts'] = $dbm -> getStaffRelDepts($condition);
		//$userObject['mydeptwagecfg'] = D('WageObject') -> getStaffDeptWagePolicy($condition);

		$staffcode = $_POST['StaffCode'];
		//'Ricky';		
//		$userObject['mymenus'] = $dbm -> getStaffModules($staffcode);
		$userObject['myroles'] = $dbm -> getStaffRoles($staffcode);
		$userObject['myrelpersons'] = $dbm -> getStaffRelPersons($staffcode);
		$userObject['myevents'] = $dbm -> getStaffEvents($staffcode);

		//		  dump($staffObject);
		return $this -> ajaxReturn($userObject);
	}
	
		public function checkUserPWD() {
			return $this -> ajaxReturn('OK');
			
		$UserID = $_POST['UserID'];
		$PWD = $_POST['PWD'];
		
		$Model = new \Think\Model("",getMyCon());
		$sqlstr = "select pwdcompare('" . $PWD . "',[Password])  cmprs from Staffs where LOWER(StaffCode) ='" . $UserID . "'";
p($sqlstr);
		$rs = $Model -> query($sqlstr);

		
		
		$response = null;
		if (count($rs) > 0) {
			if ($rs[0]['cmprs'] == 1)
				$response = "OK";
		}
		return $this -> ajaxReturn($response);
	}

	public function reviseUserPWD() {
		$UserID = $_POST['UserID'];
		$OldPWD = $_POST['OldPWD'];
		$NewPWD = $_POST['NewPWD'];

		$Model = new \Think\Model("",getMyCon());
		$sqlstr = "select pwdcompare('" . $OldPWD . "',[Password])  cmprs from Staffs where StaffCode ='" . $UserID . "'";

		$rs = $Model -> query($sqlstr);
		$response = null;
		if (count($rs) > 0) {
			if ($rs[0]['cmprs'] == 1) {
				$sqlstr = "update Staffs Set [Password]= pwdencrypt('" . $NewPWD . "')  where StaffCode ='" . $UserID . "'";
				$rs = $Model -> execute($sqlstr);
				$response = "OK";
			}
		}
		return $this -> ajaxReturn($response);
	}
	
}
?>