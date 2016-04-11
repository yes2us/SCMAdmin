<?php
namespace Home\Controller;

class WBBillMngController extends \Think\Controller {

	
	//获得目标库存调整记录
	public function getPartyBMRecord() {
		if(hasInput('WHCode')) $condition['dbmrecord.PartyCode'] = getInputValue("WHCode","ZZ27097");		
		if(hasInput('EndDate')) $condition['RecordDate'] = array("elt",getInputValue("EndDate","2014-05-02"));			
		if(hasInput('StartDate')) $condition['RecordDate'] = array("egt",getInputValue("StartDate","2014-04-02"));			
		
		$pagestr = getInputValue("Page","1,100");
		
		$fieldstr = "dbmrecord._identify,dbmrecord.partycode,PartyName,SKUCode,RecordDate,OldTargetQty,SugTargetQty,BMReason,operator";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dbmrecord","",getMyCon())
        ->join("left join bparty as p1 on dbmrecord.partycode = p1.partycode")
//      ->join("left join bsku as p2 on dbmrecord.skucode = p2.skucode")
        ->field($fieldstr)
        ->page($pagestr)
		->order("dbmrecord._Identify desc")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	//获得SKU调拨计划
    public function getMovSKUPlan(){
    		if(hasInput('PlanType'))  $condition['a.PlanType'] = array("like","%" . getInputValue("PlanType") . "%");			
    		if(hasInput('DealState'))  $condition['a.DealState'] = getInputValue("DealState");			
		
		if(hasInput('MakeDate'))  $condition['a.MakeDate'] = getInputValue("MakeDate");				
    		if(hasInput('StartDate'))  $condition['a.MakeDate'] = array( 'egt',getInputValue("StartDate"));			
    		if(hasInput('EndDate'))  $condition['a.MakeDate'] = array( 'elt',getInputValue("EndDate"));				
		if(hasInput('SrcPartyCode'))  $condition['a.SrcPartyCode'] = getInputValue("SrcPartyCode");			
		if(hasInput('TrgPartyCode'))  $condition['a.TrgPartyCode'] = getInputValue("TrgPartyCode");			
		
		$pagestr = "1,1000";
		
		$fieldstr = "a.SrcPartyCode,p1.PartyName as SrcPartyName,a.TrgPartyCode,p2.PartyName as TrgPartyName,MakeDate,Sum(MovQty) as MovQty";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dmovskuplan as a","",getMyCon())
        ->join("inner join bparty as p1 on a.SrcPartyCode = p1.PartyCode")
        ->join("inner join bparty as p2 on a.TrgPartyCode = p2.PartyCode")
        ->field($fieldstr)
        ->where($condition)
		->page($pagestr)
		->group("a.SrcPartyCode,p1.PartyName,a.TrgPartyCode,p2.PartyName,MakeDate")
        ->select();
		

//		p(M("dmovskuplan as a","",getMyCon())->_sql());
		return $this -> ajaxReturn($rs);
    }  	

//获得SKU调拨计划明细
    public function getMovSKUPlanItem(){
    		if(hasInput('PlanType'))  $condition['a.PlanType'] = array("like","%" . getInputValue("PlanType") . "%");					
    		if(hasInput('DealState'))  $condition['a.DealState'] = getInputValue("DealState");	
    		
    		if(hasInput('MakeDate'))  $condition['a.MakeDate'] = getInputValue("MakeDate");				
    		if(hasInput('StartDate'))  $condition['a.MakeDate'] = array( 'egt',getInputValue("StartDate"));			
    		if(hasInput('EndDate'))  $condition['a.MakeDate'] = array( 'elt',getInputValue("EndDate"));			
		if(hasInput('SrcPartyCode'))  $condition['a.SrcPartyCode'] = getInputValue("SrcPartyCode");			
		if(hasInput('TrgPartyCode'))  $condition['a.TrgPartyCode'] = getInputValue("TrgPartyCode");			
		if(hasInput('SKUCode'))  $condition['a.SKUCode'] = getInputValue("SKUCode");			
		
		$pagestr = "1,100";
		
		$fieldstr = "a._Identify,a.SrcPartyCode,p1.PartyName as SrcPartyName,a.TrgPartyCode,p2.PartyName as TrgPartyName,MakeDate,";
		$fieldstr = $fieldstr . "a.SKUCode,SKCCode,ProductName,ColorName,SizeName,BrandName,YearName,SeasonName,SeasonStageName,";
		$fieldstr = $fieldstr . "MainTypeName,SubTypeName,PlanType,MovQty,DealState";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dmovskuplan as a","",getMyCon())
        ->join("inner join bparty as p1 on a.SrcPartyCode = p1.PartyCode")
        ->join("inner join bparty as p2 on a.TrgPartyCode = p2.PartyCode")
        ->join("left join bsku as p3 on a.SKUCode = p3.SKUCode")
        ->field($fieldstr)
        ->where($condition)
		->page($pagestr)
        ->select();
		
//		p($rs);
		return $this -> ajaxReturn($rs);
    }  	
	
  //获得SKC调拨计划
    public function getMovSKCPlanItem(){
    		if(hasInput('PlanType'))  $condition['d1.PlanType'] = getInputValue("PlanType");			
    		if(hasInput('DealState'))  $condition['d1.DealState'] = getInputValue("DealState");			
    		if(hasInput('SKCCode'))  $condition['d1.SKCCode'] = getInputValue("SKCCode");			
		if(hasInput('SrcPartyCode'))  $condition['d1.SrcPartyCode'] = getInputValue("SrcPartyCode");			
		if(hasInput('TrgPartyCode'))  $condition['d1.TrgPartyCode'] = getInputValue("TrgPartyCode");			
		if(hasInput('ParentCode'))  
     	$condition['d1.SrcPartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  getInputValue("ParentCode",'D03A') ."')");
		
		$pagestr = "1,1000";
		
		$fieldstr = "d1._Identify,d1.SrcPartyCode,p1.PartyName as SrcPartyName,d1.TrgPartyCode,p2.PartyName as TrgPartyName,d1.SKCCode,ProductName,ColorName,";
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
		$condition['_string'] = "(d1.SrcPartyCode='". getInputValue("WHCode") . "' or d1.TrgPartyCode='". getInputValue("WHCode") ."') ";
		
		$pagestr = "1,1000";
		
		$fieldstr = "d1._Identify,d1.SrcPartyCode,p1.PartyName as SrcPartyName,d1.TrgPartyCode,p2.PartyName as TrgPartyName,d1.SKCCode,ProductName,ColorName,";
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
    

}
?>