<?php
namespace Home\Controller;

class WBStockMngController extends \Think\Controller {

		// 获得门店、分仓和中央仓的目标库存及库存
		public function getFGWarehouseTSInfo(){
//			die();
		$whcode = getInputValue("WHCode","A00Z003");
			
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT dstock._Identify,PartyCode,dstock.SKUCode,SKCCode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as SugRepQty,";
		$sqlstr = $sqlstr . " (-ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)) as SugRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $whcode . "' and (ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))>0   limit 0,10000";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
		

		// 获得目标退货仓下属退货仓（门店或分仓）的目标库存及库存
		public function getRetTargetWHSubWHTSInfo(){
		$rettargetwhcode = getInputValue("RetTargetWHCode","D03A");
		if(hasInput('SKUCode'))  $skucodestr = " and SKUCode='" . getInputValue("SKUCode") ."' ";			
		if(hasInput('SKCCode'))  $skucodestr = " and SKUCode like '%".getInputValue("SKCCode")."%' ";	

		
		$sqlstr = "SELECT dstock._Identify,dstock.PartyCode,bparty.PartyName,SKUCode,";
		$sqlstr = $sqlstr . " TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as SugRepQty,";
		$sqlstr = $sqlstr . " (-ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)) as SugRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bparty on dstock.partycode = bparty.partycode";
		$sqlstr = $sqlstr . " left join bparty2partyrelation on dstock.partycode = bparty2partyrelation.partycode and RelationType='退货关系'";
		$sqlstr = $sqlstr . " where ParentCode='" . $rettargetwhcode . "' " . $skucodestr . " and (ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))>0";
		$sqlstr = $sqlstr . " order by (-ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)) desc limit 0,10000";

//		$rs = $sqlstr;
//dump($sqlstr);


		$Model = new \Think\Model("","",getMyCon());
		$rs=$Model->query($sqlstr);

//		return $rs;
		return $this -> ajaxReturn($rs);
		}
			
	//获得产品的历史库存
	public function getSKUHSStock() {
		$condition['PartyCode'] = getInputValue("WHCode","D03A");			
		$condition['SKUCode'] = getInputValue("SKUCode","133680012016570");
		$fieldstr = "date_format(HSRecordDate,'%c/%d') as Date,HSTargetQty as GreenZone,round(2*HSTargetQty/3,1) as YellowZone,";
		$fieldstr = $fieldstr . " round(HSTargetQty/3,1) as RedZone,HSOnHandQty as HandQty";
		
		$rs['imgData'] = M("dhisstock","",getMyCon())
		->field($fieldstr)
		->limit(30)
		->where($condition)
		->select();

		$rs['yValueLimit']= M("dhisstock","",getMyCon())
		->field("max(if(HSTargetQty>HSOnHandQty,HSTargetQty,HSOnHandQty)) as YUpLimit")
		->where($condition)
		->select();
		
		return $this -> ajaxReturn($rs);
	}

	//显示门店的库存结构:此函数需要优化
	   public function getPartyIndex(){
//	   	die();
		
		if(hasInput('StoreCode'))
	   	$condition['PartyCode'] = getInputValue("StoreCode","A00Z003");
	   	
	   	if(hasInput('ParentCode'))
	   	{
	   		$parentcode = getInputValue("ParentCode","D03A");
	   		$relationtype = getInputValue("RelationType","补货关系");
	   		$condition['_string'] =  "exists( select 1 from bparty2partyrelation as a where parentcode='" . $parentcode ."' and a.partycode =zdimparty.partycode and relationtype='" . $relationtype. "')";
		}

		$fieldStr = "PartyCode,partyname,yearname,SeasonName,seriesname,SKCNum,";
		$fieldStr = $fieldStr . " StockTargetQty,StockTotalQty,StockOverInStores,StockShortInStores,";
		$fieldStr = $fieldStr . " StockDeadQty,DeadSKCNum,FRSKCNumInParty";
		
		$fieldStr = getInputValue("fieldStr",$fieldStr);
		
		$rs = M("zdimparty","",getMyCon())
//		->field($fieldStr)
		->where($condition)
		->select();
		
		
		return $this -> ajaxReturn($rs);
     }
     
     public function getWHSKCInfo(){
     		if(hasInput('WHCode'))  $condition['PartyCode'] = getInputValue("WHCode");
     		if(hasInput('ParentWHCode'))  
     		$condition['PartyCode'] = array("exp","in (select partycode from bparty2partyrelation where relationtype='补货关系' and parentcode='".  getInputValue("ParentWHCode",'A00Z003') ."')");
			
     		if(hasInput('SKCCode'))  $condition['SKCCode'] =getInputValue("SKCCode",'1326723019');
     		
			$condition['_string'] = "ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)+ifnull(TargetQty,0)+ifnull(SaleTotalQty,0)>0";
     		$pageStr = getInputValue("Page","1,1000");
		
     		$fieldStr = "PartyCode,PartyName,PartyLevel,SKCCode,ColorName,YearName,seasonname,seasonstagename,";
			$fieldStr = $fieldStr . " seriesName,MaintypeName,SubTypeName,OnShelfDays,SaleType,TargetQty,";
			$fieldStr = $fieldStr . " if(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)>0,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0),null) as 'StockQty',ShortStockQty,Sale30Qty,SaleTotalQty,";
			$fieldStr = $fieldStr . " if(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0)+ifnull(TargetQty,0)<1,0,1) InStore,0 as 'Check'";
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
			$sqlstr = $sqlstr . " ShortStockQty,Sale30Qty,SaleTotalQty,0 as 'Check'";
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