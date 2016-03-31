<?php
namespace Home\Controller;

class HonorController extends \Think\Controller {

	public function getPosHotSpots() {
		$dbm = M('sysparameters',"",getMyCon());
		$globalthresh_posyscore = $dbm -> where("name='globalthresh_posyscore'") -> getField('vinteger');
		$globalthresh_postscore = $dbm -> where("name='globalthresh_postscore'") -> getField('vinteger');
		$globalthresh_qtypersale = $dbm -> where("name='globalthresh_qtypersale'") -> getField('vinteger');

		$where['YScore'] = array('egt', $globalthresh_posyscore);
		$where['TScore'] = array('egt', $globalthresh_postscore);
		$where['TSaleQty'] = array('egt', $globalthresh_qtypersale);
		$where['_logic'] = 'or';

		$condition['_complex'] = $where;
		$condition['AuditState'] = array('neq', '待审核');
		$condition['EventScope'] = array('neq', '固定得分');

		$fieldstr = "recordcode, convert(varchar(10),Getdate(),23) recordtime,staffcode,staffname,deptcode,deptname,deptsname,incenttype,[event],eventcode,eventtype,eventscope,eventremark,yscore,xscore,tvalue,tscore,tsaleqty";
		$rs = D('ScoreObject') -> getScoreRecords($condition, $_POST['page'], $fieldstr);
		//	echo D('ScoreObject') -> _sql();
		//dump($rs);
		return $this -> ajaxReturn($rs);
	}


	public function getVariousRanks() {
		$YearMonth = $_POST['YearMonth'];
		$PageIndex = $_POST['PageIndex'];
		$PageLen = $_POST['PageLen'];
		$RankAtt = $_POST['RankAtt'];

		$sqlstring = M('sqllist',"",getMyCon()) -> where("SQLIndex='SQL_Ranker_" . $RankAtt . "'") -> getField('SQLCode');
		$sqlstring = str_replace('@parm1', $YearMonth, $sqlstring);
		$sqlstring = str_replace('@parm2', $PageIndex, $sqlstring);
		$sqlstring = str_replace('@parm3', $PageLen, $sqlstring);

		$Model = new \Think\Model("","",getMyCon());
		$rs = $Model -> query($sqlstring);

		return $this -> ajaxReturn($rs);
	}

}
?>