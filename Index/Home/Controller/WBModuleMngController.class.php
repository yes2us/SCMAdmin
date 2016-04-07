<?php
namespace Home\Controller;

class WBModuleMngController extends \Think\Controller {
	
	public function getModuleList(){
		$rs = M("bwbmodule","",getMyCon())
		->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	
	public function getSubModuleList(){
		$condition["ParentModuleID"] = getInputValue("ParentModuleID");
		
		$rs = M("bwbmodule","",getMyCon())
		->where($condition)
		->select();
		return $this -> ajaxReturn($rs);
	}

	 public function getModuleTree(){
	 	$dbt =  M("bwbmodule","",getMyCon());
		
	 	$treeData =$dbt
		->field("moduleid as id,modulename as value,true as open")
		->where("ParentModuleID is null")
		->select();
		
		
		for ($x=0; $x<count($treeData); $x++) {
  		   $parentid = $treeData[$x]['id'];
			$levelTwo = $dbt
			->field("moduleid as id,modulename as value")
			->where("ParentModuleID='" . $parentid . "'")
			->select();
			$treeData[$x]['data'] = $levelTwo;
		} 
	
		
		return $this -> ajaxReturn($treeData);
	 }
}
?>