<?php
namespace Home\Controller;

class CommonController extends \Think\Controller {

	// 获得参数
	public function getSysPara() {
		$dbtmodel = D('sysparameters');
		$rs = $dbtmodel -> where("[desc]='timespan'") -> getField('name,vinterger');
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}
	
	// 获得所有事件
	public function getEvents() {
		$dbtmodel = D('Events');
		$rs = $dbtmodel -> where('enabled=1') -> select();
		//dump($rs);
		return $this -> ajaxReturn($rs);
	}

	// 获得所有角色
	public function getRoles() {
		$dbtmodel = D('Roles');
		$rs = $dbtmodel -> where('roleenabled=1') -> select();
		$rs = $dbtmodel -> select();
		//dump($rs);
		return $this -> ajaxReturn($rs);
	}

	// 获得所有群组
	public function getGroups() {
		$dbtmodel = D('Groups');
		$rs = $dbtmodel -> where('groupenabled=1') -> select();
		//dump($rs);
		return $this -> ajaxReturn($rs);
	}

	// 获得所有部门
	public function getDepts() {
		$dbtmodel = D('Depts');
		$rs = $dbtmodel -> where('enabled=1') -> select();
		dump($rs);
//		return $this -> ajaxReturn($rs);
	}

}
?>