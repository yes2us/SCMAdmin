<?php
namespace Home\Controller;

class WBStaffMngController extends \Think\Controller {
	 /**
	 * 获得所有员工
	 */
	 public function getAllStaffs()
	 {
	 	$condition = [];
		
	 	if(isset($_POST['StaffCode']))
		{
			$isAdmin = M('staffs',"",getMyCon())->where(array("StaffCode"=>I("StaffCode")))->getField("IsAdmin");

			if(!$isAdmin)
			$condition['_string']  =" BelongDeptCode in (select distinct DeptCode from VW_DeptRelPersons where staffcode='" . I("StaffCode")  . "')";
		}
					
	 	if(isset($_GET['IsOnJob']))
		{
			$condtion["IsOnJob"] = I("IsOnJob");
			$rs = M('staffs',"",getMyCon())
			->join("left join Depts on Staffs.BelongDeptCode = Depts.DeptCode")
		 	->field("Staffs._Identify,staffcode,staffname,deptsname,BelongDeptCode,IsOnJob,MobileNO,WageLevel,IDCardNO,PicturePath")
		 	->where($condition)
			->order("BelongDeptCode,staffcode")
		 	->select();
			
		}
		else
		{
			$rs = M('staffs',"",getMyCon())
			->join("left join Depts on Staffs.BelongDeptCode = Depts.DeptCode")
		 	->field("Staffs._Identify,staffcode,staffname,deptsname,BelongDeptCode,IsOnJob,MobileNO,WageLevel,IDCardNO,PicturePath")
			->where($condition)
			->order("BelongDeptCode,staffcode")
		 	->select();
//			setTag('sql1', M('staffs')->_sql());
		}
			
		return $this -> ajaxReturn($rs);

	 }

	 /**
	 * 获得指定部门员工的下拉列表
	 */
	 public function getStaffSelectList()
	 {
	 	$condition = null;
	 	if(isset($_POST['DeptCode'])) $condition['DeptCode'] = $_POST['DeptCode'];
	 	if(isset($_POST['RelType']))    $condition['RelType'] = $_POST['RelType'];
		
		$rs = M('deptstaffs',"",getMyCon())
		->join("left join staffs on deptstaffs.staffcode = staffs.staffcode")
		->where($condition)
		->field("distinct deptstaffs.staffcode as id,staffs.staffname value")
		->select();
//		setTag('sql', M('deptstaffs')->_sql());
			
	 	 return $this -> ajaxReturn($rs);
	 }
	 
	 public function getStaffAuditorList()
	 {
	 	 $sqlstring = "";
	 	 $sqlstring = $sqlstring . "select distinct a.DefaultAuditor 'id',b.StaffName 'value'";
	 	 $sqlstring = $sqlstring . " from";
	 	 $sqlstring = $sqlstring . " (	select DefaultAuditor	from Staffs where BelongDeptCode = '" . I("DeptCode") ."' ";
	 	 $sqlstring = $sqlstring . " 	union ";
	 	 $sqlstring = $sqlstring . " 	select StaffCode from DeptStaffs where DeptCode = '" . I("DeptCode") ."' and RelType = '审核人' ";
	 	 $sqlstring = $sqlstring . " )as a  inner join Staffs as b on a.DefaultAuditor = b.StaffCode ";
	
		$rs = M('','',getMyCon())->query($sqlstring);
		return $this -> ajaxReturn($rs);
	 }
	 
	public function getRawUrl() {
		$DSSuffix = $_POST['DSSuffix'];
//		setTag('DSSuffix', $DSSuffix);
		
		$dbm = M('sysparameters',"",getMyCon());
		$rs = $dbm -> where("[name]='" . $DSSuffix . "'") -> getField('vtext');
		//		dump($rs);
//		setTag('sql1', $dbm->_sql());
				
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
		$UserID = $_POST['UserID'];
		$PWD = $_POST['PWD'];
//		setTag('UserID', $UserID);
//		setTag('PWD', $PWD);
		
		$Model = new \Think\Model("",getMyCon());
		$sqlstr = "select pwdcompare('" . $PWD . "',[Password])  cmprs from Staffs where LOWER(StaffCode) ='" . $UserID . "'";

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
	
	 /**
	 * 获得员工的固定奖分
	 */
	 public function getStaffFixedEvent()
	 {
	 	$condition["StaffCode"] = I("StaffCode");  
		
		switch (I("ShowType")) {
			case 'StaffEvent':
			 	$rs = M('stafffixedevents',"",getMyCon())
				->join('left join VW_Events on stafffixedevents.eventcode = VW_Events.eventcode')
			 	->field("stafffixedevents._Identify,StaffCode,stafffixedevents.eventcode,eventscope,eventtype,event,EventEnabled,Remark, case when DeliveryWay=3 then '下达' else  case when DeliveryWay=1 then '申请'  else '下达|申请' end  end DeliveryWay")
			 	->where($condition)
			 	->select();

	 		break;
	 	
	 		case 'AllEvent':
				$rs = M('vw_events',"",getMyCon())
				->join("left join stafffixedevents on stafffixedevents.eventcode = vw_events.eventcode and staffcode ='" . I("StaffCode") ."'")
			 	->field("vw_events._Identify,vw_events.eventcode,eventscope,eventtype,event,case when stafffixedevents.eventcode is null then CAST(0 as bit) else CAST(1 as bit) end checked,ROW_NUMBER() over(order by EventScopeOrder,EventTypeOrder,EventOrder) Ranker, case when DeliveryWay=3 then '下达' else  case when DeliveryWay=1 then '申请'  else '下达|申请' end  end DeliveryWay")
			 	->where("EventScope='固定得分' and EventEnabled=1")
			 	->select();
	 		break;
			
			default:
			break;
		}
		
		return $this -> ajaxReturn($rs);
	 }
	 
	 /**
	 * 获得部门员工
	 */
	 public function getStaffSubcriber()
	 {
		$condition["SubcriberCode"] = I("SubcriberCode");
	 	$rs = M('staffsubscribe',"",getMyCon())
	 	->join("left join Staffs on StaffSubscribe.StaffCode = Staffs.StaffCode")
	 	->field("StaffSubscribe._Identify,StaffSubscribe.StaffCode,StaffName,IsOnJob,IsGetYScores,YScoresLimit,IsGetXScores,XScoresLimit,IsGetTScores,TScoresLimit")
		->where($condition)
	 	->select();
//		setTag('sql', M('staffsubscribe')->_sql());
		return $this -> ajaxReturn($rs);
	 }
	 
	 
}
?>