<?php
namespace Home\Controller;

class MyPageController extends \Think\Controller {

	public function getRecentTScoreItems() {

		$dbm = D('ScoreObject');
		$pagestr = getInputValue('page','1,20');

		$fieldstr = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,EventRemark,RecordTime";

		$rs = $dbm -> getScoreRecords($_POST['condition'], $pagestr, $fieldstr);
//		$rs = json_encode($rs);
		return $this -> ajaxReturn($rs);
	}

	public function getRecentYScoreItems() {

		$pagestr = getInputValue('page','1,8');
		$fieldstr = getInputValue('field');
	
		$condition['RecordTime'] = array('elt', getInputValue('EndDate',date('Y-m-d')));
		$condition['StaffCode'] = getInputValue('StaffCode');
		$condition['YScore'] = array( array('gt', 0), array('lt', 0), 'or');
		$rs = D('ScoreObject') 
		-> getScoreRecords($condition, $pagestr, $fieldstr);

		return $this -> ajaxReturn($rs);
	}

	/**
	 * 统计当日T分情况
	 */
	public function getCurrentTScoreSta() {
		$StaffCode = getInputValue('StaffCode');
		$CurDate = getInputValue('CurDate');
		$DeptCode = getInputValue('DeptCode');
		$YearMonth = getInputValue('YearMonth');
		$FirstDateOfMonth = getInputValue('FirstDateOfMonth');

		//		$StaffCode = 'Admin';
		//		$CurDate = '2015-03-14';
		//		$DeptCode = '店铺1';
		//		$YearMonth = '2015-03';
		//		$FirstDateOfMonth = '2015-03-01';

		//1.0当日表现
		$response['tdTitle'] = "当日表现[分值:0=>0]";
		//1.1当日T分
		$response['tdTScore'] = 0;
		//1.2最新分值
		$response['tdSalaryPerTScore'] = 0;
		//1.3当日客单件
		$response['tdAvgQtyPerSale'] = 0;
		//1.4当日工资
		$response['tdSalary'] = 0;
		//1.5当日搭档
		$response['tdPartners'] = null;

		//2.0当月表现
		$response['mtTitle'] = "当月表现[分数:0=>0]";
		//2.1累计T分
		$response['mtTScore'] = 0;
		//2.2累计客单件
		$response['mtAvgQtyPerSale'] = 0;
		//2.3任务与时间比
		$response['mtTaskProcess'] = 0;
		//2.4累计工资
		$response['mtSalary'] = 0;

		//3.1保底分
		$response['mtSafeTScore'] = 0;
		//3.2目标分
		$response['mtTargetTScore'] = 0;
		//3.3基本分值
		$response['mtBasicSalaryPerTScore'] = 0;
		//3.4单分业绩
		$response['mtSalePerTScore'] = 0;

		//?????????????????店长要单独计算其T分
		/**
		 * 当日T分之和，客单件
		 */
		$dmb = M('scorerecords',"",getMyCon());
		$condition['StaffCode'] = $StaffCode;
		$condition['RecordTime'] = array('eq', $CurDate);
		$condition['TScore'] = array( array('gt', 0), array('lt', 0), 'or');
		$rs = $dmb 
		-> field('RecordTime,SUM(TScore) TotalTScore,AVG(TSaleQty) AvgQtyPerSale') 
		-> where($condition) 
		-> group('RecordTime') 
		-> select();
		
		if (count($rs) > 0) {
			//1.1当日T分
			$response['tdTScore'] = round((float)$rs[0]['totaltscore'], 2);
			//1.3当日客单
			$response['tdAvgQtyPerSale'] = round((float)$rs[0]['avgqtypersale'], 2);
		}

		/**
		 * 当日搭档
		 */
		unset($condition);
		$condition['ScoreRecords.StaffCode'] = $StaffCode;
		$condition['RecordTime'] = array('eq', $CurDate);
		$condition['TScore'] = array( array('gt', 0), array('lt', 0), 'or');
		$partners = $dmb 
		-> join('left join Staffs on Staffs.StaffCode = ScoreRecords.TPartner') 
		-> distinct(true) 
		-> where($condition) 
		-> field('StaffName') 
		-> select();

		if (count($partners) > 0) {
			//1.5当日搭档
			$partnerslist = "";
			foreach ($partners as $value) {
				$partnerslist = $partnerslist . $value['staffname'];
			}
			$response['tdPartners'] = $partnerslist;
		}

		/**
		 * 当月T分之和，客单件
		 */
		unset($condition);
		$condition['StaffCode'] = $StaffCode;
		$condition['TScore'] = array( array('gt', 0), array('lt', 0), 'or');
		$condition['_string'] = "RecordTime>='" . $FirstDateOfMonth . "' and RecordTime<='" . $CurDate . "'";
		$rs = $dmb -> field('SUM(TScore) TotalTScore,AVG(TSaleQty) AvgQtyPerSale') -> where($condition) -> having('SUM(TScore) is not null') -> select();

		if (count($rs) > 0) {
			//2.1累计T分
			$response['mtTScore'] = round((float)$rs[0]['totaltscore'], 2);
			//2.2累计客单
			$response['mtAvgQtyPerSale'] = round((float)$rs[0]['avgqtypersale'], 2);
		}

		/**
		 * 当月T分参数(分值,目标分,基本分等)
		 */
		$dbm = M('deptgoals',"",getMyCon());
		unset($condition);

		$condition['DeptCode'] = $DeptCode;
		$condition['YearMonth'] = $YearMonth;
		$rs = $dbm 
		-> where($condition) 
		-> select();
		//setTag('condition', json_encode($condition));

		if (count($rs) > 0) {
			//3.1保底分
			$response['mtSafeTScore'] = (int)$rs[0]['singlebasictscore'];
			//3.2目标分
			$response['mtTargetTScore'] = (int)$rs[0]['singletscoretarget'];
			//3.3基本分值
			$response['mtBasicSalaryPerTScore'] = (int)$rs[0]['basicsalarypertscore'];
			//3.4单分业绩
			$response['mtSalePerTScore'] = (int)$rs[0]['tvaluepertscore'];
		}
		//2.3任务与时间比
		$response['mtTaskProcess'] = round((float)($response['mtTScore'] / $response['mtTargetTScore']), 2);

		/*********************************************************************************************
		 * 获得其它参数
		 *********************************************************************************************/
		$dbm = D('WageObject');

		$fixedSalary = 0;
		//1.0当日表现
		$wagepolicy = $dbm -> getStaffWagePolicy($StaffCode);
		if (count($wagepolicy) > 0) {

			$fixedSalary = (int)$wagepolicy[0]['basicsalary'] + (int)$wagepolicy[0]['dutysalary'];

			$tdTitle = "当日表现[分值:";
			$arr_SalaryPerTScore = explode('|', $wagepolicy[0]['wagelevelratios']);
			for ($i = 0; $i < count($arr_SalaryPerTScore); $i++) {
				$SPTScore = (int)($arr_SalaryPerTScore[$i] * $response['mtBasicSalaryPerTScore']);
				$tdTitle = $tdTitle . (($i == 0) ? $SPTScore : "=>" . $SPTScore);
				$arr_SalaryPerTScore[$i] = $SPTScore;
				$arr_ScoreLevelRanges[$i] = ($i == 0) ? $response['mtSafeTScore'] : (int)($response['mtTargetTScore'] * $arr_ScoreLevelRanges[$i]);
			}
			$response['tdTitle'] = $tdTitle . ']';

			$mtTitle = "当月表现[分数:";
			$arr_ScoreLevelRanges = explode('|', $wagepolicy[0]['scorelevelranges']);
			for ($i = 0; $i < count($arr_ScoreLevelRanges); $i++) {
				$arr_ScoreLevelRanges[$i] = ($i == 0) ? $response['mtSafeTScore'] : (int)($response['mtTargetTScore'] * $arr_ScoreLevelRanges[$i]);
				$mtTitle = $mtTitle . (($i == 0) ? $arr_ScoreLevelRanges[$i] : "=>" . $arr_ScoreLevelRanges[$i]);
			}
			$response['mtTitle'] = $mtTitle . ']';

		}

		//1.2最新分值
		$response['tdSalaryPerTScore'] = 0;
		$len = count($arr_ScoreLevelRanges);
		for ($i = $len - 1; $i >= 0; $i--) {
			if ($response['mtTScore'] >= $arr_ScoreLevelRanges[$i]) {
				$response['tdSalaryPerTScore'] = $arr_SalaryPerTScore[$i];

				$replace = "<span class='mui-badge mui-badge-danger'>" . $response['tdSalaryPerTScore'] . "</span>";
				$response['tdTitle'] = str_replace((string)($response['tdSalaryPerTScore']), $replace, $response['tdTitle']);

				$replace = "<span class='mui-badge mui-badge-primary'>" . $arr_ScoreLevelRanges[$i] . "</span>";
				$response['mtTitle'] = str_replace((string)($arr_ScoreLevelRanges[$i]), $replace, $response['mtTitle']);
				if ($i < $len - 1) {
					$replace = "<span class='mui-badge mui-badge-primary'>" . $arr_ScoreLevelRanges[$i + 1] . "</span>";
					$response['mtTitle'] = str_replace((string)($arr_ScoreLevelRanges[$i + 1]), $replace, $response['mtTitle']);
				}
				break;
			}
		}

		//2.4累计工资
		$len = count($arr_ScoreLevelRanges);
		$arr_ScoreLevelRanges[$len] = 1000000;

		//累计到今日的工资
		$_toTdSumSalary = $fixedSalary;
		for ($i = 1; $i <= $len; $i++) {
			//			echo 'max(0,min(' . $response['mtTScore'] .','. $arr_ScoreLevelRanges[$i] . ')-' .$arr_ScoreLevelRanges[$i-1] . ')*' .$arr_SalaryPerTScore[$i-1] . '<br/>';
			$_toTdSumSalary += max(0, min($response['mtTScore'], $arr_ScoreLevelRanges[$i]) - $arr_ScoreLevelRanges[$i - 1]) * $arr_SalaryPerTScore[$i - 1];
		}

		//累计到昨日的工资
		$_toYTdSumSalary = $fixedSalary;
		for ($i = 1; $i <= $len; $i++) {
			$_toYTdSumSalary += max(0, min($response['mtTScore'] - $response['tdTScore'], $arr_ScoreLevelRanges[$i]) - $arr_ScoreLevelRanges[$i - 1]) * $arr_SalaryPerTScore[$i - 1];
		}
		$response['mtSalary'] = $_toTdSumSalary;

		//1.4今日工资
		$response['tdSalary'] = $_toTdSumSalary - $_toYTdSumSalary;

		//可以直接调用以下函数得到工资，但是有一些重复计算
		//$sumsalary = $dbm->getStaffSalary($StaffCode,$response['mtTScore'],$response['mtSafeTScore'],$response['mtTargetTScore'],$response['mtBasicSalaryPerTScore']);

		//dump($response);
		return $this -> ajaxReturn($response);
	}

	/**!!!!!!!!!!此方法将被抛弃
	 * T分Y分部门排名
	 */
	public function getDeptMemScoreRank() {
	
		$deptcode = getInputValue('deptcode');
		$startdate = getInputValue('startdate');
		$enddate = getInputValue('enddate');
		$pagestr = getInputValue('page');

		$rs = D('ScoreObject') 
		->getDeptMemScoreRank($deptcode, $startdate, $enddate, $pagestr);

		return $this -> ajaxReturn($rs);
	}


	/**
	 * 部门Y分排名
	 */
	public function getDeptMemYScoreRank() {
		
		$deptcode = getInputValue('deptcode');
		$startdate = getInputValue('startdate');
		$enddate = getInputValue('enddate');
		$pagestr = getInputValue('page');

		$rs = D('ScoreObject') 
		-> getDeptMemYScoreRank($deptcode, $startdate, $enddate, $pagestr);

		return $this -> ajaxReturn($rs);
	}


	/**
	 * T分部门排名 : 姓名,当日T分,累计T分
	 */
	public function getDeptMemTScoreRank() {
		$dbm = D('ScoreObject');
		
		$deptcode = getInputValue('deptcode');
		$startdate = getInputValue('startdate');
		$enddate = getInputValue('enddate');
		$pagestr = getInputValue('page','1,1000');

		$response['mtDeptTRank'] = null;
		$totalTValue = 0;
		$totalTScore = 0;
		$rs = $dbm -> getDeptMemTScoreRank($deptcode, $startdate, $enddate, $pagestr);
		if (count($rs) > 0) {
			$response['mtDeptTRank'] = $rs;
			for ($i = 0; $i < count($rs); $i++) {
				$totalTValue += $rs[$i]['tvalue'] ? $rs[$i]['tvalue'] : 0;
				$totalTScore += $rs[$i]['tscore'] ? $rs[$i]['tscore'] : 0;
			}
		}
        $response['totalTValue'] = $totalTValue;
        $response['$totalTScore'] = $totalTScore;
		//		dump($rs);

		/**
		 * 当月T分参数(分值,目标分,基本分等)
		 */
		unset($condition);
		$dbm = M('deptgoals',"",getMyCon());

		$condition['DeptCode'] = $deptcode;
		$condition['YearMonth'] = substr($startdate, 0, 7);
		$rs = $dbm -> where($condition) -> select();

		if (count($rs) > 0) {
			//2.1保底分
			$response['mtSafeTScore'] = (int)$rs[0]['singlebasictscore'];
			//2.2目标分
			$response['mtTargetTScore'] = (int)$rs[0]['singletscoretarget'];
			//2.3单分业绩
			$response['mtSalePerTScore'] = (int)$rs[0]['tvaluepertscore'];
			//2.4任务进度
			$response['mtTaskProcess'] = round((float)$totalTValue / ((int)$rs[0]['totaltvaluetarget'] > 0 ? (int)$rs[0]['totaltvaluetarget'] : 1000000), 2);
			//2.5平均T分
			$response['mtAvgTScore'] = round((float)$totalTScore / ((int)$rs[0]['staffnum'] > 0 ? ((int)$rs[0]['staffnum']-0.5) : 100), 1);

		}

		return $this -> ajaxReturn($response);
	}

	/**
	 * 获得未审核的得分记录
	 */
	public function getWaitAuditTask() {
		$dbm = D('ScoreObject');
		$pagestr = getInputValue('page','1,10');
		$fieldstr = getInputValue('field');


		$condition['Auditor'] = $_POST['StaffCode'];
		$condition['AuditState'] = '待审核';
		$rs = $dbm -> getScoreRecords($condition, $pagestr, $fieldstr);

		return $this -> ajaxReturn($rs);
	}

	/**
	 * 获得未仲裁的得分记录
	 */
	public function getWaitArbitrateTask() {
		$dbm = D('ScoreObject');
		$pagestr = getInputValue('page','1,10');
		$fieldstr = getInputValue('field');

		$condition['AppealDealer'] = getInputValue('StaffCode');
		$condition['AppealState'] = '待仲裁';
		$rs = $dbm -> getScoreRecords($condition, $pagestr, $fieldstr);

		return $this -> ajaxReturn($rs);
	}

	/**
	 * 获得得分趋势
	 */
	public function getScoreTrend() {
		//		$rs =  D('ScoreObject')->getScoreTrend('Ricky','店铺1','2015-02-05','2015-03-04');
		//		dump($rs);
		$rs = D('ScoreObject') 
		-> getScoreTrend(getInputValue('StaffCode'), getInputValue('DeptCode'), getInputValue('StartDate'), getInputValue('EndDate'));
		return $this -> ajaxReturn($rs);
	}

}
?>