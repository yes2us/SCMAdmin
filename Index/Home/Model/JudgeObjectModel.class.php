<?php
namespace Home\Model;
use Think\Model;

class JudgeObjectModel extends Model {

	public function getTeamJudgeRank($condition,$pagestr) {

			$rs = M('viewstaffjudgevalue',"",getMyCon())
			-> where($condition) 
			-> order('JudgeDate desc, AVGJudgeValue desc') 
			-> page($pagestr)
			-> select();
			
//			echo $this->_sql();
			return $rs;
	}

	public function getLastJudgeDate($deptcode) {

		$condition['DeptCode'] = $deptcode;
		$rs = M('teamjudgement',"",getMyCon()) 
		-> where($condition) 
		-> field('max(JudgeDate) lastJudgeDate') -> having('max(JudgeDate) is not null') -> select();

		if (count($rs) > 0) {
			return $rs[0]['lastjudgedate'];
		} else {
			return '没有记录';
		}

	}

}
?>
