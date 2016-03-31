<?php
namespace Home\Controller;

class IndexController extends \Think\Controller {

	// 获得最新版本号
	public function getLatestVersion() {
		$response['version'] = '1.5';
		$response['updateUrl'] = 'http://120.24.229.218/app/poa.apk';

		return $this -> ajaxReturn($response);
	}

	// 获得参数
	public function getSysPara() {
		$dbm = M('sysparameters','',getMyCon());
		$rs = $dbm -> where("[desc]='timespan' or name='usexscore'") -> getField('name,vinteger');
		//bdump($rs);
		return $this -> ajaxReturn($rs);
	}

	public function getRawUrl() {
		$DSSuffix = $_POST['DSSuffix'];
//		setTag('DSSuffix', $DSSuffix);
		
		$dbm = M('sysparameters','',getMyCon());
		$rs = $dbm -> where("[name]='" . $DSSuffix . "'") -> getField('vtext');
		//		dump($rs);
//		setTag('sql1', $dbm->_sql());
				
		return $this -> ajaxReturn($rs);
	}

	//获得staff信息：基本资料，部门，角色，相关人，事件
	public function getStaffInfo() {
		$dbm = D('StaffObject');

		$staffcode = getInputValue('StaffCode','47694');
		$condition['StaffCode'] = $staffcode;
		//'Ricky';
		$staffObject['mybasic'] = $dbm -> getStaffBasicInfo($condition);
		$staffObject['mydepts'] = $dbm -> getStaffRelDepts($condition);

		//'Ricky';		
		$staffObject['mymenus'] = $dbm -> getStaffModules($staffcode);
		$staffObject['myroles'] = $dbm -> getStaffRoles($staffcode);
		$staffObject['myrelpersons'] = $dbm -> getStaffRelPersons($staffcode);
		$staffObject['mysubscribe'] = $dbm -> getStaffSubscribe($staffcode);
		$staffObject['myevents'] = $dbm -> getStaffEvents($staffcode);
		$staffObject['myauthtask'] = $dbm -> getStaffAuthTask($staffcode);

		//		  dump($staffObject);
		return $this -> ajaxReturn($staffObject);
	}

	public function saveScoreRecord() {
		//		$data['StaffCode'] = "Ricky"; //前面的要大写与数据库字段一样

		$data = $_POST;
		$data['RecordCode'] = 'XXX';
		$dbm = M('scorerecords','',getMyCon());
		
		$rs_event = M('vw_events','',getMyCon())->where("EventCode='" . $_POST['EventCode'] . "'")->select();
		if(count($rs_event)>0)
		{
			$data['Event'] = $rs_event[0]['event'];
			$data['EventType'] = $rs_event[0]['eventtype'];
			$data['EventScope'] = $rs_event[0]['eventscope'];
		}
		
        if($data['TSaleQty']>2) $data['YScore'] = $data['TSaleQty']-2;

		$_identify = $dbm -> add($data);

		if ($_identify) {
			$recordcode = 'RC' . str_pad((string)$_identify, 7, "0", STR_PAD_LEFT);
			$dbm -> where("_Identify=" . $_identify) -> setField('RecordCode', $recordcode);
			$result['_Identify'] = $_identify;
			return $this -> ajaxReturn(array("_identify" => $_identify, "recordcode" => $recordcode));
		} else {
			return $this -> ajaxReturn('保存失败');
		}
	}
	
	public function checkAuth(){
		$PageId = I("PageId");
		return $this -> ajaxReturn(true);	
	}
	
	//http://zhidao.baidu.com/link?url=F_2DNnBP0PRqlQvZWahvOq7UId2TzD-lHtWqGysBv081w-GVUCBHbdLEDYVpU0suZ8L-dype1RA0V-TMA-Wdia
	public function checkUserPWD() {
		$UserID = $_POST['UserID'];
		$PWD = $_POST['PWD'];
		
		$UserID = trim($UserID);
		$PWD = trim($PWD);
		
		setTag('UserID', $UserID);
		setTag('PWD', $PWD);
		
		$Model = new \Think\Model('',getMyCon());
		$sqlstr = "select pwdcompare('" . $PWD . "',[Password])  cmprs from Staffs where StaffCode ='" . $UserID . "'";

		$rs = $Model -> query($sqlstr);
//		setTag('sql2', $Model->_sql());
		
		
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
		
		$OldPWD = trim($OldPWD);
		$NewPWD = trim($NewPWD);
		

		$Model = new \Think\Model('',getMyCon());
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

	public function savePrefConfig(){
		
		$PrefAuditorName= getInputValue('PrefAuditorName');
		$PrefArbitratorName= getInputValue('PrefArbitratorName');
		$StaffCode = getInputValue('UserID');
			
		$PrefAuditorCode = M('Staffs','',getMyCon())->where("StaffName='" . $PrefAuditorName . "'")->getField("StaffCode");
		$PrefArbitratorCode = M('Staffs','',getMyCon())->where("StaffName='" . $PrefArbitratorName . "'")->getField("StaffCode");
	
		$sqlstring = "update Staffs set PrefAuditorCode='" . $PrefAuditorCode . "',PrefArbitratorCode='" . $PrefArbitratorCode . "' where StaffCode='" . $StaffCode . "'";
		
		$Model = new \Think\Model('',getMyCon());
		$Model->execute($sqlstring);
		
//		if($count>0)
//		{
			return $this -> ajaxReturn('OK');
//		}
//		else
//		{
//			return $this -> ajaxReturn('failed');
//		}
	}
	
	public function getMySimpleStatistics() {

		$dmb = D('ScoreObject');

		$rs = $dmb -> getMyTYDeptRank($_POST['staffcode'], $_POST['deptcode'], $_POST['startdate']);
		//		$rs = $dmb->getMyTYDeptRank('Admin','店铺1','2015-03-01');

		$response = null;
		if (count($rs) > 0) {
			$response = $rs[0];
		}
		return $this -> ajaxReturn($response);
	}

}
?>