<?php
namespace Home\Model;
use Think\Model;

class WageObjectModel extends Model {

	/*
	 * 查询员工所在部门的工资政策
	 * */
	public function getStaffWagePolicy($staffcode) {
		$condition['RelType'] = '部门员工';
		$condition['StaffCode'] = $staffcode;
		$deptcode = M('deptstaffs',"",getMyCon()) 
		-> where($condition) 
		-> getField('DeptCode');
		
		$wagelevel = M('staffs',"",getMyCon()) 
		-> where($condition) 
		-> getField('WageLevel');

		return M('deptwage',"",getMyCon()) 
		-> where(array('DeptCode' => $deptcode, 'WageLevel' => $wagelevel)) 
		-> select();
	}

	/*
	 * 查询员工的工资
	 * */
	public function getStaffSalary($staffcode, $tscore, $safetscore, $targetscore,$basicsalarypertscore) {
		$SumSalary = 0;

		//1.0当日表现
		$wagepolicy = M("deptwage","",getMyCon())
		->getStaffWagePolicy($staffcode);

		if (count($wagepolicy) > 0) {

			$SumSalary = (int)$wagepolicy[0]['basicsalary'] + (int)$wagepolicy[0]['dutysalary'];

			$arr_SalaryPerTScore = explode('|', $wagepolicy[0]['wagelevelratios']);
			for ($i = 0; $i < count($arr_SalaryPerTScore); $i++) {
				$SPTScore = (int)($arr_SalaryPerTScore[$i] * $basicsalarypertscore);
				$arr_SalaryPerTScore[$i] = $SPTScore;
				$arr_ScoreLevelRanges[$i] = ($i == 0) ? $safetscore : (int)($targetscore * $arr_ScoreLevelRanges[$i]);
			}

			$arr_ScoreLevelRanges = explode('|', $wagepolicy[0]['scorelevelranges']);
			for ($i = 0; $i < count($arr_ScoreLevelRanges); $i++) {
				$arr_ScoreLevelRanges[$i] = ($i == 0) ? $safetscore : (int)($targetscore * $arr_ScoreLevelRanges[$i]);
			}

			//2.4累计工资
			$len = count($arr_ScoreLevelRanges);
			$arr_ScoreLevelRanges[$len] = 1000000;
			p($arr_SalaryPerTScore);
//			p($arr_ScoreLevelRanges);
			//累计到今日的工资
			for ($i = 1; $i <= $len; $i++) {
				//			echo 'max(0,min(' . $tscore .','. $arr_ScoreLevelRanges[$i] . ')-' .$arr_ScoreLevelRanges[$i-1] . ')*' .$arr_SalaryPerTScore[$i-1] . '<br/>';
				$SumSalary += max(0, min($tscore, $arr_ScoreLevelRanges[$i]) - $arr_ScoreLevelRanges[$i - 1]) * $arr_SalaryPerTScore[$i - 1];
			}
		}		
		return $SumSalary;
	}

}
?>
