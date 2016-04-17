<?php
namespace Home\Controller;

class WBRoleMngController extends \Think\Controller {
	 /**
	 * 获得所有角色
	 */
	 public function getRoleList()
	 {
	 	
			$rs = M('brole',"",getMyCon())->select();
		    return $this -> ajaxReturn($rs);
	 }


	/**
	 * 获得角色成员
	 */
	 public function getRoleUserList()
	 {

				$condition['a.RoleName'] = getInputValue("RoleName");
				$rs = M('buserrole as a',"",getMyCon())
				->join("left join buser on a.UserCode = bUser.UserCode")
			 	->field("a._Identify,a.RoleName,a.UserCode,UserTrueName,UserType")
				->where($condition)
			 	->select();


		return $this -> ajaxReturn($rs);
	 }
	 
	/**
	 * 获得角色的权限
	 */
	 public function getRolePrevilege()
	 {
			$condition['RoleName'] = getInputValue("RoleName");
			
			$fieldStr = "bprevilege._Identify,bprevilege.RoleName,bprevilege.ModuleID,ModuleName,ModuleLevel,ModuleDesc,";
			$fieldStr = $fieldStr . "ModuleIcon,Open,ParentModuleID,ParentModuleName,Operation";
			$rs = M('bprevilege',"",getMyCon())	
			->join("left join vwmodule  on bprevilege.ModuleID = vwmodule.ModuleID ")
			->field($fieldStr)
			->where($condition)	
			->order("ModuleLevel asc")
			->select();
			
		return $this -> ajaxReturn($rs);
	 }


}
?>