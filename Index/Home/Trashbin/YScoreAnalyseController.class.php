<?php
namespace Home\Controller;

class YScoreAnalyseController extends \Think\Controller {

	public function getDeptListYScoreRank() {
		$dbm = D('ScoreObject');
		$deptcode = getInputValue("DeptCode");
		$startdate = getInputValue("StartDate");
		$enddate = getInputValue("EndDate");
		$pagestr = getInputValue("Page");
		
		if ($deptcode != 'null') {
			$rs = $dbm -> getDeptMemYScoreRank($deptcode, $startdate, $enddate, $pagestr);
		} else {
			$rs = $dbm -> getGlobalMemYScoreRank($startdate, $enddate, $pagestr);
		}
 
        return $this -> ajaxReturn($rs);
	}

	public function getGlobalMemRank() {
	
		$startdate = getInputValue("startdate");
		$enddate = getInputValue("enddate");
		$pagestr = getInputValue("page","1,10");
		
		$rs= D('ScoreObject')
		->getGlobalMemYScoreRank($startdate,$enddate,$pagestr);

		return $this -> ajaxReturn($rs);
	}
	
		public function getMyYScoreStructure() {
		$dbm = D('ScoreObject');
		
		$staffcode = getInputValue("StaffCode");
		$startdate = getInputValue("StartDate",date('Y-m-d'));
		$enddate = getInputValue("EndDate",date('Y-m-d'));

		$giveOutScoreQty = $dbm
		->field('sum(case when yscore>0 then yscore else 0 end) pscoreqty,sum(case when yscore<0 then -yscore else 0 end) nscoreqty')
		->where("staffcode='". $staffcode . "' and staffcode<>auditor and recordtime>='" . $startdate . "' and recordtime<='" . $enddate . "'")
		->select();
		$response['giveOutScoreQty'] = $giveOutScoreQty;	
		
		
		$exchangeinfo=$dbm->field("sum(case when isexchange=1 then isnull(yscore,0) else 0 end) exchanged,sum(case when isexchange=0 then isnull(yscore,0) else 0 end) unexchanged")
		->where("staffcode='". $staffcode . "'")
		->select();	
//		echo $dbm->_sql();	
		$response['exchangeinfo'] = $exchangeinfo;		
		
		
//		setTag('tag', json_encode($_POST));
		$scorestructure = $dbm->getStaffScoreStructure($_POST['Level'],$staffcode,$startdate);
		$response['scorestructure'] = $scorestructure;
//		dump($response);
		return $this -> ajaxReturn($response);
	}

	public function getDeptListYScoreStructure() {
		$dbm = D('ScoreObject');
		
		if (getInputValue('DeptCode') != 'null') {
			$condition['DeptCode'] = getInputValue('DeptCode');
		} else {
			$condition['_string'] = " DeptCode in (select deptcode from deptstaffs where staffcode='" . getInputValue('StaffCode') . "' and RelType='审核人')";
		}
		
		$exchangeinfo=$dbm
		->field("sum(case when isexchange=1 then isnull(yscore,0) else 0 end) exchanged,sum(case when isexchange=0 then isnull(yscore,0) else 0 end) unexchanged")
		->where($condition)
		->select();	
		
//		echo $dbm->_sql();	
		$response['exchangeinfo'] = $exchangeinfo;		
		
		$startdate = getInputValue('StartDate');
		$enddate = getInputValue('EndDate');

			
		$scorestructure = $dbm->getDeptListScoreStructure($_POST['Level'],$condition,$startdate,$enddate);
		$response['scorestructure'] = $scorestructure;
//		dump($response);
		return $this -> ajaxReturn($response);
	}

	public function getIncentTaskRank()
	{
		
		$YearMonth = getInputValue("YearMonth");
		$PageIndex = getInputValue('PageIndex');
		$PageLen = getInputValue('PageLen');
		
		$sqlstr = M("sqllist","",getMyCon())
		->where("SQLIndex='SQL_IncentTaskRank'")
		->getField("SQLCode");
		
		$sqlstr = str_replace('@parm1', $YearMonth, $sqlstr);
		$sqlstr = str_replace('@parm2', $PageIndex, $sqlstr);
		$sqlstr = str_replace('@parm3', $PageLen, $sqlstr);
//		setTag('sqlstr', $sqlstr);
//		echo $sqlstr;
//		die();
       	$Model = new \Think\Model("",getMyCon());
		$rs = $Model->query($sqlstr);
		
		return $this -> ajaxReturn($rs);
	}
}
?>