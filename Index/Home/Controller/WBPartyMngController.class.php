<?php
namespace Home\Controller;

class WBPartyMngController extends \Think\Controller {

	public function getPartyList(){
		$rs = M("bparty","",getMyCon())
		->page("1,10000")
		->select();
		
		return $this -> ajaxReturn($rs);
	}
 
	public function getRegionList() {
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];	
		$condition['PartyType'] = '分仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode as id,PartyName as value");
		
		$rs = M("bparty","",getMyCon())
		->field($fieldstr)
		->page($pagestr)
		->where($condition)
		->select();

		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs = array_reverse($rs);
		return $this -> ajaxReturn($rs);
	}
	
	public function getCWHList() {
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];	
		$condition['PartyType'] = '总仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode as id,PartyName as value");
		
		$rs = M("bparty","",getMyCon())
		->field($fieldstr)
		->page($pagestr)
		->where($condition)
		->select();

		return $this -> ajaxReturn($rs);
	}

		public function getPartyRelation(){
			$condition['a.PartyCode'] = getInputValue("PartyCode","D03A");
			$rs = M('bparty2partyrelation as a','',getMyCon())
			->join(" left join bparty as c on a.parentcode=c.partycode")
			->field("a._identify,a.parentcode,c.partyname as parentname,relationtype,relationorder")
			->where($condition)
			->select();

		
		return $this -> ajaxReturn($rs);
		}
		public function getRelPartyList() {

			$condition['ParentCode'] = getInputValue("RegionCode","D03A");
			$condition['RelationType'] = getInputValue("RelationType","归属关系");
			$condition['IsReplenish']=1;
			
			$pagestr = getInputValue("Page","1,1000");

			$fieldstr = "_Identify,ParentCode,ParentName,RelationType,PartyCode,PartyName,";
			$fieldstr = $fieldstr . "PartyType,PartyLevel,PartyEnabled,RepBatchSize,RepNextDate,RepOrderCycle,";
			$fieldstr = $fieldstr . "RepSupplyTime,RepRollSpan,IsReturnStock,RetBatchSize,IsRetOverStock,";
			$fieldstr = $fieldstr . "RetOverStockNextDate,RetOverStockCycle,IsRetDeadStock,RetDeadStockNextDate,";
			$fieldstr = $fieldstr . "RetDeadStockCycle,IsBM,IsUseSKUBMPara,BMUpChkPeriod,BMUpFreezePeriod,";
			$fieldstr = $fieldstr . "BMUpErodeLmt,BMDnChkPeriod,BMDnFreezePeriod,BMDnErodeLmt";
		
			$fieldstr = getInputValue("FieldStr",$fieldstr);
			
			
			$dbt = M('vwp2partyrel','',getMyCon());
			
			$rs = $dbt
			->field($fieldstr)
			->where($condition)
			->page($pagestr)
			->select();
		
		return $this -> ajaxReturn($rs);
	}

  
	 
}
?>