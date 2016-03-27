<?php
namespace Home\Controller;

class WBRoleMngController extends \Think\Controller {
	 /**
	 * 获得所有角色
	 */
	 public function getAllRole()
	 {
	 	
			$rs = M('roles',"",getMyCon())->select();
		
		return $this -> ajaxReturn($rs);
	 }


	/**
	 * 获得角色成员
	 */
	 public function getRoleStaff()
	 {

				$condition['RoleStaffs.RoleName'] = I("RoleName");
				$rs = M('rolestaffs',"",getMyCon())
				->join("left join Roles on RoleStaffs.RoleName = Roles.RoleName")
				->join("left join Staffs on RoleStaffs.StaffCode = Staffs.StaffCode")
			 	->field("RoleStaffs._Identify,RoleStaffs.RoleName,RoleType,RoleStaffs.StaffCode,StaffName,IsOnJob")
				->where($condition)
				->order("RoleName")
			 	->select();


		return $this -> ajaxReturn($rs);
	 }
	 
	/**
	 * 获得角色的权限与任务
	 */
	 public function getRoleAuthTask()
	 {
			$condition['RoleName'] = I("RoleName");
			$rs = M('roleincentauthtask',"",getMyCon())	->where($condition)	->order("RoleName")->select();
		return $this -> ajaxReturn($rs);
	 }


}
?>