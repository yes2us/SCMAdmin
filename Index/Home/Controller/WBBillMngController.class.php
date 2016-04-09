<?php
namespace Home\Controller;

class WBBillMngController extends \Think\Controller {

	
	//获得目标库存调整记录
	public function getPartyAdjRec() {
		if(hasInput('WHCode')) $condition['dadjusttsrecord.PartyCode'] = getInputValue("WHCode","ZZ27097");		
		if(hasInput('EndDate')) $condition['RecordDate'] = array("elt",getInputValue("EndDate","2014-05-02"));			
		if(hasInput('StartDate')) $condition['RecordDate'] = array("egt",getInputValue("StartDate","2014-04-02"));			
		
		$pagestr = getInputValue("Page","1,100");
		
		$fieldstr = "dadjusttsrecord._identify,dadjusttsrecord.partycode,PartyName,SKUCode,RecordDate,OldTargetQty,SugTargetQty,AdjustReason,operator";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dadjusttsrecord","",getMyCon())
        ->join("left join bparty as p1 on dadjusttsrecord.partycode = p1.partycode")
//      ->join("left join bsku as p2 on dadjusttsrecord.skucode = p2.skucode")
        ->field($fieldstr)
        ->page($pagestr)
		->order("dadjusttsrecord._Identify desc")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
		
	//获得补货退货单
	public function getRepRetOrder() {
	
		if(getInputValue("OrderType","Rep")=='Rep'){$tablename='dreporder';}else{$tablename='dretorder';};
		if(hasInput('RegionCode'))  $condition['d1.ParentCode'] = getInputValue("RegionCode","D03A");			
		if(hasInput('WHCode'))  $condition['d1.PartyCode'] = getInputValue("WHCode","ZZ27097");			
		if(hasInput('EndDate'))  $condition['MakeDate'] = array("elt",getInputValue("EndDate","2014-05-21"));			
		if(hasInput('StartDate'))  $condition['MakeDate'] = array("egt",getInputValue("StartDate","2014-03-24"));			
		if(hasInput('WHType'))  $condition['p1.PartyType'] = getInputValue("WHType","门店");			
		
		$pagestr = getInputValue("Page","1,1000");
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,d1.OrderCode,MakeDate,sum(OrderQty) as OrderQty";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M($tablename . " as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
        ->field($fieldstr)
        ->page($pagestr)
        ->group("p1.PartyName,p2.PartyName,d1.OrderCode,MakeDate")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}

	//获得补货退货单
	public function getRetOrderRESTful() {
		if(hasInput('OrderType'))  $condition['d1.OrderType'] = getInputValue("OrderType","人工退货");			
		if(hasInput('ParentCode'))  $condition['d1.ParentCode'] = getInputValue("ParentCode","D03A");			
		if(hasInput('PartyCode'))  $condition['d1.PartyCode'] = getInputValue("PartyCode","ZZ27097");			
		if(hasInput('SKUCode'))  $condition['SKUCode'] = getInputValue("SKUCode");		
		if(hasInput('SKCCode'))  $condition['SKUCode'] = array("like","%" . getInputValue("SKCCode") . "%");			
		$condition['DealState'] = -1;
		
		$pagestr = getInputValue("Page","1,1000");
		$fieldstr = "d1.PartyCode,p1.PartyName,d1.ParentCode,p2.PartyName as ParentName,d1.OrderCode,d1.OrderType,d1.DealState,MakeDate,OrderQty";
				
        $rs = M("dretorder as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
        ->field($fieldstr)
        ->page($pagestr)
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	//获得补货单明细
	public function getRepRetOrderItem() {
		if(getInputValue("OrderType","Rep")=='Rep'){$tablename='dreporder';}else{$tablename='dretorder';};
		if(hasInput('OrderCode'))  $condition['OrderCode'] = getInputValue("OrderCode","ZZ27001@2014-05-03");			
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,d1.OrderCode,d1.SKUCode,SKCCode,ProductName,ColorName,";
		$fieldstr = $fieldstr . "SizeName,BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,SubTypeName,OrderType,OrderQty,MakeDate";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M($tablename . " as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
		->join("left join bsku as p3 on d1.skucode = p3.skucode")
        ->field($fieldstr)
        ->where($condition)
        ->select();
		
//		p(M($tablename . " as d1","",getMyCon())->_sql());
		return $this -> ajaxReturn($rs);
	}

    //获得退货计划
    public function getRetPlanOrder(){
		if(hasInput('RetTargetWHCode'))  $condition['d1.ParentCode'] = getInputValue("RetTargetWHCode","D03A");			
		if(hasInput('SubWHCode'))  $condition['p1.PartyCode'] = getInputValue("SubWHCode");			
		
		if(hasInput('SKUCode'))  $condition['d1.SKUCode'] = getInputValue("SKUCode");			
		if(hasInput('SKCCode'))  $condition['d1.SKUCode'] = array("like","%".getInputValue("SKCCode")."%");			
		$pagestr = "1,1000";
		
		$fieldstr = "d1.partycode,p1.PartyName,p2.PartyName as ParentName,d1.OrderCode,d1.SKUCode,SKCCode,ProductName,ColorName,";
		$fieldstr = $fieldstr . "SizeName,BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,SubTypeName,OrderType,OrderQty,MakeDate";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dretorder as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
		->join("left join bsku as p3 on d1.skucode = p3.skucode")
        ->field($fieldstr)
        ->where($condition)
		->page($pagestr)
        ->select();
		
//		$rs= M("dretorder as d1","",getMyCon())->_sql();
//		p($rs);
		return $this -> ajaxReturn($rs);
    }
    
  //获得调拨计划
    public function getMovSKCPlan(){
    		if(hasInput('PlanType'))  $condition['d1.PlanType'] = getInputValue("PlanType");			
    		if(hasInput('DealState'))  $condition['d1.DealState'] = getInputValue("DealState");			
    		if(hasInput('SKCCode'))  $condition['d1.SKCCode'] = getInputValue("SKCCode");			
		if(hasInput('SrcPartyCode'))  $condition['d1.SrcPartyCode'] = getInputValue("SrcPartyCode");			
		if(hasInput('TrgPartyCode'))  $condition['d1.TrgPartyCode'] = getInputValue("TrgPartyCode");			
		if(hasInput('ParentCode'))  
     	$condition['d1.SrcPartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  getInputValue("ParentCode",'D03A') ."')");
		
		$pagestr = "1,1000";
		
		$fieldstr = "d1.SrcPartyCode,p1.PartyName as SrcPartyName,d1.TrgPartyCode,p2.PartyName as TrgPartyName,d1.SKCCode,ProductName,ColorName,";
		$fieldstr = $fieldstr . "BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,SubTypeName,MovQty,MakeDate,DealState";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dmovskcplan as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.srcpartycode = p1.partycode")
        ->join("left join bparty as p2 on d1.trgpartycode = p2.partycode")
		->join("left join vwskc as p3 on d1.SKCCode = p3.SKCCode")
        ->field($fieldstr)
        ->where($condition)
		->page($pagestr)
        ->select();
		
//		$rs= M("dmovskcplan as d1","",getMyCon())->_sql();
//		p($rs);
		return $this -> ajaxReturn($rs);
    }  		
    
    
	//拉式换款计划
    public function getRefrSKCPlan(){
    		if(hasInput('SKCCode'))  $condition['d1.SKCCode'] = getInputValue("SKCCode");		
		$condition['_string'] = "(d1.SrcPartyCode=' ". getInputValue("WHCode") . "' or d1.TrgPartyCode='". getInputValue("WHCode") ."') ";
		
		$pagestr = "1,1000";
		
		$fieldstr = "d1.SrcPartyCode,p1.PartyName as SrcPartyName,d1.TrgPartyCode,p2.PartyName as TrPartyName,d1.SKCCode,ProductName,ColorName,";
		$fieldstr = $fieldstr . "BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,SubTypeName,MovQty,MakeDate,DealState";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dmovskcplan as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.srcpartycode = p1.partycode")
        ->join("left join bparty as p2 on d1.trgpartycode = p2.partycode")
		->join("left join vwskc as p3 on d1.SKCCode = p3.SKCCode")
        ->field($fieldstr)
        ->where($condition)
		->page($pagestr)
        ->select();
		
//		$rs= M("dmovskcplan as d1","",getMyCon())->_sql();
//		p($rs);
		return $this -> ajaxReturn($rs);
    } 
    
    	 public function getMovPlanRESTful(){
    	 	$condition['DealState'] = '未处理';
		$condition['PlanType'] = $_GET["PlanType"];
	 	if(hasInput('SrcPartyCode')) $condition['SrcPartyCode'] =$_GET["SrcPartyCode"];
	 	if(hasInput('TrgPartyCode')) $condition['TrgPartyCode'] =$_GET["TrgPartyCode"];
	 	if(hasInput('TrgPartyCode')) $condition['TrgPartyCode'] =$_GET["TrgPartyCode"];
	 	if(hasInput('TrgPartyCode')) $condition['TrgPartyCode'] =$_GET["TrgPartyCode"];
	 	
	 	if(hasInput('SKCCode')) $condition['SKCCode'] =$_GET["SKCCode"];
		if(hasInput('RetRegionCode')) 
		$condition['SrcPartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  $_GET['RetRegionCode'] ."')");
		
		$fieldStr = "a._Identify,SrcPartyCode,b.PartyName as SrcPartyName,TrgPartyCode,c.PartyName as TrgPartyName,";
		$fieldStr = $fieldStr . "MakeDate,MovQty,DealState";
		
	   $rs = M("dmovskcplan as a","",getMyCon())
        ->join("left join bparty as b on a.srcpartycode = b.partycode")
        ->join("left join bparty as c on a.trgpartycode = c.partycode")
        ->field($fieldStr)
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	 }
     

}
?>