<?php
namespace Home\Controller;

class SettingController extends \Think\Controller {

	public function getDeptStaff() {
		$dbm = D('BasicObject');

		$deptcode = getInputValue('DeptCode');
		$rs = $dbm -> getDeptStaffs($deptcode);

		//		setTag('rs', json_encode($rs));

		return $this -> ajaxReturn($rs);
	}

	public function getDeptMember(){
		$dbm = D('BasicObject');

		$deptcode = getInputValue('DeptCode');
		$rs = $dbm -> getDeptMembers($deptcode);

		//		setTag('rs', json_encode($rs));

		return $this -> ajaxReturn($rs);
	}
	
	
	public function getDeptGoal() {
		$dbm = D('BasicObject');

		$deptcode = getInputValue('DeptCode');
		$rs = $dbm -> getDeptGoals($deptcode);

		//		setTag('rs', json_encode($rs));

		return $this -> ajaxReturn($rs);
	}


	public function saveStaff() {	
		
		$dbm = M('staffs',"",getMyCon());
				
		$data['StaffCode'] = getInputValue('StaffCode','xxx');
		$data['StaffName'] = getInputValue('StaffName','XXX');
		$data['BelongDeptCode'] = getInputValue('BelongDeptCode','10025');
		$data['WageLevel'] = 1;
		$data['IsOnJob'] = 1;
		$data['IsLock'] = 0;
		
		$StaffType= getInputValue('StaffType');
		if($StaffType=='店长')
		{
			$data['WageLevel'] = 2;
		}
		
	
			$count=$dbm->where(array('StaffCode'=>$data['StaffCode']))->count();
			
		if($count>0)
		{
			return $this->ajaxReturn('Fail');//该帐号已经存在
		}	
		else
		{
			$dbm -> data($data) -> add();
			$initPWD = getInputValue('DSSuffix','eekabsc.com') . '123';
			$Model = new \Think\Model('',getMyCon());
			$sqlstr = "update Staffs Set [Password]= pwdencrypt('" . $initPWD . "'),";
			$sqlstr = $sqlstr . " PrefAuditorCode=(select max(StaffCode) from DeptStaffs where DeptCode='" . $data['BelongDeptCode'] . "' and RelType = '店长') ";
			$sqlstr = $sqlstr . " where StaffCode ='" . $data['StaffCode'] . "'";
//			setTag('sqlstr',$sqlstr);
			$rs = $Model -> execute($sqlstr);
			
			return $this -> ajaxReturn('保存成功');
		}	
	}


	public function saveYearMonthGoal() {	
		
		$dbm = M('deptgoals',"",getMyCon());
				
		$data['DeptCode'] = getInputValue('DeptCode','10024');
		$data['YearMonth'] = getInputValue('YearMonth','2016-07');
		$data['TValuePerTScore'] = 1000;
		if($_POST['StaffNum'])	$data['StaffNum'] = getInputValue('StaffNum',6);
		$data['TotalTValueTarget'] = getInputValue('TotalTValueTarget',30000);
		
		$count=$dbm->where(array('DeptCode'=>$data['DeptCode'],'YearMonth'=>$data['YearMonth']))->count();
			
		if($count>0)
		{
			$dbm -> where(array('DeptCode'=>$data['DeptCode'],'YearMonth'=>$data['YearMonth'])) -> save($data);
			return $this->ajaxReturn('UpdateOK');
		}	
		else
		{
			$dbm -> data($data) -> add();
			return $this -> ajaxReturn('SaveOK');
		}	
	}

	public function saveSubscriber() {	
		
		$dbm = M('staffsubscribe',"",getMyCon());
				
		$data['SubcriberCode'] = getInputValue('StaffCode');
		foreach($_POST['Subscriber'] as $item) 
		{
			$data['StaffCode'] = $item['StaffCode'];
			
			$deletecondition['SubcriberCode'] = getInputValue('StaffCode');
			$deletecondition['StaffCode'] = $item['StaffCode'];
			$dbm->where($deletecondition)->delete();
			
			$data['IsGetYScores'] = 1;
			$data['IsGetXScores'] = 1;
			$data['IsGetTScores'] = 1;
			$data['YScoresLimit'] = 100;
			$data['XScoresLimit'] = 100;
			$data['TScoresLimit'] = 100;
			
			if($item['State']) 	$dbm -> data($data) -> add();
		}
  
		return $this -> ajaxReturn('保存成功');
	}
	
	public function deleteStaff()
	{
			$deletecondition['StaffCode'] = getInputValue('StaffCode');
			
			$dbm = M('staffs',"",getMyCon());
			$Identify = $dbm->where($deletecondition)->delete();
			if($Identify>0)
			{
				return $this -> ajaxReturn('OK');
			}
			else
			{
				return $this -> ajaxReturn('删除失败');
			}
	}

}
?>