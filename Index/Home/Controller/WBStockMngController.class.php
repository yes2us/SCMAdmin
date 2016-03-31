<?php
namespace Home\Controller;

class WBStockMngController extends \Think\Controller {

	
		public function getStoreTSInfo(){
		$storecode = getInputValue("StoreCode","A00Z003");
			
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT dstock._Identify,PartyCode,dstock.SKUCode,productcolorcode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as RepRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $storecode . "'";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
		
		
		// 获得中央仓的目标库存及库存
 	public function getCWHTSInfo() {
		$storecode = getInputValue("CWHCode","A00Z003");
		
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT PartyCode,dstock.SKUCode,productcolorcode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as RepRet";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $storecode . "'";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
	
	//获得目标库存调整记录
	public function getPartyAdjRec() {
		$condition['dadjusttsrecord.PartyCode'] = getInputValue("WHCode","ZZ27097");			
		$condition['RecordDate'] = array("elt",getInputValue("RecordDate","2014-05-02"));			
		
		$pagestr = getInputValue("Page","1,100");
		
		$fieldstr = "PartyName,SKUCode,RecordDate,OldTargetQty,SugTargetQty,AdjustReason,operator";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dadjusttsrecord","",getMyCon())
        ->join("left join bparty as p1 on dadjusttsrecord.partycode = p1.partycode")
//      ->join("left join bsku as p2 on dadjusttsrecord.skucode = p2.skucode")
        ->field($fieldstr)
        ->page($pagestr)
		->order("RecordDate desc")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
		
	//获得补货单
	public function getReplenishBill() {
		$condition['dreporder.PartyCode'] = getInputValue("StoreCode","ZZ27097");			
		$condition['RepDate'] = array("eq",getInputValue("RepDate","2014-05-24"));			
		
		$pagestr = getInputValue("Page","1,1000");
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,dreporder.SKUCode,ProductColorCode,ProductName,";
		$fieldstr = $fieldstr . "SizeName,BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,RepType,RepQty,RepDate";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dreporder","",getMyCon())
        ->join("left join bparty as p1 on dreporder.partycode = p1.partycode")
        ->join("left join bparty as p2 on dreporder.partycode = p2.partycode")
        ->join("left join bsku as p3 on dreporder.skucode = p3.skucode")
        ->field($fieldstr)
        ->page($pagestr)
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	//获得退货单
	public function getReturnBill() {		
		$condition['dretorder.PartyCode'] = getInputValue("StoreCode","ZZ18001");			
		$condition['RetDate'] = array("eq",getInputValue("RetDate","2014-04-16"));			
		
		$pagestr = getInputValue("Page","1,1000");
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,dretorder.SKUCode,ProductColorCode,ProductName,";
		$fieldstr = $fieldstr . "SizeName,BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,RetType,RetQty,RetDate";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dretorder","",getMyCon())
        ->join("left join bparty as p1 on dretorder.partycode = p1.partycode")
        ->join("left join bparty as p2 on dretorder.partycode = p2.partycode")
        ->join("left join bsku as p3 on dretorder.skucode = p3.skucode")
        ->field($fieldstr)
        ->page($pagestr)
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	//获得产品的历史库存
	public function getProdHSStock() {
		$condition['PartyCode'] = getInputValue("WHCode","D03A");			
		$condition['SKUCode'] = getInputValue("SKUCode","133680012016573");
		$rs = M("dhisstock","",getMyCon())
		->order("HSRecordDate desc")
		->limit(30)
		->where($condition)
		->select();

		return $this -> ajaxReturn($rs);
	}
}
?>