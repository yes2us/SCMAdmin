<?php
namespace Home\Controller;

class WBProdMngController extends \Think\Controller {
// 获得产品列表
	public function getProductList() {
		if(isset($_POST["ProductColorCode"])) $condition["ProductColorCode"] = array("like",$_POST["ProductColorCode"]);
		
		if(isset($_POST["BrandName"])) $condition["BrandName"] = $_POST["BrandName"];
		if(isset($_POST["YearName"])) $condition["BrandName"] = $_POST["BrandName"];
		if(isset($_POST["SeriesName"])) $condition["BrandName"] = $_POST["BrandName"];

		if(isset($_POST["SeasonName"])) $condition["SeasonName"] = $_POST["SeasonName"];
		if(isset($_POST["SeasonStageName"])) $condition["SeasonStageName"] = $_POST["SeasonStageName"];
		if(isset($_POST["MainTypeName"])) $condition["MainTypeName"] = $_POST["MainTypeName"];

		$pagestr = getInputValue("Page","1,1000");
		
		$rs = M('vwproductcolor','',getMyCon())
		->where($condition)
		->page($pagestr)
		->select();

		
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}
	
	
	//获得产品的销售
	public function getProdSale() {
		$condition["SKUCode"] = array("like",getInputValue("ProductColorCode","DK52718510165"));
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
	
	
	//获得货品运作指标
	public function getProdIndicator() {
		if(isset($_POST["ProductColorCode"])) $condition["ProductColorCode"] = array("like",$_POST["ProductColorCode"]);
		
		if(isset($_POST["BrandName"])) $condition["BrandName"] = $_POST["BrandName"];
		if(isset($_POST["YearName"])) $condition["BrandName"] = $_POST["BrandName"];
		if(isset($_POST["SeriesName"])) $condition["BrandName"] = $_POST["BrandName"];

		if(isset($_POST["SeasonName"])) $condition["SeasonName"] = $_POST["SeasonName"];
		if(isset($_POST["SeasonStageName"])) $condition["SeasonStageName"] = $_POST["SeasonStageName"];
		if(isset($_POST["MainTypeName"])) $condition["MainTypeName"] = $_POST["MainTypeName"];

		$pagestr = getInputValue("Page","1,1000");
		
		$rs = M('vwprodindicatorsum','',getMyCon())
		->where($condition)
		->page($pagestr)
		->select();

		
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}


//获得货品运作指标- 明细到门店
	public function getProdIndicatorItem() {
		$condition["ProductColorCode"] = getInputValue("ProductColorCode","1326723155"); 
		
		$rs = M('dprodindicator','',getMyCon())
		->where($condition)
		->select();
		
//		dump($rs);
		return $this -> ajaxReturn($rs);
	}
}
?>