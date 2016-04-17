<?php
namespace Home\Controller;

class WBProdMngController extends \Think\Controller {
// 获得产品列表
	public function getProductList() {
		if(isset($_POST["SKCCode"])) $condition["SKCCode"] = $_POST["SKCCode"];
		
		if(isset($_POST["BrandName"])) $condition["BrandName"] = $_POST["BrandName"];
		if(isset($_POST["YearName"])) $condition["YearName"] = $_POST["YearName"];
		if(isset($_POST["SeriesName"])) $condition["SeriesName"] = $_POST["SeriesName"];

		if(isset($_POST["SeasonName"])) $condition["SeasonName"] = $_POST["SeasonName"];
		if(isset($_POST["SeasonStageName"])) $condition["SeasonStageName"] = $_POST["SeasonStageName"];
		if(isset($_POST["MainTypeName"])) $condition["MainTypeName"] = $_POST["MainTypeName"];

		$fieldstr = "_Identify,IsStopProduce,IsStopReplenish,IsStopAnalyze,SKCCode,SKCName,ProductCode,";
		$fieldstr = $fieldstr . "ProductName,ColorCode,ColorName,TicketPrice,VCRatio,BrandCode,BrandName,";
		$fieldstr = $fieldstr . "SeriesCode,SeriesName,YearCode,YearName,SeasonCode,SeasonName,SeasonStageCode,";
		$fieldstr = $fieldstr . "SeasonStageName,MainTypeCode,MainTypeName,SubTypeCode,SubTypeName,";
		$fieldstr = $fieldstr . "ProductTypeCode,ProductTypeName,SubType1Code,SubType2Code,SubType3Code";
	
		$pagestr = getInputValue("Page","1,1000");
		$fieldstr = getInputValue("FieldStr",$fieldstr);
		
		$rs = M('bskc','',getMyCon())
		->field($fieldstr)
		->where($condition)
		->page($pagestr)
		->select();

//		dump($rs);
		return $this -> ajaxReturn($rs);
	}
	
	
	//获得产品的销售
	public function getProdSale() {
		$condition["SKUCode"] = array("like",getInputValue("SKCCode","DK52718510165"));
		$condition["PartyCode"] = getInputValue("PartyCode","ZZ10082");
		$pagestr = getInputValue("Page","1,1000");	
		$rs = M('bsale','',getMyCon())
		->field("SKUCode,SaleBillQty,SaleBillMoney,SaleBillDiscount")
		->where($condition)
		->page($pagestr)
		->select();
//		p(M('bsale','',getMyCon())->_sql());
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}
	
	public function getBrandList() {
		$rs = M('bskc','',getMyCon())
		->field("brandcode as id ,brandname as value")
		->where($condition)
		->distinct(true)
		->select();
		
		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs = array_reverse($rs);
		return $this -> ajaxReturn($rs);
	}

	public function getYearList() {
		$rs = M('bskc','',getMyCon())
		->field("yearcode as id ,yearname as value")
		->where($condition)
		->distinct(true)
		->select();
	
		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs = array_reverse($rs);
		return $this -> ajaxReturn($rs);
	}	
	
	public function getSeasonList() {
		$rs = M('bskc','',getMyCon())
		->field("seasoncode as id ,seasonname as value")
		->where($condition)
		->distinct(true)
		->select();
		
		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs = array_reverse($rs);
		
		return $this -> ajaxReturn($rs);
	}	
	
	
	//获得货品运作指标
	public function getSKCIndex() {
		if(isset($_POST["SKCCode"])) $condition["SKCCode"] = getInputValue("SKCCode");
		
		if(isset($_POST["BrandName"])) $condition["BrandName"] = getInputValue("BrandName");
		if(isset($_POST["YearName"])) $condition["YearName"] = getInputValue("YearName");
		if(isset($_POST["SeriesName"])) $condition["SeriesName"] = getInputValue("SeriesName");

		if(isset($_POST["SeasonName"])) $condition["SeasonName"] = getInputValue("SeasonName");
		if(isset($_POST["SeasonStageName"])) $condition["SeasonStageName"] = getInputValue("SeasonStageName");
		if(isset($_POST["MainTypeName"])) $condition["MainTypeName"] = getInputValue("MainTypeName");

		$pagestr = getInputValue("Page","1,1000");
		
		$rs = M('vwskcindexsum','',getMyCon())
		->where($condition)
		->page($pagestr)
		->select();

		
//		dump(M('vwskcindexsum','',getMyCon())->_sql());
		return $this -> ajaxReturn($rs);
	}


//获得货品运作指标- 明细到门店
	public function getSKCIndexItem() {
		$condition["SKCCode"] = getInputValue("SKCCode","1326723155"); 
		
		$rs = M('zdimskc','',getMyCon())
		->where($condition)
		->select();
		
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}

}
?>