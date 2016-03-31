<?php
namespace Home\Controller;

class WBPartyMngController extends \Think\Controller {

	/**
 * 获得当月的行动计划
 */
	public function getRegionList() {
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];	
		$condition['PartyType'] = '分仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode,PartyName");
		
		$rs = M("bparty","",getMyCon())
		->field($fieldstr)
		->page($pagestr)
		->where(array("PartyType"=>"分仓","PartyEnabled"=>1))
		->select();

		$rs = D("PartyObject")->getRegionList($condition,$pagestr,$fieldstr);
		return $this -> ajaxReturn($rs);
	}
	
		public function getStoreList() {

			$condition['ParentCode'] = getInputValue("RegionCode","D03A");
			$condition['RelationType'] = "归属关系";
			
			$pagestr = getInputValue("Page","1,1000");

			$fieldstr = "_Identify,ParentCode,ParentName,RelationType,PartyCode,PartyName,";
			$fieldstr = $fieldstr . "PartyType,PartyLevel,PartyEnabled,RepBatchSize,RepParaNextDate,RepParaOrderCycle,";
			$fieldstr = $fieldstr . "RepParaSupplyTime,RepParaRollSpan,IsReturnStock,RetBatchSize,IsRetOverStock,";
			$fieldstr = $fieldstr . "RetOverStockNextDate,RetOverStockCycle,IsRetDeadStock,RetDeadStockNextDate,";
			$fieldstr = $fieldstr . "RetDeadStockCycle,IsAdjustTarget,IsUseSKUAdjPara,AdjParaUpChkPeriod,AdjParaUpFreezePeriod,";
			$fieldstr = $fieldstr . "AdjParaUpErodeLmt,AdjParaDnChkPeriod,AdjParaDnFreezePeriod,AdjParaDnErodeLmt";
		
			$fieldstr = getInputValue("FieldStr",$fieldstr);
			$rs = D("PartyObject")->getStoreList($condition,$pagestr,$fieldstr);
		return $this -> ajaxReturn($rs);
	}
	
		public function getStoreTSInfo(){
			$storecode = getInputValue("StoreCode","D03A");
			$rs = D("PartyObject")->getStoreTSInfo($storecode);
			return $this -> ajaxReturn($rs);
		}
}
?>