<?php
namespace Home\Controller;

class IncentStaController extends \Think\Controller {

	public function getDeptMemRank() {
		
		$deptcode = getInputValue('deptcode');
		$startdate = getInputValue('startdate');
		$enddate = getInputValue('enddate');
		$pagestr = getInputValue('page','1,1000');
		
		$rs=D('ScoreObject')
		->getDeptMemYScoreRank($deptcode,$startdate,$enddate,$pagestr);

		return $this -> ajaxReturn($rs);
	}

	public function getGlobalMemRank() {

		$startdate = getInputValue('startdate');
		$enddate = getInputValue('enddate');
		$pagestr = getInputValue('page','1,10');

		
		$rs=D('ScoreObject')
		->getGlobalMemYScoreRank($startdate,$enddate,$pagestr);

		return $this -> ajaxReturn($rs);
	}
	

	

}
?>