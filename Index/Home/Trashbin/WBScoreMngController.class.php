<?php
namespace Home\Controller;

class WBScoreMngController extends \Think\Controller {

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

		$fieldstr = "recordcode, convert(varchar(10),recordtime,23) recordtime,staffcode,staffname,deptcode,deptname,deptsname,incenttype,[event],eventcode,eventtype,eventscope,eventremark,yscore,xscore,tvalue,tscore,tsaleqty,auditorcode,auditorname";
		$rs = D('ScoreObject') -> getScoreRecords($condition, $_POST['page'], $fieldstr);
		//	echo D('ScoreObject') -> _sql();
		//		dump($rs);
		return $this -> ajaxReturn($rs);
	}

		public function queryScoreRecord(){
			$fieldstr = "_identify,recordcode, recordtype, convert(varchar(10),recordtime,23) recordtime,staffcode,staffname,";
			$fieldstr = $fieldstr . "deptcode,deptname,deptsname,incenttype,[event],eventcode,eventtype,eventscope,eventremark,";
			$fieldstr = $fieldstr . "yscore,xscore,tvalue,tscore,atvalue,vtvalue,tsaleqty,auditstate,auditdate,auditorname,";
			$fieldstr = $fieldstr . "(case when auditstate='批准' then 1 else case when auditstate='拒绝' then 0 else 2 end end) auditvalue";
			if(isset($_POST['Condition']))
			{
				$condition = $_POST['Condition'];
				$rs = D('ScoreObject') -> getScoreRecords($condition, "1,5000", $fieldstr);
			}
			else
			{
				$rs = D('ScoreObject') -> getScoreRecords(null, "1,5000", $fieldstr);	
			}
			return $this -> ajaxReturn($rs);
		}
		
		public function queryPeriodScoreSum(){
	
		$sql = " select a.DeptCode,a.DeptSName,a.StaffCode,a.StaffName,a.yscore,a.xscore,a.tvalue,a.tscore,a.atvalue,a.vtvalue,a.tsaleqty, ";
		
		$RankArea = getInputValue('RankArea','indept');
		
		if($RankArea=='indept')
		{
			$sql = $sql. " ROW_NUMBER() over(partition by DeptCode ORDER BY a.yscore desc) 'YOrderNo',ROW_NUMBER() over(partition by DeptCode ORDER BY a.tvalue desc) 'TOrderNo',COUNT(a.staffcode) over(partition by a.DeptCode) 'MemCount'";
		}
		else
		{
			$sql = $sql. " ROW_NUMBER() over( ORDER BY a.yscore desc) 'YOrderNo',ROW_NUMBER() over( ORDER BY a.tvalue desc) 'TOrderNo',COUNT(a.staffcode) over(partition by a.DeptCode) 'MemCount'";
		}
		
		$sql = $sql. " from  ";
		$sql = $sql. " ( ";
		$sql = $sql. " 	select staffcode,staffname,deptcode,deptsname,sum(yscore) yscore,sum(xscore) xscore,sum(tvalue) tvalue, sum(tscore) tscore, ";
		$sql = $sql. " 	sum(atvalue) atvalue,sum(vtvalue) vtvalue,avg(tsaleqty) tsaleqty ";
		$sql = $sql. " 	from viewscorerecords ";
		$sql = $sql. " 	where AuditState='批准' and " . $_POST['Condition'];
		$sql = $sql. " 	group by staffcode,staffname,deptcode,deptsname ";
		$sql = $sql. " ) as a ";
	
//		p($sql);
	
		$Model = new \Think\Model("",getMyCon());
		$rs = $Model -> query($sql);
		
		for($i = 0; $i < count($rs); $i++)
		{
			$rs[$i]['problem'] = '';
			if($rs[$i]['yorderno']-$rs[$i]['torderno']>$rs[$i]['memcount']/3)
			{
				$rs[$i]['problem'] = 'Y分低业绩高';
			}
			
			if($rs[$i]['yorderno']-$rs[$i]['torderno']<-$rs[$i]['memcount']/3)
			{
				$rs[$i]['problem'] = 'Y分高业绩低';
			}
			
			if($rs[$i]['tvalue']<1)
			{
				$rs[$i]['problem'] = '无业绩';
			}
		}
			
		 return $this -> ajaxReturn($rs);
		}
		
		
	public function getVariousRanks() {
		$RankAtt = $_POST['RankAtt'];
				
		$sqlstring = null;
		$sqlstring = M('sqllist') -> where("SQLIndex='WBSQL_Ranker_" . $RankAtt . "'") -> getField('SQLCode');
		

		if(strripos($RankAtt,'GARatio') != false || $RankAtt=='IncentTask')
		{
			$sqlstring = str_replace('@parm1', I("YearMonth"), $sqlstring);
//					setTag('$sqlstring0', $sqlstring);
					
			$sqlstring = str_replace('@parm2', I("PageIndex"), $sqlstring);
			$sqlstring = str_replace('@parm3', I("PageLen"), $sqlstring);
		}
		else
		{
			$sqlstring = str_replace('@parm1', I("StartDate"), $sqlstring);
			$sqlstring = str_replace('@parm2', I("EndDate"), $sqlstring);			
			$sqlstring = str_replace('@parm3', I("PageIndex"), $sqlstring);
			$sqlstring = str_replace('@parm4', I("PageLen"), $sqlstring);
		}

//p($sqlstring);
//		setTag('$sqlstring', $sqlstring);
		
		$Model = new \Think\Model("",getMyCon());
		$rs = $Model -> query($sqlstring);

		return $this -> ajaxReturn($rs);
	}
	
	public function getScoreCrossStructure(){
//		    $condition["ColAsRow"] = 'EventType';
//			$condition["StartDate"] = "2015-01-01";
//			$condition["EndDate"] = null;
//			$condition["DeptCode"] = 'StoreXX';
//			$condition["CompareType"] = FALSE;
			$condition = $_POST;
		    $rs = D('ScoreObject') ->getScoreCrossStructure($condition);
			return $this -> ajaxReturn($rs);
	}
	
	public function getScoreCrossStructureColArray(){
	
			$sqlstr = "select EventScopeCode as colcode, EventScope as colname from EventScopes where EventScopeEnabled=1 and EventScope in (select distinct EventScope from ScoreRecords where RecordTime>'2015-08-01')";
			$sqlstr = $sqlstr . " union select EventTypeCode as colcode, EventType as colname from EventTypes where EventTypeEnabled=1 and EventType in (select distinct EventType from ScoreRecords where RecordTime>'2015-08-01')";
			$sqlstr = $sqlstr . " union select EventCode as colcode, [Event] as colname from [Events] where EventEnabled=1 and [Event] in (select distinct [Event] from ScoreRecords where RecordTime>'2015-08-01')";
			
			$Model = new \Think\Model("",getMyCon());
			$rs = $Model -> query($sqlstr);
		
			return $this -> ajaxReturn($rs);
	}
}
?>