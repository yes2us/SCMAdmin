<?php
namespace Home\Controller;

class DetailScoreController extends \Think\Controller {

	/**
	 * 我的相关分
	 */
	public function getMyTYScores() {
		$condition['StaffCode'] = getInputValue("staffcode");
		$condition['RecordTime'] = array('elt', getInputValue("enddate",date('Y-m-d')));
		
		$pagestr = getInputValue("page","1,10");		
		$fieldstr = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,TPartner,TSaleQty,EventRemark,RecordTime";

		$rs = D('ScoreObject') 
		-> getScoreRecords($condition, $pagestr, $fieldstr);

		//		dump($rs);
		$this -> ajaxReturn($rs);
	}

	public function getGiveOutPScores() {

		$condition['StaffCode'] = array('neq', getInputValue("staffcode"));
		$condition['GetFrom'] = getInputValue("staffcode");
		$condition['YScore'] = array('gt', 0);
		$condition['RecordTime'] = array('elt', getInputValue("enddate",date('Y-m-d')));
		
		$pagestr = getInputValue("page","1,10");	
		
		$fieldstr = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,TPartner,TSaleQty,EventRemark,RecordTime";

		$rs = D('ScoreObject') -> getScoreRecords($condition, $pagestr, $fieldstr);

		//		dump($rs);
		$this -> ajaxReturn($rs);
	}

	public function getGiveOutNScores() {
		
		$condition['StaffCode'] = array('neq', getInputValue("staffcode"));
		$condition['GetFrom'] = getInputValue("staffcode");
		$condition['YScore'] = array('lt', 0);
		$condition['RecordTime'] = array('elt', getInputValue("enddate",date('Y-m-d')));

		$pagestr = getInputValue("page","1,10");	

		$fieldstr = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,TPartner,TSaleQty,EventRemark,RecordTime";

		$rs = D('ScoreObject') -> getScoreRecords($condition, $pagestr, $fieldstr);

		$this -> ajaxReturn($rs);
	}

	/**
	 * 团队得分
	 */
	public function getTeamScores() {

		//由员工得到部门编号
		$condition['StaffCode'] = getInputValue("staffcode");
		$deptcode = M('staffs',"",getMyCon())
		->where($condition)
		->getField('BelongDeptCode');

		unset($condition);
		$condition['IsOnJob'] = 1;
		$condition['IsLocked'] = 0;
		$condition['DeptCode'] = $deptcode;
		$condition['RecordTime'] = array('elt', getInputValue("enddate",date('Y-m-d')));
		switch (getInputValue('scoretype')) {
			case 'TScore' :
				$condition['TScore'] = array('neq', 0);
				break;
			case 'PYScore' :
				$condition['YScore'] = array('gt', 0);
				break;
			case 'NYScore' :
				$condition['YScore'] = array('lt', 0);
				break;
			default :
				break;
		}

		$pagestr = getInputValue("page","1,10");	
		$fieldstr = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,TPartner,TSaleQty,EventRemark,RecordTime";

		$rs = D('ScoreObject')
		 ->getScoreRecords($condition, $pagestr, $fieldstr);

		//		dump($rs);
		$this -> ajaxReturn($rs);
	}

	/**
	 * 操作得分记录
	 */
	public function operateScoreRecord() {
		$dbm = M('scorerecords',"",getMyCon());
		$condition['RecordCode'] = getInputValue('RecordCode');

		$currentDate = date('Y-m-d', time());

		switch (getInputValue('Operation')) {

			case '批准审核' :
				$data['AuditDate'] = $currentDate;
				$data['AuditState'] = '批准';
				$dbm -> where($condition) -> save($data);
				$this -> ajaxReturn('OK');
				break;
			case '拒绝审核' :
				$data['AuditDate'] = $currentDate;
				$data['AuditState'] = '拒绝';
				$dbm -> where($condition) -> save($data);
				$this -> ajaxReturn('OK');
				break;

			case '删除得分' :
				if ($dbm -> where($condition) -> getField('AuditState') == '待审核') {
					$dbm -> where($condition) -> delete();
					$this -> ajaxReturn('OK');
				} else {
					$this -> ajaxReturn('不可删除已审核记录');
				}
				break;
			case '申诉得分' :
				$data['AppealDate'] = $currentDate;
				$data['AppealDealer'] = $_POST['AppealDealer'];
				$data['AppealState'] = '待仲裁';
				$dbm -> where($condition) -> save($data);
				//				echo $dbm->_sql() ;
				$this -> ajaxReturn('OK');
				break;

			case '同意申诉' :
				$data['AppealDealDate'] = $currentDate;
				$data['AppealState'] = '同意';
				$data['AppealDealRemark'] = $_POST['AppealDealRemark'];
				$dbm -> where($condition) -> save($data);
				$this -> ajaxReturn('OK');
				break;
			case '驳回申诉' :
				$data['AppealDealDate'] = $currentDate;
				$data['AppealState'] = '驳回';
				$data['AppealDealRemark'] = $_POST['AppealDealRemark'];
				$dbm -> where($condition) -> save($data);
				$this -> ajaxReturn('OK');
				break;
			default :
				break;
		}
	}

	public function getSubscribeScores() {
		$dbm = D('ScoreObject');
		
		$condition['RecordTime'] = array('elt', getInputValue("EndDate",date('Y-m-d')));

		$field = "RecordCode,StaffCode,StaffName,DeptSName,Event,EventType,EventScope,YScore,XScore,TValue,TScore,EventRemark,RecordTime";

		switch (getInputValue('ScoreType')) {
			case 'TScore' :
				$condition['TScore'] = array( array('gt', 0), array('lt', 0), 'or');
				$condition['_string'] = "StaffCode in (select StaffCode from dbo.StaffSubscribe where SubcriberCode= '" . getInputValue('StaffCode') . "' and IsGetTScores=1 )";
				$rs = $dbm -> getScoreRecords($condition, getInputValue('Page',"1,1000"), $field);
				break;
			case 'PYScore' :
				$condition['YScore'] = array('gt', 0);
				$condition['_string'] = "StaffCode in (select StaffCode from dbo.StaffSubscribe where SubcriberCode= '" . getInputValue('StaffCode') . "' and IsGetYScores=1 )";
				$rs = $dbm -> getScoreRecords($condition, getInputValue('Page',"1,1000"), $field);
				break;
			case 'NYScore' :
				$condition['YScore'] = array('lt', 0);
				$condition['_string'] = "StaffCode in (select StaffCode from dbo.StaffSubscribe where SubcriberCode= '" . getInputValue('StaffCode') . "' and IsGetYScores=1 )";
				$rs = $dbm -> getScoreRecords($condition, getInputValue('Page',"1,1000"), $field);
				break;
			default :
				break;
		}
		
		return $this -> ajaxReturn($rs);

	}

}
?>