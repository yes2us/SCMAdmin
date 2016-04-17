<?php
namespace Home\Controller;

class TeamJudgeController extends \Think\Controller {
    public function getMyJudgement(){
    	$dbm = M("viewstaffjudgevalue","",getMyCon());
		$condition['StaffCode'] = getInputValue('StaffCode');
		
		$rs = $dbm-> where($condition)->select();
		
		return $this -> ajaxReturn($rs);
    }

    public function getTeamJudgeRank(){
		$condition['DeptCode'] = getInputValue('DeptCode');
		$pagestr = getInputValue('page','1,20');

		$rs = D("JudgeObject")
		->getTeamJudgeRank($condition,$pagestr);
//		dump($rs);
		return $this -> ajaxReturn($rs);
    }
	
	public function getLastJudgeDate() {
		$LastJudgeDate = D("JudgeObject")
		->getLastJudgeDate(getInputValue('DeptCode'));
		
        return $this -> ajaxReturn($LastJudgeDate);	
	}

	public function saveJudgementResult() {
		$data['RecordDate'] = getInputValue('RecordDate');
		$data['JudgeDate'] = getInputValue('JudgeDate');
		$data['JudgerCode'] = getInputValue('JudgerCode');
		$data['DeptCode'] = getInputValue('DeptCode');

		$dbm = M("teamjudgement","",getMyCon());
		//		setTag('judge',json_encode($_POST));

		foreach ($_POST['items'] as $item) {
			$deletecondition['JudgeDate'] = getInputValue('JudgeDate');
			$deletecondition['JudgerCode'] = getInputValue('JudgerCode');
			$deletecondition['StaffCode'] = $item['StaffCode'];
			$dbm->where($deletecondition)->delete();
			//			setTag('StaffCode',$item['StaffCode']);
			//			setTag('JudgeValue',$item['JudgeValue']);

			$data['StaffCode'] = $item['StaffCode'];
			$data['JudgeValue'] = $item['JudgeValue'];
			$data['JudgerWeight'] = 1;
			$dbm -> data($data) -> add();
		}

		return $this -> ajaxReturn('保存成功');
	}

}
?>