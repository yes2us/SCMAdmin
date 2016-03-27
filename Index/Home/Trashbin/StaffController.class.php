<?php
namespace Home\Controller;

class StaffController extends \Think\Controller {


// 获得所有员工	OK
	public function getStaffBasicInfo() {
		$staffs = M('Staffs');
		$condition['StaffCode'] = I("staffcode");
		$rs = $staffs -> where($condition)->select();
		dump($rs);
//		$this->ajaxReturn($rs);
	}

		
// 获得员工相关部门 OK
	public function getStaffRelDepts() {
		$dbtmodel = D('StaffDepts');
		$condition['StaffCode'] = I("staffcode");
		$filedarray=['staffcode','reltype','deptcode']; //必须选择关联的外键，否则关联项不显示
		$rs = $dbtmodel ->Relation(true)->field($filedarray)-> where($condition) -> select();
		//dump($rs);
		$this->ajaxReturn($rs);
	}


// 获得员工的相关同事,即它同一个部门的相关人[员工,审核人,申诉人,店长,经理等]
	public function getStaffRelColleagues() {
		//由员工得到部门编号
		$dbtmodel = D('StaffDepts');
		$condition['StaffCode'] = I("staffcode");
		$condition['RelType'] = '部门员工';		
		$deptcode = $dbtmodel->where($condition)->getField('deptcode');	
//		dump($deptcode);

		unset($condition);		
		$condition['DeptCode'] = $deptcode;
		$fielddarray=['staffcode','reltype','deptcode'];//必须选择关联的外键，否则关联项不显示
		$rs = $dbtmodel ->Relation(true)->field($fielddarray)-> where($condition) -> select();
		dump($rs);
//		$this->ajaxReturn($rs);
	}
	
	
	// 获得员工订阅者
	public function getStaffSubscribe() {
		$dbtmodel = D('StaffSubscribe');
		$condition['SubcriberCode'] = I("staffcode");
		$filedarray=['subcribercode','staffname','IsOnJob'];
		$rs = $dbtmodel ->Relation(true)-> where($condition) -> select();
		dump($rs);
//		$this->ajaxReturn($rs);
	}
	
	
	// 获得员工事件 
	public function getStaffEvents() {
		$dbtmodel = D('StaffEvents');
		$condition['StaffCode'] = I("staffcode");
		$rs = $dbtmodel ->where($condition)->select();
		dump($rs);
//		$this->ajaxReturn($rs);
	}


// 获得角色奖扣权限
    public function getRoleIncentAuth(){
    	$dbtmodel = D('RoleAuthTask');
		$rs = $dbtmodel -> select();
		dump($rs);
//		$this->ajaxReturn($rs);
    }
}

?>