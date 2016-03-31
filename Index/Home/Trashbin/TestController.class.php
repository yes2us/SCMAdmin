<?php
namespace Home\Controller;


class TestController extends \Think\Controller {
	public function Test() {
		
		$filename = "D:\\phpStudy4IIS\\WWW\\POAAdmin\\import-excel-php\\grid.xls";
		importExcel2DB($filename);

//   p(D("StaffObject")->getStaffEvents('48024'));
//	
	
//		upload2OSS("eekavip","andriod.jpg","D:\\网盘\\[Pictures]\\mmexport1431657043318.jpg");
		
//		$dmb = D("StaffObject");
//		$condition['StaffCode']='48024';
//		$rs = $dmb->getStaffRelDepts($condition);
//		dump($rs);

	}
}

?>