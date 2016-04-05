<?php
namespace Home\Controller;

class WBStockMngController extends \Think\Controller {

		// 获得门店、分仓和中央仓的目标库存及库存
		public function getFGWarehouseTSInfo(){
		$whcode = getInputValue("WHCode","A00Z003");
			
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT dstock._Identify,PartyCode,dstock.SKUCode,SKCCode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as RepRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $whcode . "' and (ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))>0";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
		

		// 获得目标退货仓下属退货仓（门店或分仓）的目标库存及库存
		public function getRetTargetWHSubWHTSInfo(){
		$rettargetwhcode = getInputValue("RetTargetWHCode","D03A");
		if(isset($_POST['SKUCode']))  $skucodestr = " SKUCode='" . getInputValue("SKUCode") ."' ";			
		if(isset($_POST['SKCCode']))  $skucodestr = " SKUCode like '%".getInputValue("SKCCode")."%' ";	
			
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT dstock._Identify,dstock.PartyCode,bparty.PartyName,SKUCode,";
		$sqlstr = $sqlstr . " TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (-ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)) as SugRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bparty on dstock.partycode = bparty.partycode";
		$sqlstr = $sqlstr . " left join bparty2partyrelation on dstock.partycode = bparty2partyrelation.partycode and RelationType='退货关系'";
		$sqlstr = $sqlstr . " where ParentCode='" . $rettargetwhcode . "' and " . $skucodestr . " and (ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))>0";
		$sqlstr = $sqlstr . " order by (-ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)) desc";

//		$rs = $sqlstr;
		$rs=$Model->query($sqlstr);

//		return $rs;
		return $this -> ajaxReturn($rs);
		}
			
	//获得目标库存调整记录
	public function getPartyAdjRec() {
		if(isset($_POST['WHCode'])) $condition['dadjusttsrecord.PartyCode'] = getInputValue("WHCode","ZZ27097");		
		if(isset($_POST['EndDate'])) $condition['RecordDate'] = array("elt",getInputValue("EndDate","2014-05-02"));			
		if(isset($_POST['StartDate'])) $condition['RecordDate'] = array("egt",getInputValue("StartDate","2014-04-02"));			
		
		$pagestr = getInputValue("Page","1,100");
		
		$fieldstr = "PartyName,SKUCode,RecordDate,OldTargetQty,SugTargetQty,AdjustReason,operator";
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
		if(isset($_POST['RegionCode']))  $condition['d1.ParentCode'] = getInputValue("RegionCode","D03A");			
		if(isset($_POST['WHCode']))  $condition['d1.PartyCode'] = getInputValue("WHCode","ZZ27097");			
		if(isset($_POST['EndDate']))  $condition['MakeDate'] = array("elt",getInputValue("EndDate","2014-05-21"));			
		if(isset($_POST['StartDate']))  $condition['MakeDate'] = array("egt",getInputValue("StartDate","2014-03-24"));			
		if(isset($_POST['WHType']))  $condition['p1.PartyType'] = getInputValue("WHType","门店");			
		
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

	
	
	//获得补货单明细
	public function getRepRetOrderItem() {
		if(getInputValue("OrderType","Rep")=='Rep'){$tablename='dreporder';}else{$tablename='dretorder';};
		if(isset($_POST['OrderCode']))  $condition['OrderCode'] = getInputValue("OrderCode","ZZ27001@2014-05-03");			
		
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
		if(isset($_POST['RetTargetWHCode']))  $condition['d1.ParentCode'] = getInputValue("RetTargetWHCode","D03A");			
		if(isset($_POST['SubWHCode']))  $condition['p1.PartyCode'] = getInputValue("SubWHCode");			
		
		if(isset($_POST['SKUCode']))  $condition['d1.SKUCode'] = getInputValue("SKUCode");			
		if(isset($_POST['SKCCode']))  $condition['d1.SKUCode'] = array("like","%".getInputValue("SKCCode")."%");			
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
    		if(isset($_POST['SKCCode']))  $condition['d1.SKCCode'] = getInputValue("SKCCode");			
		if(isset($_POST['SrcPartyCode']))  $condition['d1.SrcPartyCode'] = getInputValue("SrcPartyCode");			
		if(isset($_POST['TrgPartyCode']))  $condition['d1.TrgPartyCode'] = getInputValue("TrgPartyCode");			
		if(isset($_POST['ParentCode']))  
     	$condition['d1.SrcPartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  getInputValue("ParentCode",'D03A') ."')");
		
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
    
    
	//拉式换款计划
    public function getRefrSKCPlan(){
    		if(isset($_POST['SKCCode']))  $condition['d1.SKCCode'] = getInputValue("SKCCode");		
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

	//显示门店的库存结构
	   public function getStoreStockStruct(){
	   	$wherestr = "where ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) >0 ";
	   	 if(isset($_POST['StoreCode'])) 
     	{
     		    	$partycode = getInputValue("StoreCode","A00Z003");
     		    $wherestr = $wherestr . " and 	PartyCode='" . $partycode ."'";
     	}
     	
     	if(isset($_POST['RetTargetWHCode'])) 
     	{
     			$retTargetcode = getInputValue("RetTargetWHCode","D03A");
     			$wherestr = $wherestr . " and exists( select 1 from bparty2partyrelation as a where parentcode='" . $retTargetcode ."' and a.partycode =zdimpartyskc.partycode and relationtype='退货关系')";
		}
		
		$sqlstr = "select PartyCode,partyname,yearname,SeasonName,seriesname,count(1) as 'SKCNum',";
		$sqlstr = $sqlstr . " sum(ifnull(TargetQty,0)) as 'TargetQty',sum(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) )as 'TotalQty',";
		$sqlstr = $sqlstr . " sum(ifnull(StoreOverStockQty,0) )as 'OverStockQty',sum(ifnull(StoreShortStockQty,0)) as 'ShortStockQty',";
		$sqlstr = $sqlstr . " sum(if(IsDeadProduct,0,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))) as 'DeadStockQty',";
		$sqlstr = $sqlstr . " sum(if(IsDeadProduct,0,1)) as 'DeadSKCNum',sum(if(SaleType='畅销款',1,0)) as 'FastRunnerSKCNum'";
		$sqlstr = $sqlstr . " from zdimpartyskc ";
		$sqlstr = $sqlstr . $wherestr;
		$sqlstr = $sqlstr . " group by PartyCode,partyname,yearname,SeasonName,seriesname";
		
		$dbt = new \Think\Model("","",getMyCon());
		$rs = $dbt->query($sqlstr);
		
		return $this -> ajaxReturn($rs);
     }
     
     public function getWHSKCInfo(){
     		if(isset($_POST['WHCode']))  $condition['PartyCode'] = getInputValue("WHCode");
     		if(isset($_POST['ParentWHCode']))  
     		$condition['PartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  getInputValue("ParentWHCode",'A00Z003') ."')");
			
     		if(isset($_POST['SKCCode']))  $condition['SKCCode'] =getInputValue("SKCCode",'1326723019');
     		
			$condition['_string'] = "ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)+ifnull(TargetQty,0)+ifnull(SaleTotalQty,0)>0";
     		$pageStr = getInputValue("Page","1,1000");
		
     		$fieldStr = "PartyCode,PartyName,PartyLevel,SKCCode,ColorName,YearName,seasonname,seasonstagename,";
			$fieldStr = $fieldStr . " seriesName,MaintypeName,SubTypeName,OnShelfDays,SaleType,TargetQty,";
			$fieldStr = $fieldStr . " if(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)>0,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0),null) as 'StockQty',ShortStockQty,Sale30Qty,SaleTotalQty,";
			$fieldStr = $fieldStr . " if(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)+ifnull(TargetQty,0)<1,0,1) InStore";
     		$fieldStr = getInputValue("fieldStr",$fieldStr);
			
     		$rs = M("zdimpartyskc","",getMyCon())
     		->field($fieldStr)
			->where($condition)
			->page($pageStr)
			->select();
			
     		return $this->ajaxReturn($rs);
     }

//查询一个门店没有的款色
    public function getWHSKCInfoNewSKC(){
    			
     		$storecode = getInputValue("WHCode","AZ00003");
     		$parentcode = getInputValue("ParentCode","D03A");
     		
     		$sqlstr = " select PartyCode,PartyName,PartyLevel,SKCCode,ColorName,YearName,seasonname,seasonstagename,";
			$sqlstr = $sqlstr . " seriesName,MaintypeName,SubTypeName,OnShelfDays,SaleType,TargetQty,";
			$sqlstr = $sqlstr . " if(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)>0,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0),null) as 'StockQty',";
			$sqlstr = $sqlstr . " ShortStockQty,Sale30Qty,SaleTotalQty ";
			$sqlstr = $sqlstr . " from  zdimpartyskc as a";
			$sqlstr = $sqlstr . " where  ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)+ifnull(TargetQty,0)+ifnull(SaleTotalQty,0)>0 ";
			$sqlstr = $sqlstr . " and PartyCode='" . $parentcode . "' and not exists(";
			$sqlstr = $sqlstr . " SELECT 1 FROM zdimpartyskc  as b";
			$sqlstr = $sqlstr . " WHERE b.PartyCode = '" . $storecode . "'  and b.SKCCode=a.SKCCode";
			$sqlstr = $sqlstr . " AND ( ifnull(b.OnHandQty,0)+ifnull(b.OnRoadQty,0)+ifnull(b.TargetQty,0)+ifnull(SaleTotalQty,0)>0)) ";
			
//			dump($sqlstr);
			
			$dbt = new \Think\Model("","",getMyCon());
			$rs = $dbt->query($sqlstr);
			
     		return $this->ajaxReturn($rs);
     }
}
?>