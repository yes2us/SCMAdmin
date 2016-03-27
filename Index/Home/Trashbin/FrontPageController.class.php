<?php
namespace Home\Controller;

class FrontPageController extends \Think\Controller{

	public function getMyRecentScores(){
		$dbtmodel = D('Scores');
		$staffcode = 'Ricky'; //I('staffcode')
		dump(I('filter'));
		if(isset($_POST['page']))
		{
			$pagestr = I('page');
			$rs = $dbtmodel->relation(true)->where(I('filter'))->page($pagestr)->Select();
		}
		else
		{
			$rs = $dbtmodel->relation(true)->where(I('filter'))->Select();
		}
		
//		dump($rs);
		$this->ajaxReturn($rs);
	}
	
	public function getTeamRecentScores(){
		$dbtmodel = D('Scores');
		if(isset(_POST['page']))
		{
			$pagestr = I('page');
			$rs = $dbtmodel->relation(true)->where(I('filter'))->page($pagestr)->Select();
		}
		else
		{
			$rs = $dbtmodel->relation(true)->where(I('filter'))->Select();	
		}
//		dump($rs);
//		$rs = json_encode($rs);  //使用了json_encode后，jquery中要使用jQuery.parseJSON(data)
		$this->ajaxReturn($rs);
	}
		
	public function getMyAuditItems(){
		$dbtmodel = D('Scores');
		$staffcode = 'Ricky'; //I('staffcode')
		$rs = $dbtmodel->relation(true)->where("staffcode='". $staffcode . "'")->page(1,10)->Select();
		//dump($rs);
		$this->ajaxReturn($rs);
	}
}	


?>