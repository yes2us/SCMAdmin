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
	
		public function getRawUrl() {
	
		$dbm = M('bsyspara',"",getMyCon());
		$rs = $dbm -> where(array("Name"=> 'DSSuffix')) -> getField('vtext');
		
//		dump($dbm->_sql());
//		setTag('sql1', $dbm->_sql());
				
		return $this -> ajaxReturn($rs);
	}
		
		
	//获得user信息：基本资料，角色，相关人，事件
	public function getUserInfo() {
		$condition["UserCode"] = getInputValue("UserCode","Admin");
		$userObject['MyBasic'] = M('buser',"",getMyCon())->where($condition)->select();
		$userObject['MyRole'] = M('buserrole',"",getMyCon())->where($condition)->select();

		$sqlstr = "select distinct a.UserCode,b.RoleName,c.ParentModuleID,c.ParentModuleName,";
	    $sqlstr = $sqlstr . " b.ModuleID,c.ModuleName,c.ModuleICON,c.ModuleDesc,c.ModuleLevel,";
	    $sqlstr = $sqlstr . " max(b.Operation) as Operation,max(b.Open) as Open";
	    $sqlstr = $sqlstr . " from buserrole as a inner join bprevilege as b on a.RoleName = b.RoleName";
	    $sqlstr = $sqlstr . " inner join vwmodule as c on b.ModuleID = c.ModuleID";
	    $sqlstr = $sqlstr . " where UserCode='" . getInputValue('UserCode','Admin') . "'";
		$sqlstr = $sqlstr . " group by a.UserCode,b.RoleName,c.ParentModuleID,c.ParentModuleName,";
		$sqlstr = $sqlstr . " b.ModuleID,c.ModuleName,c.ModuleICON,c.ModuleDesc,c.ModuleLevel";

	    	$Model = new \Think\Model("","",getMyCon());
		$userObject['MyPrevilege']=$Model->query($sqlstr);
//		dump($userObject);
		return $this -> ajaxReturn($userObject);
	}
	
		public function checkUserPWD() {
//			return $this -> ajaxReturn('OK');
			
		$UserCode = $_POST['UserCode'];
		$PWD = $_POST['PWD'];
		
		$Model = new \Think\Model("",getMyCon());
		$sqlstr = "select SHA('" . $PWD . "')=UserPassword as  cmprs from buser where LOWER(UserCode) ='" . $UserCode . "'";
//		p($sqlstr);
		
		$rs = $Model -> query($sqlstr);

		$response = null;
		if (count($rs) > 0) {
			if ($rs[0]['cmprs'] == 1)
				$response = "OK";
		}
		return $this -> ajaxReturn($response);
	}

	public function reviseUserPWD() {
		$UserCode = $_POST['UserCode'];
		$OldPWD = $_POST['OldPWD'];
		$NewPWD = $_POST['NewPWD'];

		$Model = new \Think\Model("",getMyCon());
		$sqlstr = "select pwdcompare('" . $OldPWD . "',[Password])  cmprs from buser where UserCode ='" . $UserCode . "'";

		$rs = $Model -> query($sqlstr);
		$response = null;
		if (count($rs) > 0) {
			if ($rs[0]['cmprs'] == 1) {
				$sqlstr = "update buser Set [Password]= pwdencrypt('" . $NewPWD . "')  where UserCode ='" . $UserCode . "'";
				$rs = $Model -> execute($sqlstr);
				$response = "OK";
			}
		}
		return $this -> ajaxReturn($response);
	}
	
}
?>