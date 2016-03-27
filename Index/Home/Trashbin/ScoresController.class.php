<?php
namespace Home\Controller;

class ScoresController extends \Think\Controller {
    //测试时用isset($_GET()),实际运行时用isset($_POST())
    //http://localhost/HBCMobileAdmin/index.php/Home/Scores/getStaffScores?staffcode=ricky&page=2
    //另一种http://localhost/HBCMobileAdmin/index.php/Home/Scores/getStaffScores/staffcode/ricky/page/1/startdate/2014-12-16
	public function getStaffScores() {
		$dbtmodel = D('ViewScoreRecords');
		
		$relstr = false;
		if (isset($_GET['staffcode'])) {
			$conditions['StaffCode'] = I('staffcode');
		}
		
		if (isset($_GET['deptcode'])) {
			$conditions['DeptCode'] = I('deptcode');
			$relstr = true;
		}
				
		if (isset($_GET['startdate'])) {
			$conditions['RecordTime'] = array('egt', I('startdate'));
		}
		
		if (isset($_GET['enddate'])) {
			$conditions['RecordTime'] = array('elt', I('enddate'));
		}

		if (isset($_GET['page'])) {
			$pagestr = I('page').',10';
			
		} else {
			$pagestr = '1,10';
		}
		
		$rs = $dbtmodel -> where($conditions) -> page($pagestr) -> select();
		
//		echo json_encode($_GET) . "<br/>" ;  
//		echo $dbtmodel->_Sql();

		dump($rs);
		//$this->ajaxReturn($rs);
	}
	
	
	public function getScoreStructure()
	{
		$Model = new \Think\Model();
		$sqlstr = "select StaffCode,a.EventScope,a.EventType,a.[Event],SUM(a.YScore) YScore,SUM(a.XScore) XScore,SUM(a.TValue) TValue,SUM(a.TScore) TScore";
		$sqlstr = $sqlstr . " from ScoreRecords as a left join [Events] as b on a.EventCode = b.EventCode where 1=1";
		if (isset($_GET["staffcode"]))
		{
			$sqlstr = $sqlstr . " and Staffcode='". I("staffcode") ."' ";
		}
		
		if (isset($_GET["startdate"]))
		{
			$sqlstr = $sqlstr . " and RecordTime>='". I("startdate") ."' ";
		}
		
		if (isset($_GET["enddate"]))
		{
			$sqlstr = $sqlstr . " and RecordTime<='". I("enddate") ."' ";
		}
		$sqlstr = $sqlstr . " group by StaffCode,a.EventScope,a.EventType,a.[Event],b.TypeOrder,b.EventOrder ";
		$sqlstr = $sqlstr . " order by a.EventScope,b.TypeOrder asc,b.EventOrder asc ";
//		echo $sqlstr;
//		die();
		$rs = $Model->query($sqlstr);
		dump($rs);
		//$this->ajaxReturn($rs);
	}
	//	public function saveScoreRecord() {
	//		$dbtmodel = D('Scores');
	//
	//		$data['staffcode'] = I('staffcode');
	//		$data['eventcode'] = I('eventcode');
	//		$data['_locked'] = 0;
	//		$data['recordcode'] = "";
	//		$data['recordtime'] = "";
	//		$data['revisetime'] = "";
	//
	//		$data['staffcode'] = "";
	//		$data['eventcode'] = I('eventcode');
	//		$data['event'] = "";
	//		$data['eventscope'] = "";
	//		$data['eventtype'] = "";
	//		$data['eventremark'] = I('remark');
	//
	//		$data['xscore'] = "";
	//		$data['yscore'] = "";
	//		$data['tscore'] = "";
	//		$data['tvalue'] = "";
	//
	//		$data['transactor'] = "";
	//		$data['auditresult'] = "";
	//		$data['auditor'] = "";
	//		$data['auditdate'] = "";
	//		$data['auditstate'] = "";
	//
	//		$data['appealdate'] = "";
	//		$data['appealdealer'] = "";
	//		$data['appealresult'] = "";
	//		$data['appealremark'] = "";
	//
	//		$data['recorder'] = "";
	//		$data['isexchange'] = "";
	//
	//		if (I('_identify') > 0) {
	//
	//			for att in $data
	//			  $data[att] = I(att);
	//			end F($name)
	//			$data['revisetime'] = "";
	//			$data['eventcode'] = I('eventcode');
	//			$data['event'] = "";
	//			$data['eventscope'] = "";
	//			$data['eventtype'] = "";
	//			$data['eventremark'] = I('remark');
	//
	//
	//			$dbtmodel -> data($data) -> save();
	//		} else {
	//			$dbtmodel -> data($data) -> add();
	//		}
	//	}
}
?>