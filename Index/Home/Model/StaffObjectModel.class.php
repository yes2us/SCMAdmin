<?php
namespace Home\Model;
use Think\Model\RelationModel;

class StaffObjectModel extends RelationModel{
			
	/*
	 * 查询获得一个或多个员工的信息
	 * */
		public function getStaffBasicInfo($condition,$fieldstr=null)
		{
			if(!$fieldstr) 
			$fieldstr = "_Identify,StaffCode,StaffName,IsOnJob,IDCardNO,Birthday,MobileNO,BelongDeptCode,IsAdmin,PicturePath,";
			$fieldstr = $fieldstr . " (select vstring from SysParameters where Name='DSSuffix') DSSuffix, ";
			$fieldstr = $fieldstr . " (select staffname from staffs as a where a.staffcode=c.PrefAuditorCode) PrefAuditorName,";
			$fieldstr = $fieldstr . " (select staffname from staffs as b where b.staffcode=c.PrefArbitratorCode) PrefArbitratorName ";
			
			$rs=M('staffs as c',"",getMyCon())
			->field($fieldstr)
			->where($condition)
			->select();
//			dump($rs);
			return $rs;
		}
	
		/*
	 * 查询获得一个的角色
	 * */
		public function getStaffRoles($StaffCode)
		{	
			$rs=M('deptstaffs',"",getMyCon())
			->distinct(true)
			->where(array('StaffCode'=>$StaffCode))
			->field('reltype as rolename')
			->select();

			return $rs;
		}
	

			
	/*
	 * 查询获得一个员工的相关部门
	 * */
//		$fieldstr=['staffcode','reltype','deptcode']; //必须选择关联的外键，否则关联项不显示
		public function getStaffRelDepts($condition,$fieldstr=null)
		{
			$dbtmodel = M('vw_deptrelpersons',"",getMyCon());			
			$rs = $dbtmodel 
			->field($fieldstr)
			-> where($condition) 
			-> select();
			//echo $this->_sql();
			//dump($rs);
			return $rs;
		}
	
	/*
	 * 获得员工的相关同事,即它同一个部门的相关人[员工,审核人,申诉人,店长,经理等]
	 * */
	public function getStaffRelPersons($staffcode,$fieldstr=null) {
		//由员工得到部门编号
		$dbtmodel = M('staffs',"",getMyCon());
		$condition['StaffCode'] = $staffcode;
		$deptcode = $dbtmodel->where($condition)->getField('BelongDeptCode');	
//		echo M('staffs')->_sql();

		unset($condition);		
		$condition['DeptCode'] = $deptcode;
		$fielddarray=['staffcode','reltype','deptcode'];//必须选择关联的外键，否则关联项不显示
		$rs = M('vw_deptrelpersons',"",getMyCon()) 
//		->field($fielddarray)
		-> where($condition) 
		-> select();
		
//		echo M('vw_deptrelpersons')->_sql();
//		setTag('sql1234', $dbtmodel->_sql());
//		dump($rs);
		return $rs;
	}


	/*
	 * 获得某员工的订阅者
	 * */
	public function getStaffSubscribe($staffcode,$fieldstr=null) {
		if ($fieldstr==null)
		{
			$fieldstr=['SubcriberCode','StaffSubscribe.StaffCode','StaffName','IsOnJob'];
		}
		
		$condition['SubcriberCode'] = $staffcode;
		
		$rs = M('staffsubscribe',"",getMyCon()) 
		->join("inner join Staffs on StaffSubscribe.staffcode = Staffs.staffcode")
		->field($fieldstr)
		->where($condition) 
		->select();

		return $rs;
	}
	

	/*
	 * 获得员工事件 
	 * */
	public function getStaffEvents($staffcode,$fieldstr=null) {
		$condition['StaffCode'] = $staffcode;
		
		$rs = M("viewstaffevents","",getMyCon())
		->where($condition)
		->order('eventscopeorder, eventtypeorder,eventorder')
		->select();

		//echo $this->_sql();
//		dump($rs);
		return $rs;
	}
	
	/*
	 * 获得员工的奖扣权限和任务
	 * */
    public function getStaffAuthTask($staffcode,$fieldstr=null){
		
		$rs = M('viewroleauthtask',"",getMyCon())
		->field($fieldstr)
		->where("RoleName in (SELECT [reltype] FROM [DeptStaffs] WHERE StaffCode='". $staffcode ."')")
		->select();

		//echo $this->_sql();
//		dump($rs);
		return $rs;
    }

   	/*
	 * 获得员工的奖扣权限和任务
	 * */
    public function getStaffModules($staffcode){
		$rolename = M('rolestaffs',"",getMyCon())
		->join(' roles on rolestaffs.rolename = roles.rolename')
		->where("StaffCode='" . $staffcode . "' and roletype='系统权限'")
		->getField('rolestaffs.rolename');
		
		if(!$rolename) $rolename = '一般员工';
		
		$vtext = M('sysparameters',"",getMyCon())
		->where("[desc] like '系统权限%' and [desc] like  '%". $rolename ."%'")
		->getField('vtext');
		
        $vtext = json_decode($vtext,true);
		return $vtext;
    }
}

?>
