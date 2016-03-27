<?php
namespace Home\Controller;

class WBCURDMngController extends \Think\Controller {

	/**
	 * 获得操作表
	 */
	 private function _CURDOperation($tableName,$DBNo,$POSTData,$attArray,$uniqArray)
	 {

	 	$dbm = M(strtolower($tableName),"",getMyCon($DBNo)); 
	 	switch ($POSTData['webix_operation']) {
			
				case 'insert':				
				
				if($uniqArray && (count($uniqArray)>0))	
				{
					foreach($uniqArray as $uniqatt)  $condition[$uniqatt] = $POSTData[strtolower($uniqatt)];	
					$rs=$dbm->where($condition)->select();
//					setTag('sssql', $dbm->_sql());
					if(count($rs)>0) return "duplicate record";
				}
				
				foreach($attArray as $att) 
				{
					if(isset($POSTData[strtolower($att)])) $data[$att] = $POSTData[strtolower($att)];
				}	

				$recordid = $dbm -> add($data);
				
				//setTag('Sql',$dbm ->_sql());			
				if($recordid<1) return 'fail';
				
				return $recordid;
				break;
				
			case 'update':
				
				foreach($attArray as $att) 
				{
					if(isset($POSTData[strtolower($att)])) $data[$att] = $POSTData[strtolower($att)];
				}	
						
				$saveresult = $dbm->where('_Identify=' . $POSTData['_identify']) ->save($data);
				if($saveresult===false) return "fail";//$saveresult为更新的记录条数，$saveresult=0表示更新数据与原数据没变化
				
				return "success";
				break;
				
			case 'delete':
				
				$saveresult = $dbm->where('_Identify=' . $POSTData['_identify']) ->delete();
				if($saveresult===false) return "fail";//$saveresult为更新的记录条数，$saveresult=0表示更新数据与原数据没变化
				
				return "success";
			break;
				
			default:
				
				break;
		}
	 }

 	  /**
	  * ***************操作debug***************************
	  */
	  public function saveDebugRecord()
	  {
	  	if(stripos("delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
//	  	if(stripos("insert|update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
	  	$tableName = "Debug";
		$attArray = array('Label','Context');
		$uniqArray = nulll;
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	 /**
	  * ***************操作参数表***************************
	  */
	  public function saveParameter()
	  {
	  	if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
	  	$tableName = "SysParameters";
		$attArray = array('Name','Type','VInteger','VFloat','VDate','VBool','VString','VText','Desc');
		$uniqArray = array("Name");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  /**
	  * ***************操作参数表***************************
	  */
	  public function saveSQLCode()
	  {
	  	if(stripos("",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
//	  	if(stripos("insert|update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
	  	$tableName = "SQLList";
		$attArray = array('SQLIndex','SQLCode','Remark','SQLCodeBak','Debug');
		$uniqArray = array("SQLIndex");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  
	/**
	  * ***************操作角色表***************************
	  */  
	  	public function saveRole()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "Roles";
		$attArray = array('RoleName','RoleEnabled','RoleType','RoleDesc');
		$uniqArray = array("RoleName");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqAtt);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	 public function saveRoleStaff()
	  {
	    if(stripos("insert|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "RoleStaffs";
		$attArray = array('RoleName','StaffCode');
		$uniqArray = array('RoleName','StaffCode');
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	   public function saveRoleAuthTask()
	  {
	    if(stripos("insert|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "RoleIncentAuthTask";
		$attArray = array("Enabled","RoleName","YScoreUpLimit","YScoreDownLimit","XScoreUpLimit","XScoreDownLimit",
		"AuthTaskDesc","PosYScoreTaskPerMonth","NegYScoreTaskPerMonth");
		$uniqArray = array("RoleName");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['yscoreuplimit']))  $MyPOST['yscoreuplimit'] = null;
		if(!is_numeric($MyPOST['yscoredownlimit']))  $MyPOST['yscoredownlimit'] = null;
		if(!is_numeric($MyPOST['xscoreuplimit']))  $MyPOST['xscoreuplimit'] = null;
		if(!is_numeric($MyPOST['xscoredownlimit']))  $MyPOST['xscoredownlimit'] = null;
		if(!is_numeric($MyPOST['posyscoretaskpermonth']))  $MyPOST['posyscoretaskpermonth'] = null;
		if(!is_numeric($MyPOST['negyscoretaskpermonth']))  $MyPOST['negyscoretaskpermonth'] = null;
		
		
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  /**
	  * ***************操作事件表***************************
	  */ 
	  	  	public function saveEvent()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "Events";
		$attArray = array('EventCode','Event','EventTypeCode','YScore','Remark','DeliveryWay','Maintainer','EventEnabled');
		$uniqArray = array("EventCode");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['yscore']))  $MyPOST['yscore'] = null;
		
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
		
		//修改eventcode
		if($_POST['webix_operation']=='insert' && $status)
		{
			$_identify = $status;
			$eventcode = 'SJ' . str_pad((string)$_identify, 4, "0", STR_PAD_LEFT);
			M("events")-> where("_Identify=" . $_identify) -> setField('EventCode', $eventcode);
			return $this -> ajaxReturn(array("_identify" => $_identify, "eventcode" => $eventcode));
		}
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	 public function saveEventType()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "EventTypes";
		$attArray = array('EventTypeCode','EventType','EventTypeOrder','EventScopeCode','EventTypeEnabled');
		$uniqArray = array('EventTypeCode');
		
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	   public function saveEventScope()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "EventScopes";
		$attArray = array("EventScopeCode","EventScope","EventScopeOrder","EventScopeEnabled");
		$uniqArray = array("EventScopeCode");
	
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	public function saveEventRecord()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "ScoreRecords";
		$attArray = array("RecordTime","ReviseTime","StaffCode","EventCode","Event","EventType","EventScope","EventRemark",
		"YScore","TValue","ATValue","VTValue","TScore","TSaleQty",
		"TPartner","Transactor","AuditResult","Auditor","AuditDate",
		"AuditState","AppealDate","AppealState","AppealDealer","AppealDealDate","AppealDealRemark","Recorder");
		$uniqArray = null;
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['yscore']))  $MyPOST['yscore'] = null;
		if(!is_numeric($MyPOST['tvalue']))  $MyPOST['tvalue'] = null;
		if(!is_numeric($MyPOST['atvalue']))  $MyPOST['atvalue'] = null;
		if(!is_numeric($MyPOST['vtvalue']))  $MyPOST['vtvalue'] = null;
		if(!is_numeric($MyPOST['tscore']))  $MyPOST['tscore'] = null;
		if(!is_numeric($MyPOST['tsaleqty']))  $MyPOST['tsaleqty'] = null;
		
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
		

		//修改recordcode
		if($_POST['webix_operation']=='insert')
		{
			$_identify = $status;
			$recordcode = 'RC' . str_pad((string)$_identify, 7, "0", STR_PAD_LEFT);
			M("scorerecords")-> where("_Identify=" . $_identify) -> setField('RecordCode', $recordcode);
			return $this -> ajaxReturn(array("_identify" => $_identify, "recordcode" => $recordcode));
		}
				
		return $this -> ajaxReturn($status);
	  }

	  /**
	  * ***************操作部门表***************************
	  */ 
	 public function saveDept()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "Depts";
		$attArray = array('DeptCode','Enabled','ScoreMngEnabled','VIPMngEnabled','DeptName','DeptSName','DeptType','SupDeptCode',
		'TValue2TScoreRate','DeptLevel');
		$uniqArray = array("DeptCode");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['tvalue2tscorerate']))  $MyPOST['tvalue2tscorerate'] = null;
		if(!is_numeric($MyPOST['deptlevel']))  $MyPOST['deptlevel'] = null;
		
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	 public function saveDeptStaff()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "DeptStaffs";
		$attArray = array('DeptCode','StaffCode','RelType');
		$uniqArray = array('DeptCode','StaffCode','RelType');
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	   public function saveDeptEvent()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "DeptEvents";
		$attArray = array("DeptCode","EventCode","IsKeyEvent");
		$uniqArray = array("DeptCode","EventCode");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveDeptGoal()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "DeptGoals";
		$attArray = array("DeptCode","YearMonth","StaffNum",
		"TValuePerTScore","TotalTValueTarget","SingleTScoreTarget","SingleBasicTScore","BasicSalaryPerTScore","StandardMonthGoal");
		$uniqArray = array("DeptCode","YearMonth");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['tvaluepertscore']))  $MyPOST['tvaluepertscore'] = null;
		if(!is_numeric($MyPOST['totaltvaluetarget']))  $MyPOST['totaltvaluetarget'] = null;
		if(!is_numeric($MyPOST['singletscoretarget']))  $MyPOST['singletscoretarget'] = null;
		if(!is_numeric($MyPOST['singlebasictscore']))  $MyPOST['singlebasictscore'] = null;
		if(!is_numeric($MyPOST['basicsalarypertscore']))  $MyPOST['basicsalarypertscore'] = null;
		
		if($MyPOST['staffnum']>1 && $MyPOST['tvaluepertscore']>0 && $MyPOST['totaltvaluetarget']>0)
		{
			$MyPOST['singletscoretarget'] = $MyPOST['totaltvaluetarget']/($_POST['staffnum']-1)/$_POST['tvaluepertscore'];	
			$MyPOST['singletscoretarget'] = round($MyPOST['singletscoretarget'],0);
			
			if($MyPOST['orginalbonusratio']>0 && $MyPOST['standardmonthgoal']>0 && $MyPOST['basicsalary']>0)				
			{
				$MyPOST['basicsalarypertscore'] = $MyPOST['tvaluepertscore']*($MyPOST['orginalbonusratio']+ $MyPOST['basicsalary']*($MyPOST['staffnum']-1)/$MyPOST['standardmonthgoal']);
				$MyPOST['singlebasictscore'] = $MyPOST['basicsalary']/$MyPOST['basicsalarypertscore'];
				
				$MyPOST['basicsalarypertscore'] = round($MyPOST['basicsalarypertscore'],0);
				$MyPOST['singlebasictscore'] = round($MyPOST['singlebasictscore'],0);
			}
		}
				
				
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveDeptWage()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "DeptWage";
		$attArray = array("DeptCode","WageLevel","BasicSalary","DutySalary","OrginalBonusRatio","WageLevelRatios","ScoreLevelRanges");
		$uniqArray = array("DeptCode","WageLevel");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['wagelevel']))  $MyPOST['wagelevel'] = null;
		if(!is_numeric($MyPOST['basicsalary']))  $MyPOST['basicsalary'] = null;
		if(!is_numeric($MyPOST['dutysalary']))  $MyPOST['dutysalary'] = null;

		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  /**
	  * ***************操作部门表***************************
	  */ 
	  public function saveStaff()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "Staffs";
		$attArray = array("StaffCode","IsOnJob","IsLocked","StaffName","IDCardNO","Birthday","MobileNO","WageLevel","PicturePath","BelongDeptCode","DefaultAuditor");
		$uniqArray = array("StaffCode");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
		
		if($_POST['webix_operation']=='insert' || I("isresetpwd"))
		{
			$initPWD = I('DSSuffix') . '123';
			$Model = new \Think\Model('',getMyCon());
			$sqlstr = "update Staffs Set [Password]= pwdencrypt('" . $initPWD . "')  where StaffCode ='" . I('staffcode') . "'";
//			setTag('sqlstr',$sqlstr);
			$rs = $Model -> execute($sqlstr);
			$status = "OK";
		}
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveStaffFixedScore()
	  {
	    if(stripos("insert|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "StaffFixedEvents";
		$attArray = array("StaffCode","EventCode");
		$uniqArray = array("StaffCode","EventCode");
		$status = $this->_CURDOperation($tableName,1,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveStaffSubcriber()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  	setTag("_POST", json_encode($_POST));	  	
	  	$tableName = "StaffSubscribe";
		$attArray = array("SubcriberCode","StaffCode","IsGetYScores","IsGetXScores","IsGetTScores","YScoresLimit","XScoresLimit","TScoresLimit");
		$uniqArray = array("SubcriberCode","StaffCode");
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['yscoreslimit']))  $MyPOST['yscoreslimit'] = null;
		if(!is_numeric($MyPOST['xscoreslimit']))  $MyPOST['xscoreslimit'] = null;
		if(!is_numeric($MyPOST['tscoreslimit']))  $MyPOST['tscoreslimit'] = null;
		
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveStaffAttendance()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "StaffAttendance";
		$attArray = array("RecordTime","StaffCode","AttendanceType",
		"OverTimeHours","OffDays","AbsentDays","AuditDate","AuditorCode","AuditState","Remark");
		$uniqArray = null;
		
		$MyPOST = $_POST;
		if(!is_numeric($MyPOST['overtimehours']))  $MyPOST['overtimehours'] = null;
		if(!is_numeric($MyPOST['offdays']))  $MyPOST['offdays'] = null;
		if(!is_numeric($MyPOST['absentdays']))  $MyPOST['absentdays'] = null;
		
		$status = $this->_CURDOperation($tableName,1,$MyPOST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }

	  
	  /**
	  * ***************操作VIP表***************************
	  */ 
	  	  
	  public function saveStaffMaintainVIP()
	  {

	    if(stripos("update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
		$tableName = "Customers";
		$attArray = array("IsReserved","MaintainerCode");
		$uniqArray = array("CustomerCode");
		
		$status = $this->_CURDOperation($tableName,2, $_POST, $attArray, $uniqArray);		
		
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveVIPInfo()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");

		$tableName = "Customers";
		$attArray = array("IsReserved","CustomerCode","CustomerName","PicturePath","DevDate","PrefSize",
		"MobileNo","Email","QQ","WeChart","Address","PrefContWay","PrefContTime","Birthday","CZBirthday","BodyShape",
		"DressStyle","Careen","Characters","Interest","BizCircle","PrefBuySites","BuyReasons","MateBirthday","ChildBirthday",
		"WeddingAnniDate","WeddingAnniRemark","PrefColor","PrefStyle","FirstBuySceneDesc","OtherPersonalities","MaintainerCode",
		"BelongStoreCode","Remark","NtContDate","NtContContent","AnzImportantLevel","AnzStateLevel","AnzStates","AnzToInvite","PlanInviteDate","PlanInviteContent");
		$uniqArray = array("CustomerCode");
		$status = $this->_CURDOperation($tableName,2,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveVIPContRecord()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
		$tableName = "ContactRecords";
		$attArray = array("CustomerCode","ContDate","MaintainerCode","ContWay","ResponseLevel","ContContent");
		$uniqArray = null;
		$status = $this->_CURDOperation($tableName,2,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }

	  public function saveVIPFitting()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");

		$tableName = "CustomerFittings";
		$attArray = array("CustomerCode","FittingDate","SolutionCode","Scence","FittingAdvisor","ProductColorCode");
		$uniqArray = null;
		$status = $this->_CURDOperation($tableName,2,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  public function saveVIPStore()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  	
		$tableName = "CustomerStore";
		$attArray = array("BuildDate","CustomerCode","ProductColorCode","MaintainerCode","ProfessionalSug");
		$uniqArray = array("BuildDate","CustomerCode","ProductColorCode");
		$status = $this->_CURDOperation($tableName,2,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
}
?>