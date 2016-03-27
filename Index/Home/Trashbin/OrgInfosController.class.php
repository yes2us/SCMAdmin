<?php
namespace Home\Controller;

class OrgInfosController extends \Think\Controller {

// 获得所有部门	
	public function getAllDepts() {
		$dbtmodel = D('Depts');
		$rs = $dbtmodel -> select();
		dump($rs);
	}


// 获得所有群组	
	public function getAllGroups() {
		$dbtmodel = D('Groups');
		$rs = $dbtmodel -> select();
		dump($rs);
	}



// 获得员工事件
	public function getGroupEvents() {
		$dbtmodel = D('GroupEvents');
		$rs = $dbtmodel ->Relation(true)-> select();
		dump($rs);
	}


}

?>