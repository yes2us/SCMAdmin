<?php
namespace Home\Controller;


class TestController extends \Think\Controller {
	public function Test() {
		$condition['PartyType'] = '分仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode,PartyName");
		p($condition);
//		import('@.Model.PartyObjectModel');
//		$PartyObjectModel = new PartyObjectModel();
		$rs = D("PartyObject")->getRegionList($condition,$pagestr,$fieldstr);
		dump($rs);
	}
	
	public function Test2(){
		$rs = D("BasicObject")->getSysPara();
		dump($rs);
	}
}

?>