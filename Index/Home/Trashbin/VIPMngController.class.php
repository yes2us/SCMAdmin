<?php
namespace Home\Controller;

class VIPMngController extends \Think\Controller {

/**
 * 获得当月的行动计划
 */
	public function getCurMonthPlan() {

		if(isset($_POST['StaffCode']))
		{
			$condition['MaintainerCode'] = $_POST['StaffCode'];
		}
		
		if(isset($_POST['DeptCode']))
		{
			$condition['BelongStoreCode'] = $_POST['DeptCode'];
		}

		$condition['_Locked'] != true;		
		$MaintainType = $_POST['MaintainType'];
		
		$pagestr = getInputValue('Page');
				
		switch ($MaintainType) {
			case 'AnzToInvite':
				$condition['_string'] = " PlanInviteDate is not null and convert(varchar(10),PlanInviteDate,23)<=convert(varchar(10),GetDate()+7,23)";
				break;
				
			case 'AnzToMaintain':
				$condition['_string'] = " NtContDate is not null and CONVERT(varchar(7),NtContDate,23)<=CONVERT(varchar(7),GETDATE()+7,23)";
				break;
				
			case 'AnzToBirthday':
				$today = "substring(CONVERT(varchar(10),GETDATE(),23),6,5)";
			    $threedayslater = "substring(CONVERT(varchar(10),GETDATE()+3,23),6,5)";
				
				$sqlstr = " ((NtBirthDate>=" . $today ." and NtBirthDate<=" . $threedayslater . ")";
				$sqlstr = $sqlstr . " or (NtMateBirthDate>=" . $today ." and NtBirthDate<=" . $threedayslater . ")";
				$sqlstr = $sqlstr . " or (NtChildBirthDate>=" . $today ." and NtChildBirthDate<=" . $threedayslater . ")";
				$sqlstr = $sqlstr . " or (NtWeddingAnniDate>=" . $today ." and NtWeddingAnniDate<=" . $threedayslater . "))";
				 $condition['_string'] = $sqlstr;
				break;
				
			case 'AnzToActivate':
				$condition['AnzToActivate'] = true;
				break;
			case 'AnzToWakeUp':
				$condition['AnzToWakeUp'] = true;
				break;
			default:
				break;
		}
		$fieldstr = "CustomerCode,CustomerName,MaintainerCode,Staffname,AnzBuyNearity,AnzContNearity,AnzMidTermBuyMoney,AnzMidTermBuyFreq, case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree";	
		$rs = D("VIPObject")
		->getVIPs($condition,$pagestr,$fieldstr);

		return $this -> ajaxReturn($rs);
	}

/**
 * 获得不同情感阶段的会员
 */
	public function getVIPInEmotionStage() {
			
		if(isset($_POST['StaffCode']))
		{
			$condition['MaintainerCode'] = $_POST['StaffCode'];
		}
		
		if(isset($_POST['DeptCode']))
		{
			$condition['BelongStoreCode'] = $_POST['DeptCode'];
		}
	
		$condition['AnzRelationLevel']	= $_POST['VIPEmotionStage'];
		$pagestr = getInputValue('Page');
					
		$fieldstr = "CustomerCode,CustomerName,AnzContNearity,AnzBuyHabitQuad,AnzRelationLevel,case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree";	
	    $rs = D("VIPObject")->getVIPs($condition,$pagestr,$fieldstr);
		
		return $this -> ajaxReturn($rs);
	}

/**
 * 获得不同象限的会员
 */
	public function getVIPQuadrant() {
		
		if(isset($_POST['StaffCode']))
		{
			$condition['MaintainerCode'] = $_POST['StaffCode'];
		}
		
		if(isset($_POST['DeptCode']))
		{
			$condition['BelongStoreCode'] = $_POST['DeptCode'];
		}

		$pagestr = getInputValue('Page');
		switch ($_POST['VIPQuadrant']) {
			case '亲密重要':
				$condition['AnzBuyHabitQuad'] = '第1象限';
				break;
			case '亲密一般':
				$condition['AnzBuyHabitQuad'] = '第2象限';
				break;
				case '友好重要':
				$condition['AnzBuyHabitQuad'] = '第3象限';
				break;
				case '友好一般':
				$condition['AnzBuyHabitQuad'] = '第4象限';
				break;
				
				case '休眠会员':
				$condition['AnzBuyHabitQuad'] = '第5象限';
				break;
				case '流失会员':
				$condition['AnzBuyHabitQuad'] = '第6象限';
				break;
			default:
					$condition['AnzBuyHabitQuad']	= $_POST['VIPQuadrant'];
				break;
		}		

		
					
		//$fieldstr = "CustomerCode,CustomerName,MaintainerCode,AnzBuyNearity,AnzContNearity,AnzMidTermBuyMoney,AnzMidTermBuyFreq,AnzRelationLevel,case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree";	
		$fieldstr = "CustomerCode,CustomerName,MaintainerCode,AnzBuyNearity,AnzContNearity,AnzMidTermBuyMoney,";
		$fieldstr = $fieldstr . " AnzMidTermBuyFreq,AnzRelationLevel,case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,";
		$fieldstr = $fieldstr . " case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree,";	
		
		//以下代码是为了迁就app代码中的错误
		$fieldstr = $fieldstr . " AnzBuyNearity BuyNearity,AnzContNearity ContNearity,AnzMidTermBuyMoney MidTermBuyMoney,AnzMidTermBuyFreq MidTermBuyFreq";	
		
	    $rs = D("VIPObject")->getVIPs($condition,$pagestr,$fieldstr);
		
		return $this -> ajaxReturn($rs);
	}

	
/**
 * 获得单个会员的详细信息：联系记录，消费特殊，个人衣柜等，情感分数（曲线）
 */
	public function getSingleVIPDetailInfo() {
		$customercode = getInputValue('CustomerCode');
		$rs = D("VIPObject")
		->getSingleVIPDetailInfo($customercode);

		return $this -> ajaxReturn($rs);
	}

/**
 * 保存单个会员的联系记录
 * step1.删除同一天已经有的联系记录和得分记录
 * step2.增加联系记录，修改下次联系计划
 * step3.修改下次联系计划
 * step4.增加得分记录
 */
	public function saveSingleVIPContRecord() {
		/**
		 * 0.找到已经增加的联系记录和Y分记录编号
		 */

		$Condition['CustomerCode'] = getInputValue('CustomerCode');
		$MaintainerCode = M('customers',"",getMyCon(2))->where($Condition)->getField("MaintainerCode");
				 
		$Condition['ContDate'] = date('Y-m-d');
		
		$dbm=M('contactrecords','',getMyCon(2));
		$identify = $dbm
		->where($Condition)
		->getField("_Identify");
		
		$crecordcode = $dbm
		->where($Condition)
		->getField("CRecordCode");
		
		//1.删除同一VIP的联系记录以及对应的得分记录
		if($identify && $crecordcode) 
		{
			$dbm
			->where('_Identify=' . $identify)
			->delete();
			
			M('scorerecords','',getMyCon())
			->where("EventRemark='" . $crecordcode . "'")
			->delete();
		}

		//2.增加联系记录	
		$ContactRecords['CustomerCode'] = getInputValue('CustomerCode');
		$ContactRecords['ContDate'] = date('Y-m-d');
		$ContactRecords['MaintainerCode'] = $MaintainerCode;
		$ContactRecords['ContWay'] = getInputValue('ContWay');
		$ContactRecords['ResponseLevel'] = getInputValue('ResponseLevel');
		$ContactRecords['InviteResult'] = getInputValue('InviteResult');
		$ContactRecords['ContContent'] = getInputValue('CurContContent');
		
		$dbm->add($ContactRecords);
		$crecordcode = $dbm->where($Condition)->getField("CRecordCode");

		/**
		 * 3.修改下次联系计划,如果维护计划存在,那到取消邀约计划
		 */	
		$Customers['AnzLastContContent'] = getInputValue('CurContContent');
		
		$Customers['NtContDate'] = getInputValue('NtContDate');
		$Customers['NtContContent'] = getInputValue('NtContContent');
		if(getInputValue('NtContDate'))
		{
				$Customers['PlanInviteDate'] = null;
				$Customers['PlanInviteContent'] = null;
		}
		$dbm = M('customers','',getMyCon(2));
		$dbm
		->where($Condition)
		->save($Customers);
		
		$emotionStageLevel = $dbm
		->where($Condition)
		->getField("AnzRelationLevel");
		
		/**
		 * 4.增加得分记录
		 */
		 $scorerecord['RecordTime'] = date('Y-m-d');
		 $scorerecord['ReviseTime'] = date('Y-m-d');
		 
		 $scorerecord['StaffCode'] = $MaintainerCode;
		 $scorerecord['EventCode'] = "SJ0001";
		 $scorerecord['Event'] = "维护与邀约";
		 $scorerecord['EventType'] = "会员管理";
		 $scorerecord['EventScope'] = "流程内";
		 $scorerecord['EventRemark'] = $crecordcode;
		 
		 $scorerecord['YScore'] = (getInputValue('ContWay')==='电话')? 1:0.5;
		 if($emotionStageLevel=='恋爱期') $scorerecord['YScore'] = 2*$scorerecord['YScore'];
		 
		 $auditorcode = M("staffs","",getMyCon())
		 ->join("inner join ViewStoreMngers on BelongDeptCode = DeptCode")
		 ->where("staffs.StaffCode='" . $MaintainerCode . "'")
		 ->getField("ViewStoreMngers.StaffCode");
		 
		 $scorerecord['Auditor'] = $auditorcode;
		 $scorerecord['AuditState'] = "待审核";
		 $scorerecord['Transactor'] = $MaintainerCode;
		 $scorerecord['Recorder'] = $MaintainerCode;
		 $scorerecord['GetFrom'] = $MaintainerCode;

		$_identify = M('scorerecords',"",getMyCon()) -> add($scorerecord);
		
		if ($_identify) {
			$recordcode = 'RC' . str_pad((string)$_identify, 7, "0", STR_PAD_LEFT);
			M('scorerecords',"",getMyCon()) -> where("_Identify=" . $_identify) -> setField('RecordCode', $recordcode);
			
			$state = "OK";
		} else {
			$state = '保存失败';
		}
		return $this -> ajaxReturn($state);
	}
	
	public function deleteSingleVIPContRecord()
	{
	       $CRecordCode = getInputValue("CRecordCode");	
		   $condition['EventRemark'] = $CRecordCode;
		   
		   $AuditState = M('scorerecords',"",getMyCon())
		   ->where($condition)
		   ->getField('AuditState');
		   
		   if($AuditState=='批准')
		   {
		   	 return $this -> ajaxReturn("不可删除已经审核的记录");
		   }
		   else
		   	{
		   		M('scorerecords',"",getMyCon())->where($condition)->delete();
				
				unset($condition);
				$condition['CRecordCode'] = $CRecordCode;
				
				M('contactrecords',"",getMyCon(2))
				->where($condition)
				->delete();
				
				return $this -> ajaxReturn("OK");
		   	}
		
	}
	
	public function getMyMaintainTemperature()
	{		
		$condition['MaintainerCode'] = getInputValue("MaintainerCode");
		$condition['_string'] = "AnzRelationLevel is not null and AnzBuyNearity<180";
		
		$result=M('customers','',getMyCon(2))
		->field("AnzRelationLevel,convert(decimal(5,1),avg(ISNULL(AnzEmotionDegree,0))) Temperature")
		->where($condition)
		->group('AnzRelationLevel')
		->select();
		
		$rs['impressPeriod'] = 0;
		$rs['afterPeriod'] = 0;
		$rs['inLovePeriod'] = 0;
		$rs['sixMonthPeriod'] = 0;
		
		if(count($result)>0)
		{
			foreach($result as $row)
			{
				if($row['印象期'])  $rs['impressPeriod'] = $row['temperature'];
				if($row['追求期'])  $rs['afterPeriod'] = $row['temperature'];
				if($row['恋爱期'])  $rs['inLovePeriod'] = $row['temperature'];
				
				$rs['sixMonthPeriod'] = $rs['sixMonthPeriod'] + $row['temperature'];
			}
		}
		$rs['sixMonthPeriod'] = $rs['sixMonthPeriod']/3;
		
		return $this -> ajaxReturn($rs);
	}
	
	/**
	 * 保存单个会员的邀约计划,取消维护计划
	 */
	public function saveSingleVIPInvitePlan() {
		$CustomerCode = getInputValue("CustomerCode");
		$PlanInviteData['PlanInviteDate'] = getInputValue('PlanInviteDate');
		$PlanInviteData['PlanInviteContent'] = getInputValue('PlanInviteContent');
		
		if(getInputValue('PlanInviteDate'))
		{
				$Customers['NtContDate'] = null;
				$Customers['NtContContent'] = null;
		}
		
		
		$dbm=M('customers','',getMyCon(2));
		$condition['CustomerCode'] = $CustomerCode;
		$dbm->where($condition)->save($PlanInviteData);
		
		return $this -> ajaxReturn("OK");
	}
		
}
?>