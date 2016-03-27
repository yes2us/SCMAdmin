<?php
namespace Home\Controller;

class DeptMngController extends \Think\Controller {

	public function getRecentTScoreItems() {

		$dbm = D('ScoreObject');
		
		$pagestr = getInputValue("Page",'1,8');
		$enddate = getInputValue("EndDate",date('Y-m-d'));
		
		$condition['RecordTime'] = array('elt', $enddate);
		$condition['TScore'] = array( array('gt', 0), array('lt', 0), 'or');

		if ($_POST['DeptCode'] != 'null') {
			$condition['DeptCode'] = $_POST['DeptCode'];
		} else {
			$condition['_string'] = " DeptCode in (select deptcode from deptstaffs where staffcode='" . $_POST['StaffCode'] . "' and RelType='审核人')";
			//$condition['_string'] = " DeptCode in (select deptcode from deptstaffs where staffcode='Admin' and RelType='审核人')";
		}

		$rs = $dbm -> getScoreRecords($condition, $pagestr, null);
		//      echo $dbm->_sql();
		return $this -> ajaxReturn($rs);
	}

	public function getRecentYScoreItems() {

		$dbm = D('ScoreObject');
		
		$pagestr = getInputValue("Page",'1,8');
		$fieldstr = getInputValue("field");
		$enddate = getInputValue("EndDate",date('Y-m-d'));

		
		$condition['RecordTime'] = array('elt', $enddate);
		$condition['YScore'] = array( array('gt', 0), array('lt', 0), 'or');

		if ($_POST['DeptCode'] != 'null') {
			$condition['DeptCode'] = $_POST['DeptCode'];
		} else {
				$condition['_string'] = " DeptCode in (select distinct deptcode from deptstaffs where staffcode='" . $_POST['StaffCode'] . "' and RelType='审核人')";
//			$condition['_string'] = " DeptCode in (select deptcode from deptstaffs where staffcode='Admin' and RelType='审核人')";
		}

		//setTag('condition', json_encode($condition));

		$rs = $dbm -> getScoreRecords($condition, $pagestr, $fieldstr);
		//      echo $dbm->_sql();
		return $this -> ajaxReturn($rs);
	}

	/**
	 * T分Y分部门排名
	 */
	public function getDeptListTScoreRank() {
	
		$dbm = D('ScoreObject');
		
		$deptcode = getInputValue("DeptCode");
		$pagestr = getInputValue("Page");
		$startdate = getInputValue("StartDate");
		$enddate = getInputValue("EndDate");		


		if ($deptcode != 'null') {
			$rs = $dbm -> getDeptMemTScoreRank($deptcode, $startdate, $enddate, $pagestr);
		} else {
			$rs = $dbm -> getGlobalMemTScoreRank($startdate, $enddate, $pagestr);
		}

		return $this -> ajaxReturn($rs);
	}

	
	/**
	 * T分Y分部门排名
	 */
	public function getDeptListYScoreRank() {
	
		$dbm = D('ScoreObject');
		
		$deptcode = getInputValue("DeptCode");
		$pagestr = getInputValue("Page");
		$startdate = getInputValue("StartDate");
		$enddate = getInputValue("EndDate");	

		if ($deptcode != 'null') {
			$rs = $dbm -> getDeptMemYScoreRank($deptcode, $startdate, $enddate, $pagestr);
		} else {
			$rs = $dbm -> getGlobalMemYScoreRank($startdate, $enddate, $pagestr);
		}

		return $this -> ajaxReturn($rs);
	}

	/**
	 * 获得得分趋势
	 */
	public function getDeptListScoreTrend() {
		
		$deptcode = getInputValue("DeptCode");		
		$staffcode = getInputValue("StaffCode");
		$startdate = getInputValue("StartDate");
		$enddate = getInputValue("EndDate");	
		
		$rs = D('ScoreObject') 
		-> getDeptListScoreTrend($staffcode, $deptcode, $startdate, $enddate);
		return $this -> ajaxReturn($rs);
	}

	/**
	 * 获得"我"所有部门的得分统计
	 */
	public function getDeptListInfo() {
		
		$Model = new \Think\Model("",getMyCon());
				
		$viewercode = getInputValue("ViewerCode",'Admin');
		$startdate = getInputValue("StartDate",'2015-08-01');
		$enddate = getInputValue("EndDate",'2015-08-31');	
		$yearmonth = getInputValue("YearMonth",'2015-08');	
		
		$sqlstr = "select a.DeptCode,a.DeptSName,a.StaffCode,a.StaffName,";
		$sqlstr = $sqlstr . " convert(decimal(10,0),SUM(isnull(TValue,0)))/10000 TValue,convert(decimal(10,0),SUM(isnull(YScore,0))) YScore";
		$sqlstr = $sqlstr . " from ViewScoreRecords as a inner join VW_DeptRelPersons as b on a.DeptCode = b.DeptCode and b.StaffCode='" . $viewercode ."' ";
		$sqlstr = $sqlstr . " where a.AuditState='批准' and a.RecordTime>='" . $startdate . "' and a.RecordTime<='" . $enddate . "'";
		$sqlstr = $sqlstr . " group by a.DeptCode,a.DeptSName,a.StaffCode,a.StaffName";
		$sqlstr = $sqlstr . " order by a.DeptCode,SUM(isnull(TValue,0)) desc";
		
//		echo $sqlstr;

		$StaffItem = $Model->query($sqlstr);
		
		$sqlstr = "select distinct a.DeptCode,a.DeptSName,a.StaffCode,a.StaffName,ISNULL(c.TotalTValueTarget/10000,-1) MonthTarget,c.StaffNum";
		$sqlstr = $sqlstr . " from ViewStoreMngers as a inner join VW_DeptRelPersons as b";
		$sqlstr = $sqlstr . " on a.DeptCode = b.DeptCode and b.StaffCode='" . $viewercode . "' left join DeptGoals as c";
		$sqlstr = $sqlstr . " on a.DeptCode = c.DeptCode and c.YearMonth = '" .  $yearmonth ."'";
		
		$StoreInfo = $Model->query($sqlstr);
		
		for ($i=0; $i<count($StoreInfo); $i++) {
			
		  $DeptCode = $StoreInfo[$i]['deptcode'];
		  $MonthTarget = $StoreInfo[$i]['monthtarget'];
		  $MngerCode = $StoreInfo[$i]['staffcode'];
//		  $StaffCount = $StoreInfo[$i]['staffnum']-1;
		  $_condition['BelongDeptCode'] = $DeptCode;
		  $_condition['IsOnJob'] = 1;
		  
		  $StaffCount = M('Staffs','',getMyCon())->where($_condition)->count();
		  $StaffCount = $StaffCount-1;
		  
		  $MngerYScore = 0;
		  
		  if ($StaffCount<1) $StaffCount = 1;
		  
		  $PersonalMonthTarget = $MonthTarget/$StaffCount;
		  
		  
		  $TotalTValue = 0;
		  $TotalYScore = 0;
		  
		  $StoreInfo[$i]['staffs']=[];
		  
		  $k = 0;	
		  for($j=0; $j<count($StaffItem); $j++)
		  {
		  	   if($StaffItem[$j]['deptcode']==$DeptCode)
			   {
			   		$TotalTValue = $TotalTValue + (float)$StaffItem[$j]['tvalue'];
			   	 $StaffItem[$j]['tvalue'] = str_pad((string)round($StaffItem[$j]['tvalue'],2),6,' ',STR_PAD_LEFT);
			   	  if($PersonalMonthTarget<1)
			   	  {
			   	  	 $StaffItem[$j]['tvalueprog'] = 0;
			   	  }	
					else
					{
						$StaffItem[$j]['tvalueprog'] = str_pad((string)round(100*(float)$StaffItem[$j]['tvalue']/$PersonalMonthTarget,0),1,'0',STR_PAD_LEFT);
					}
			   	  
				   if($StaffItem[$j]['staffcode']==$MngerCode)
				   {
				   		$MngerYScore = (int)$StaffItem[$j]['yscore'];
				   }
					else
					{
						$TotalYScore = $TotalYScore + (int)$StaffItem[$j]['yscore'];
					}
					
					$StoreInfo[$i]['staffs'][$k++] = $StaffItem[$j];
			   }
		  }
		  
//		  	usort($StoreInfo[$i], function($a, $b) {
//          $al = (int)$a['tvalueprog'];
//          $bl = (int)$b['tvalueprog'];
//          if ($al == $bl)
//              return 0;
//          return ($al > $bl) ? -1 : 1;
//      	});
			
		  $StoreInfo[$i]['totaltvalue'] = str_pad((string)round($TotalTValue,1),5,' ',STR_PAD_LEFT);
		  if($MonthTarget>1)
		  {
		  	$StoreInfo[$i]['totaltvalueprog'] = round(100*$TotalTValue/$MonthTarget,0);
		  }
		else
		{
			$StoreInfo[$i]['totaltvalueprog'] = 0;		
		}
		  
		  $StoreInfo[$i]['mngercode'] = $MngerCode;
		  $StoreInfo[$i]['mngeryscore'] = $MngerYScore;
		  $StoreInfo[$i]['avstaffyscore'] = ceil($TotalYScore/$StaffCount);
		
		}
		
		usort($StoreInfo, function($a, $b) {
            $al = $a['totaltvalueprog'];
            $bl = $b['totaltvalueprog'];
            if ($al == $bl)
                return 0;
            return ($al > $bl) ? -1 : 1;
        });
		
//		dump($StoreInfo);
//		die();
		return $this -> ajaxReturn($StoreInfo);
	}
	
	/**
	 * 获得员工得分统计
	 */
	public function getDeptStaffScoreInfo() {
		
		$Model = new \Think\Model("",getMyCon());
		
		$staffcode = getInputValue("StaffCode",'47669');
		$startdate = getInputValue("StartDate",'2015-06-01');
		$enddate = getInputValue("EndDate",'2015-06-30');	
		
		$sqlstr = "select Event,convert(decimal(8,0),sum(YScore)) YScore from ScoreRecords";
		$sqlstr = $sqlstr . " where AuditState='批准' and Event<>'导购T分T值' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "' and StaffCode = '" . $staffcode ."'";
		$sqlstr = $sqlstr . " group by Event";
		
//		echo $sqlstr;
		
		$rs = $Model->query($sqlstr);
		return $this -> ajaxReturn($rs);
	}
}
?>