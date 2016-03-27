<?php
namespace Home\Model;
use Think\Model;

class ScoreObjectModel extends Model {
	/*
	 * 一般查询
	 * */
	public function getScoreRecords($condition,$pagestr,$fieldstr=null) {
//		setTag('$condition',json_encode($condition));
//      setTag('$pagestr',$pagestr);
//      setTag('$fieldstr',$fieldstr);

	
		$rs = M("viewscorerecords","",getMyCon()) 
		->field($fieldstr)
		-> where($condition) 
		-> page($pagestr) 
		-> order('RecordTime desc')
		-> select();
		
//		setTag('_sql',M("viewscorerecords","",getMyCon()) -> _sql());
		//dump($rs);
		return $rs;
	}

	/*
	 * 查询某员工的得分记录
	 * */
	public function getStaffScoreRecords($staffcode,$startdate, $enddate, $pagestr = '1,10',$fieldstr=null) {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}
		$wherestr = " staffcode='" . $staffcode . "' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'";
		$rs = M("viewscorerecords","",getMyCon()) 
		->field($fieldstr)
		-> where($wherestr) 
		-> page($pagestr) 
		-> order('RecordTime desc')
		-> select();
		
//		echo $this -> _sql();
//		setTag('_sql',$this -> _sql());
		//dump($rs);
		return $rs;
	}

		/*
	 * 查询员工的T分和Y分在部门内的排名
	 * */
	public function getMyTYDeptRank($staffcode,$deptcode,$startdate, $enddate) {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}
		
		$Model = new \Think\Model("",getMyCon());

		$sqlstr =    "	select StaffCode,YScore,todayYScore,YRanker,TScore,todayTScore,XRanker from ( "; 
		$sqlstr =  $sqlstr . " SELECT StaffCode, SUM(YScore) YScore, ROW_NUMBER () over ( order by SUM(YScore) desc) YRanker,"; 
		$sqlstr =  $sqlstr . " SUM(case when CONVERT(varchar(10),RecordTime,23)=CONVERT(varchar(10),getdate(),23)  then YScore else 0 end) todayYScore,"; 
		$sqlstr =  $sqlstr  . "  SUM(TScore) TScore, ROW_NUMBER () over ( order by SUM(TScore) desc) XRanker, "; 
		$sqlstr =  $sqlstr  . "  SUM(case when CONVERT(varchar(10),RecordTime,23)=CONVERT(varchar(10),getdate(),23)  then TScore else 0 end) todayTScore "; 
		$sqlstr =  $sqlstr  . "  FROM [ViewScoreRecords] "; 
		$sqlstr =  $sqlstr  . "  WHERE AuditState='批准' and DeptCode = '". $deptcode ."' AND [RecordTime] >= '". $startdate ."'  AND [RecordTime] <= '". $enddate ."'";
		$sqlstr =  $sqlstr  . " group by StaffCode "; 
		$sqlstr =  $sqlstr  . " ) as a where a.StaffCode = '". $staffcode ."' "; 

        $rs = $Model->query($sqlstr);
//		echo $this -> _sql();
//		setTag('_sql',$this -> _sql());
		//dump($rs);
		return $rs;
	}

	/*
	 * 查询某部门员工的得分记录
	 * */
	public function getDeptMemScoreRecords($deptcode,$startdate, $enddate, $pagestr = '1,10',$fieldstr=null) {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}
				
		$wherestr = " IsOnJob=1 and IsLocked=0 and deptcode='" . $deptcode . "' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'";
		$rs = M("viewscorerecords","",getMyCon()) 
		->field($fieldstr)
		-> where($wherestr) 
		-> page($pagestr) 
		-> order('RecordTime desc')
		-> select();
		
//		echo $this -> _sql();
//		setTag('_sql',$this -> _sql());
		//dump($rs);
		return $rs;
	}


	/*
	 * 查询整体T分排名
	 * */
	public function getGlobalMemTScoreRank($startdate, $enddate, $pagestr = '1,10') {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}
		$fieldArray=array('DeptCode','DeptName','StaffName',
		'SUM(TScore)'=>'TScore',
		'SUM(case when RecordTime=CONVERT(varchar(10),getDate(),23) then TScore else 0 end)'=>'TodayTScore',
		'SUM(TScore)/(select SingleTScoreTarget from DeptGoals where YearMonth=CONVERT(varchar(7),getDate(),23) and DeptGoals.DeptCode = ViewScoreRecords.DeptCode)'=>'GARatio',
		'ROW_NUMBER() over(order by SUM(TScore)/(select SingleTScoreTarget from DeptGoals where YearMonth=CONVERT(varchar(7),getDate(),23) and DeptGoals.DeptCode = ViewScoreRecords.DeptCode) desc)'=>'TScoreRanker'
		);
		
		$rs = M("viewscorerecords","",getMyCon()) 
		->field($fieldArray)
		-> where(" AuditState='批准' and IsOnJob=1 and IsLocked=0 and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'") 
		-> group('DeptCode,DeptName,StaffName') 
		-> page($pagestr) 
		-> select();
		
		return $rs;
	}

	/*
	 * 查询整体Y分排名
	 * */
	public function getGlobalMemYScoreRank($startdate, $enddate, $pagestr = '1,10') {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}
		$fieldArray=array('DeptCode','DeptName','StaffName','SUM(YScore)'=>'YScore',
		'SUM(case when RecordTime=CONVERT(varchar(10),getDate(),23) then YScore else 0 end)'=>'TodayYScore',
		'ROW_NUMBER() over(order by SUM(YScore) desc)'=>'YScoreRanker',
		'SUM(isnull(YScore,0)+ISNULL(T2YConvertRate,1.0)*ISNULL(TScore,0))'=>'TotalYScore'
		);
		
		$rs = M("viewscorerecords","",getMyCon()) 
//		-> field('DeptName,StaffName,SUM(YScore) YScore, SUM(TScore) TScore,SUM(isnull(YScore,0)+ISNULL(T2YConvertRate,1.0)*ISNULL(TScore,0)) TotalYScore') 
		->field($fieldArray)
		-> where("AuditState='批准'  and IsOnJob=1 and IsLocked=0 and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'") 
		-> group('DeptCode,DeptName,StaffName') 
		-> page($pagestr) 
		-> select();
//		setTag('sql', $this->_sql());
		return $rs;
	}


	/*
	 * 查询部门内成员的Y分排名
	 * */
	public function getDeptMemYScoreRank($deptcode, $startdate, $enddate, $pagestr = '1,10') {
		if ($startdate == NULL) {
			$startdate = '2014-01-01';
		}
				
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}

		$rs = M("viewscorerecords","",getMyCon())
		-> field('StaffName,SUM(YScore) YScore, SUM(TScore) TScore,SUM(case when RecordTime=CONVERT(varchar(10),getDate(),23) then YScore else 0 end) TodayYScore,SUM(isnull(YScore,0)+ISNULL(T2YConvertRate,1.0)*ISNULL(TScore,0)) TotalYScore,ROW_NUMBER() over(order by SUM(YScore) desc) YScoreRanker') 
		-> where("AuditState='批准'  and IsOnJob=1 and IsLocked=0 and  ViewScoreRecords.deptcode='" . $deptcode . "' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'") 
		-> group('StaffName') 
		-> page($pagestr) 
		-> select();
		
//		setTag('_sql',$this -> _sql());
		
		return $rs;
	}


	/*
	 * 查询部门内成员的得分排名
	 * */
	public function getDeptMemTScoreRank($deptcode, $startdate, $enddate, $pagestr = '1,10') {
		if ($startdate == NULL) {
			$startdate = '2014-01-01';
		}
				
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}

		$fieldArray=array('DeptCode','StaffName',
		'SUM(TScore)'=>'TScore',
		'SUM(TValue)'=>'TValue',
		'SUM(case when RecordTime=CONVERT(varchar(10),getDate(),23) then TScore else 0 end)'=>'TodayTScore',
		'SUM(TScore)/(select SingleTScoreTarget from DeptGoals where YearMonth=CONVERT(varchar(7),getDate(),23) and DeptGoals.DeptCode = ViewScoreRecords.DeptCode)'=>'GARatio',
		'ROW_NUMBER() over(order by SUM(TScore)/(select SingleTScoreTarget from DeptGoals where YearMonth=CONVERT(varchar(7),getDate(),23) and DeptGoals.DeptCode = ViewScoreRecords.DeptCode) desc)'=>'TScoreRanker'
		);
		
		$rs = M("viewscorerecords","",getMyCon()) 
		->field($fieldArray)
//		-> field('StaffName,SUM(TScore) TScore, SUM(TValue) TValue,SUM(case when convert(varchar(10),RecordTime,23)=convert(varchar(10),GETDATE(),23) then TScore else 0 end) TodayTScore') 
		-> where("AuditState='批准'  and IsOnJob=1 and IsLocked=0 and  deptcode='" . $deptcode . "' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'") 
		-> group('DeptCode,StaffName') 
		-> page($pagestr) 
		-> select();
		
//		echo $this -> _sql();
//		setTag('_sql',$this -> _sql());
		//dump($rs);
		
		return $rs;
	}
		/*
	 * 查询整体得分排名
	 * */
	public function getDeptAveScoreRank($startdate, $enddate, $pagestr = '1,10') {
		if ($enddate == NULL) {
			$enddate = date('Y-m-d');
		}

		$rs = M("viewscorerecords","",getMyCon()) 
		-> join('Depts on ViewScoreRecords.DeptCode = Depts.DeptCode') 
		-> field('Depts.DeptCode,DeptName,convert(decimal(18,1),avg(isnull(YScore,0))) YScore,convert(decimal(18,1),avg(isnull(TScore,0))) TScore,convert(decimal(18,1),avg(isnull(YScore,0)+ISNULL(T2YConvertRate,1.0)*ISNULL(TScore,0))) TotalYScore') 
		-> where("AuditState='批准' and IsOnJob=1 and IsLocked=0 and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'") 
		-> group('Depts.DeptCode,DeptName') 
//		-> page($pagestr) 
//		-> order('SUM(isnull(YScore,0)+ISNULL(T2YConvertRate,1.0)*ISNULL(TScore,0)) desc')
		-> select();
		
//		echo $this -> _sql();
//		setTag('_sql',$this -> _sql());
		//dump($rs);
		
		return $rs;
	}
	
	/*
	 * 得到个人得分的结构
	 * */
	public function getStaffScoreStructure($Level,$staffcode,$startdate,$enddate=null)
	{
		if ($startdate==null)	$startdate = '2014-01-01';
		if ($enddate==null)		$enddate = date('Y-m-d');
		
		$Model = new \Think\Model("",getMyCon());
		switch ($Level) {
			case '1':
						$sqlstr = "select a.StaffCode,a.EventScope,b.EventScopeOrder,SUM(a.YScore) YScore,SUM(a.XScore) XScore,SUM(a.TValue) TValue,SUM(a.TScore) TScore";						
						$groupstr = " group by StaffCode,a.EventScope,b.EventScopeOrder";
						$orderstr = " order by b.EventScopeOrder";
				break;
			case '2':
						$sqlstr = "select a.StaffCode,a.EventScope,a.EventType,b.EventScopeOrder,b.EventTypeOrder,SUM(a.YScore) YScore,SUM(a.XScore) XScore,SUM(a.TValue) TValue,SUM(a.TScore) TScore";
						$groupstr = " group by a.StaffCode,a.EventScope,a.EventType,b.EventScopeOrder,b.EventTypeOrder";
						$orderstr = " order by b.EventScopeOrder,b.EventTypeOrder";
				break;
			default:
						$sqlstr = "select a.StaffCode,a.EventScope,a.EventType,a.[Event],b.EventScopeOrder,b.EventTypeOrder,b.EventOrder,SUM(a.YScore) YScore,SUM(a.XScore) XScore,SUM(a.TValue) TValue,SUM(a.TScore) TScore";
						$groupstr = " group by a.StaffCode,a.EventScope,a.EventType,a.[Event],b.EventScopeOrder,b.EventTypeOrder,b.EventOrder";
						$orderstr = " order by b.EventScopeOrder,b.EventTypeOrder,b.EventOrder";
				break;
		}

		$sqlstr = $sqlstr . " from ScoreRecords as a left join [VW_Events] as b on a.EventCode = b.EventCode ";
		$sqlstr = $sqlstr . " where a.AuditState='批准' and a.Staffcode='". $staffcode."' and RecordTime>='". $startdate ."' and RecordTime<='". $enddate ."'  and a.[Event]<>'导购T分T值'";
		$sqlstr = $sqlstr . $groupstr;
		$sqlstr = $sqlstr . $orderstr;
		
//		echo $sqlstr;
		$rs = $Model->query($sqlstr);
		
		//echo $this -> _sql();
//		setTag('_sql',$sqlstr);
		//dump($rs);
		return $rs;
	}


	/*
	 * 得到单个或多个部门的Y分结构
	 * */
	public function getDeptListScoreStructure($Level,$condition,$startdate,$enddate=null)
	{
		if ($startdate==null)	    $startdate = '2014-01-01';
		if ($enddate==null)		$enddate = date('Y-m-d');
		
		$Model = new \Think\Model("",getMyCon());
		
		switch ($Level) {
			case '1':
			   $types = 'EventScope';
				break;
			case '2':
			     $types = 'EventScope,EventType';
				break;
			default:
				$types = 'EventScope,EventType,[Event]';
				break;
		}
		
		if (isset($condition['DeptCode'])) 
		{
			$sqlstr = "select " . $types . ",convert(decimal(10,1),SUM(YScore)/(select sum(StaffNum) from DeptGoals where  DeptCode='". $condition['DeptCode'] ."' and YearMonth=CONVERT(varchar(7),getdate(),23))) YScore";		
		} else {
			$sqlstr = "select " . $types . ",convert(decimal(10,1),SUM(YScore)/(select sum(StaffNum) from DeptGoals where  YearMonth=CONVERT(varchar(7),getdate(),23))) YScore";			
		}	
		
		$sqlstr = $sqlstr . " from ViewScoreRecords";		
		if (isset($condition['DeptCode'])) {
			$sqlstr = $sqlstr . " where AuditState='批准' and DeptCode='" . $condition['DeptCode'] . "' and RecordTime>='". $startdate ."' and RecordTime<='" . $enddate . "'  and [Event]<>'导购T分T值'";	
		} else {
			$sqlstr = $sqlstr . " where AuditState='批准'  and " . $condition['_string'] . " and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "'  and [Event]<>'导购T分T值'";	
		}		
		$sqlstr = $sqlstr . " group by " . $types; 
		$sqlstr = $sqlstr . " order by ". $types; //. ",TypeOrder,EventOrder";
		
//		setTag('_sql',$sqlstr);
		
//		echo $sqlstr;
		$rs = $Model->query($sqlstr);
		
		//echo $this -> _sql();
		
		//dump($rs);
		return $rs;
	}

	/*
	 * 得到单个或多个部门的Y分交叉结构
	 * 以下五个参数必须全部设置
	 		$condition["ColAsRow"] = 'EventType';
			$condition["StartDate"] = "2015-01-01";
			$condition["EndDate"] = 'all';
			$condition["DeptCode"] = null;
			$condition["CompareDept"] = false;
	 *	 * */
	public function getScoreCrossStructure($condition)
	{
		$colasrow = strtolower($condition["ColAsRow"]);
		$startdate = $condition["StartDate"];
		if (!$startdate)	    $startdate = '2014-01-01';
				
		$enddate = $condition["EndDate"];
		if (!$enddate)	    $enddate = date('Y-m-d');

		if(isset($condition['DeptCode'])) 	$deptcode = $condition["DeptCode"]; else $deptcode = null;
		
//  生成如下语句		
//		select DeptSName,StaffName,
//		SUM(case when EventType='现场管理' then YScore else null end) as '现场管理',
//		SUM(case when EventType='会员管理' then YScore else null end) as '会员管理',
//		SUM(case when EventType='负能量' then YScore else null end) as '负能量',
//		SUM(case when EventType='正能量' then YScore else null end) as '正能量'
//		from ViewScoreRecords 
//		where RecordTime>'2015-01-01'
//		group by DeptSName,StaffName

		$rs['cols'] = M($colasrow . "s","",getMyCon())
		->field($colasrow . "code as colcode," . $colasrow . " as colname")
		->where($colasrow . "Enabled=1 and " . $colasrow . " in (select distinct " . $colasrow . " from ScoreRecords where RecordTime>=' " . $startdate . "')")
		->select();
//		dump($colArray);
				
		$sqlstr = "select DeptSName,StaffName,ScoreMngEnabled";
		$sqlstr0 = "select DeptSName ";
		foreach ($rs['cols'] as $col) {
			//查到$colasrow对应的编码,因thinkphp不能处理中文，必须用code表示
			$sqlstr = $sqlstr . ", convert(decimal(10,0),SUM(case when ". $colasrow ."='". $col['colname'] ."' then YScore else 0 end)) as '" . $col['colcode'] . "'";
			$sqlstr0 = $sqlstr0 . ", convert(decimal(10,0),avg(" . $col['colcode'] . "))  as " . $col['colcode'];
		}
		$sqlstr = $sqlstr . " from ViewScoreRecords ";			
		$sqlstr = $sqlstr . " where AuditState='批准' and [Event] not like '%T值%' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "' ";	
		if($deptcode) $sqlstr = $sqlstr . " and  DeptCode = '". $condition["DeptCode"] ."'";		
		$sqlstr = $sqlstr . " group by  DeptSName,StaffName,ScoreMngEnabled";
		
				
		if($condition['CompareType']=="cmpdept")
		{
			$sqlstr = $sqlstr0 . " from (" .$sqlstr . ") as t where ScoreMngEnabled=1 group by DeptSName";
		}
		
//		echo $sqlstr;
//		setTag('_sql',$sqlstr);	
		$Model = new \Think\Model("",getMyCon());
		$rs['data'] = $Model->query($sqlstr);
		
//		dump($rs);
		return $rs['data'];
	}

	/*
	 * 得到个人和部门的YT分30天的趋势
	 * */
	public function getScoreTrend($staffcode,$deptcode,$startdate,$enddate)
	{
		$Model = new \Think\Model("",getMyCon());

		$sqlstr = "select a.[date], ";						
		$sqlstr = $sqlstr . " SUM(case when ObjectType='Person' then YScore else 0 end) PersonYScore ,";
		$sqlstr = $sqlstr . " SUM(case when ObjectType='Person' then TScore else 0 end) PersonTScore ,";

		$sqlstr = $sqlstr . " SUM(case when ObjectType='Dept' then YScore else 0 end) DeptYScore ,";
		$sqlstr = $sqlstr . " SUM(case when ObjectType='Dept' then TScore else 0 end) DeptTScore ";
		
		$sqlstr = $sqlstr . " from DateInfo a left join (";
		$sqlstr = $sqlstr . "	 select 'Person' ObjectType,RecordTime,SUM(isnull(YScore,0)) YScore,SUM(isnull(TScore,0)) TScore";
		$sqlstr = $sqlstr . "	 from ScoreRecords";
		$sqlstr = $sqlstr . "	 where AuditState='批准' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "' and StaffCode='" . $staffcode . "'";
		$sqlstr = $sqlstr . "	 group by RecordTime	";
		
		$sqlstr = $sqlstr . "	 union";
		
		$sqlstr = $sqlstr . "	 select 'Dept' ObjectType,RecordTime,SUM(isnull(YScore,0)) YScore,SUM(isnull(TScore,0)) TScore";
		$sqlstr = $sqlstr . "	 from ScoreRecords left join Staffs on ScoreRecords.StaffCode = Staffs.StaffCode";
		$sqlstr = $sqlstr . "	 where AuditState='批准' and RecordTime>='" . $startdate . "' and RecordTime<='" . $enddate . "' and BelongDeptCode='" . $deptcode . "'";
		$sqlstr = $sqlstr . "	 group by RecordTime	) as b";
		$sqlstr = $sqlstr . " on a.[date]=b.RecordTime";
		$sqlstr = $sqlstr . " where a.[date]>='" . $startdate . "' and a.[date]<='" . $enddate . "'";
		$sqlstr = $sqlstr . " group by a.[date]";
		
//		echo $sqlstr;
		$rs = $Model->query($sqlstr);
		
		//echo $this -> _sql();
//		setTag('_sql',$sqlstr);
//		dump($rs);
		return $rs;
	}


	/*
	 * 得到个人和部门的YT分30天的趋势
	 * */
	public function getDeptListScoreTrend($staffcode,$deptcode,$startdate,$enddate)
	{
		
		$sqlstr = M('sqllist')->where("SQLIndex='SQL_DeptListScoreRank'")->getField('SQLCode');
		$sqlstr = str_replace('@parm1', $staffcode, $sqlstr);
		$sqlstr = str_replace('@parm2', $deptcode, $sqlstr);
		$sqlstr = str_replace('@parm3', $startdate, $sqlstr);
		$sqlstr = str_replace('@parm4', $enddate, $sqlstr);

		$Model = new \Think\Model("",getMyCon());
		$rs = $Model->query($sqlstr);
		
		//echo $this -> _sql();
//		dump($rs);
		return $rs;
	}
}
?>
