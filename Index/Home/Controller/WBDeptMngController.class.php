<?php
namespace Home\Controller;

class WBDeptMngController extends \Think\Controller {

	/**
	 * 获得部门列表
	 */
	 public function getDeptList()
	 {
//	 	$condition['Enabled'] =1;
		if(isset($_GET['ScoreMngEnabled']) || isset($_POST['ScoreMngEnabled'])) $condition['ScoreMngEnabled'] =1;
		if(isset($_GET['VIPMngEnabled']) || isset($_POST['VIPMngEnabled'])) $condition['VIPMngEnabled'] =1;
		if(isset($_GET['Enabled']) || isset($_POST['Enabled'])) $condition['Enabled'] =1;
		if(isset($_GET['StaffCode']) || isset($_POST['StaffCode'])) 
		{
			$isAdmin = M('staffs',"",getMyCon())->where(array("StaffCode"=>I("StaffCode")))->getField("IsAdmin");

			if(!$isAdmin)
			$condition['_string'] ="deptcode in (select distinct DeptCode from VW_DeptRelPersons where staffcode='" . I("StaffCode")  . "')";
		}
			
	 	$rs = M('depts',"",getMyCon())
	 	->where($condition)
	 	->select();

		return $this -> ajaxReturn($rs);
	 }

	/**
	 * 获得部门列表
	 */
	 public function getDeptSNameList()
	 {
	 	$condition['Enabled'] =1;
		if(isset($_GET['ScoreMngEnabled']) || isset($_POST['ScoreMngEnabled'])) $condition['ScoreMngEnabled'] =1;
		if(isset($_GET['VIPMngEnabled']) || isset($_POST['VIPMngEnabled'])) $condition['VIPMngEnabled'] =1;
		if(isset($_GET['Enabled']) || isset($_POST['Enabled'])) $condition['Enabled'] =1;
		
		if(isset($_GET['StaffCode']) || isset($_POST['StaffCode']))
		$condition['_string'] ="deptcode in (select distinct DeptCode from VW_DeptRelPersons where staffcode='" . I("StaffCode")  . "')";

		
	 	$rs = M('depts',"",getMyCon())
	 	->field('DeptCode as id, DeptSName as value')
	 	->where($condition)
	 	->select();

//		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs0[0]=array('id'=>'all','value'=>'所有');
		$rs=array_merge($rs0,$rs);
		return $this -> ajaxReturn($rs);
	 }

	/**
	 * 获得部门类型
	 */
	 public function getDeptTypeList()
	 {	 	
	 	$rs = M('depts',"",getMyCon())
	 	->field('distinct DeptType as id, DeptType as value')
	 	->select();

		return $this -> ajaxReturn($rs);
	 }
	 
	 /**
	 * 获得部门事件
	 */
	 public function getDeptEvents()
	 {
		switch (I("ShowType")) {
			case 'DeptEvent':
				$condition["DeptCode"] = I("DeptCode");  
				$condition["EventEnabled"] = 1;  
				$rs = M('deptevents',"",getMyCon())
				->join('left join vw_events on deptevents.eventcode = vw_events.eventcode')
			 	->field("deptevents._Identify,deptevents.eventcode,iskeyevent,eventscope,eventtype,event,EventEnabled,Remark, case when DeliveryWay=2 then '下达' else  case when DeliveryWay=1 then '申请'  else '下达|申请' end  end DeliveryWay,ROW_NUMBER() over(order by EventScopeOrder,EventTypeOrder,EventOrder) Ranker")
			 	->where($condition)
			 	->select();
				break;
			
			case 'AllEvent':
			  $condition["EventEnabled"] = 1;  
				$rs = M('vw_events',"",getMyCon())
				->join("left join deptevents on deptevents.eventcode = vw_events.eventcode and DeptCode ='" . I("DeptCode") ."'")
			 	->field("vw_events.eventcode,eventscope,eventtype,event,case when deptevents.eventcode is null then CAST(0 as bit) else CAST(1 as bit) end checked,ROW_NUMBER() over(order by EventScopeOrder,EventTypeOrder,EventOrder) Ranker")
			 	->where("EventScope in('流程外','流程内') and EventEnabled=1")
			 	->select();
			 	//echo M('vw_events')->_sql();
				break;
				
				default:
				break;
		}

		return $this -> ajaxReturn($rs);
	 }
	 
	 /**
	 * 获得部门员工
	 */
	 public function getDeptStaffs()
	 {
		$condition["DeptCode"] = I("DeptCode");
		$condition["RelType"] = array('eq',"部门员工");
		$condition["IsOnJob"] = true;
				
	 	$rs = M('deptstaffs',"",getMyCon())
	 	->join("left join staffs on deptstaffs.staffcode=staffs.staffcode")
	 	->field("deptstaffs._identify,isonjob,deptstaffs.staffcode,staffname,reltype")
		->where($condition)
	 	->select();
		
		return $this -> ajaxReturn($rs);
	 }
	
	/**
	 * 获得部门员工
	 */
	 public function getDeptStaffList()
	 {
		$condition["BelongDeptCode"] = I("DeptCode");
		$condition["IsOnJob"] = true;
	 	$rs = M('staffs',"",getMyCon())
	 	->field("staffcode as id,staffname as value")
		->where($condition)
	 	->select();
		
		return $this -> ajaxReturn($rs);
	 }
	 
		 /**
	 * 获得部门员工
	 */
	 public function getDeptMngers()
	 {
		$condition["DeptCode"] = I("DeptCode");
		$condition["RelType"] = array('neq',"部门员工");
		$condition["IsOnJob"] = true;
	 	$rs = M('deptstaffs',"",getMyCon())
	 	->join("left join staffs on deptstaffs.staffcode=staffs.staffcode")
	 	->field("deptstaffs._identify,isonjob,deptstaffs.staffcode,staffname,reltype")
		->where($condition)
	 	->select();
		
		return $this -> ajaxReturn($rs);
	 } 

	/**
	 * 获得部门目标
	 */
	 public function getDeptGoal()
	 {
	 	$condition["DeptGoals.DeptCode"] = I("DeptCode");
	 	$rs = M('deptgoals',"",getMyCon())
		->join("inner join DeptWage on DeptGoals.DeptCode = DeptWage.DeptCode and WageLevel=1")
		->field("DeptGoals._Identify,DeptGoals.DeptCode,YearMonth,TotalTValueTarget,StaffNum,TValuePerTScore,SingleTScoreTarget,SingleBasicTScore,BasicSalaryPerTScore,StandardMonthGoal,BasicSalary,OrginalBonusRatio")
	 	->where($condition)
		->order("YearMonth desc")
	 	->select();
		return $this -> ajaxReturn($rs);
	 }
	 
	 /**
	 * 获得部门工资
	 */
	 public function getDeptWage()
	 {
		$condition["DeptCode"] = I("DeptCode");
	 	$rs = M('deptwage',"",getMyCon())
	 	->where($condition)
	 	->select();
		return $this -> ajaxReturn($rs);
	 }
	 	  	  	  	   
}
?>