<?php
namespace Home\Controller;

class WBVIPMngController extends \Think\Controller {

	/**
 * 获得当月的行动计划
 */
	public function getVIPList() {
		if(isset($_POST['CustomerCode']))	$condition['CustomerCode'] = $_POST['CustomerCode'];				
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];				
		if(isset($_POST['BelongStoreCode']))	$condition['BelongStoreCode'] = $_POST['BelongStoreCode'];		
		if(isset($_POST['AnzBuyHabitQuad']))	$condition['AnzBuyHabitQuad'] = $_POST['AnzBuyHabitQuad'];	
		if(isset($_POST['IsReserved']))	$condition['IsReserved'] = $_POST['IsReserved'];	
		if(isset($_POST['VIPMngEnabled']))	$condition['VIPMngEnabled'] = true;	
		
		if(isset($_POST['Page']))
		{
			$pagestr = $_POST['Page'];
		} else  	
		{
			$pagestr = "1,10000";
		}
		
		$condition['_Locked'] != true;	
		
//		setTag('$pagestr', $pagestr);
		if(isset($_POST['FieldStr']))
		{
			$fieldstr= $_POST['FieldStr'];
		}
		else
		{
			$fieldstr = "customers._Identify,[CustomerCode],[CustomerName],[MobileNo],[Birthday],[BodyShape],[Interest],[DressStyle],[Careen],[Characters],[PicturePath],AnzStateLevel,AnzImportantLevel,";
			$fieldstr = $fieldstr . "[BizCircle],[PrefSize],[PrefContWay],[PrefContTime],[PrefBuySites],[PrefColor],[PrefStyle],[BuyReasons],[MateBirthday],[ChildBirthday],[WeddingAnniDate],[WeddingAnniRemark],";
			$fieldstr = $fieldstr . "[OtherPersonalities],PlanInviteDate,PlanInviteContent,[NtContDate],[AnzLastContDate],[AnzLastBuyDate],[AnzBuyNearity],[AnzMidTermBuyMoney],[AnzMidTermBuyFreq],[AnzTotalBuyMoney],[AnzTotalBuyFreq],";
			$fieldstr = $fieldstr . "[AnzBuyGapDays],[AnzMoneyPerSale],[AnzQtyPerSale],[AnzBuyPrice],[AnzBuyHabitQuad],BelongStoreCode, isnull(DeptSName,'(无归属店铺)') DeptSName,MaintainerCode,isnull(StaffName,'(无人维护)') MaintainerName,";
			$fieldstr = $fieldstr . "ROW_NUMBER() over (partition by AnzBuyHabitQuad order by AnzBuyNearity) QuadRankNo";
		}
//		setTag('$fieldstr', $fieldstr);
		$rs = D("VIPObject")->getVIPs($condition,$pagestr,$fieldstr);

		
		return $this -> ajaxReturn($rs);
	}
	
		public function getSingleVIPBasicDetail() {
		if(isset($_POST['CustomerCode']))	$condition['CustomerCode'] = $_POST['CustomerCode'];				
		if(isset($_GET['CustomerCode']))	$condition['CustomerCode'] = $_GET['CustomerCode'];				
		
			$fieldstr = "customers._Identify,[CustomerCode],IsReserved,[CustomerName],[MobileNo],[Birthday],[BodyShape],[Interest],[DressStyle],[Careen],[Characters],[PicturePath],AnzStateLevel,AnzImportantLevel,";
			$fieldstr = $fieldstr . "[BizCircle],[PrefSize],[PrefContWay],[PrefContTime],[PrefBuySites],[PrefColor],[PrefStyle],[BuyReasons],[MateBirthday],[ChildBirthday],[WeddingAnniDate],[WeddingAnniRemark],";
			$fieldstr = $fieldstr . "[OtherPersonalities],PlanInviteDate,PlanInviteContent,[NtContDate],[AnzLastContDate],[AnzLastBuyDate],[AnzBuyNearity],[AnzMidTermBuyMoney],[AnzMidTermBuyFreq],[AnzTotalBuyMoney],[AnzTotalBuyFreq],";
			$fieldstr = $fieldstr . "[AnzBuyGapDays],[AnzMoneyPerSale],[AnzQtyPerSale],[AnzBuyPrice],[AnzBuyHabitQuad],BelongStoreCode,isnull(DeptSName,'(无归属店铺)') DeptSName,MaintainerCode,isnull(StaffName,'(无人维护)') MaintainerName,";
			$fieldstr = $fieldstr . "AnzEmotionDegree,AnzContNearity,anzrelationlevel,anztotalscore,ntcontcontent,ROW_NUMBER() over (partition by AnzBuyHabitQuad order by AnzBuyNearity) QuadRankNo";
			
		$rs = D("VIPObject")->getVIPs($condition,'1,10',$fieldstr);

		
		return $this -> ajaxReturn($rs);
	}
	
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

		if(isset($_POST['Page']))
		{
			$pagestr = $_POST['Page'];
		}
		else
		{
			$pagestr = "1,5000";
		}
		$condition['_Locked'] != true;		
		$MaintainType = $_POST['MaintainType'];
		
		switch ($MaintainType) {
			case 'AnzToInvite':
				$condition['_string'] = " PlanInviteDate is not null and convert(varchar(10),PlanInviteDate,23)<=convert(varchar(10),GetDate()+7,23)";
				break;
			case 'AnzToMaintain':
				$condition['_string'] = "NtContDate is not null and CONVERT(varchar(7),NtContDate,23)<=CONVERT(varchar(7),GETDATE()+7,23)";
				break;
			case 'AnzToBirthday':
			    $today = "substring(CONVERT(varchar(10),GETDATE(),23),6,5)";
			    $threedayslater = "substring(CONVERT(varchar(10),GETDATE()+3,23),6,5)";
				
				$sqlstr = "((NtBirthDate>=" . $today ." and NtBirthDate<=" . $threedayslater . ")";
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
//		setTag('sql111', $condition['_string']);
					
		$fieldstr = "CustomerCode,CustomerName,MobileNo,MaintainerCode,Staffname,AnzBuyNearity,AnzContNearity,AnzMidTermBuyMoney,";	
		$fieldstr = $fieldstr. " AnzMidTermBuyFreq, case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,";	
		$fieldstr = $fieldstr. " case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree,";	
		$fieldstr = $fieldstr. " NtBirthDate,NtMateBirthDate,NtChildBirthDate,NtWeddingAnniDate,AnzImportantLevel,AnzStateLevel,AnzBuyHabitQuad";	
		$rs = D("VIPObject")->getVIPs($condition,$pagestr,$fieldstr);
		
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

		if(isset($_POST['Page']))
		{
			$pagestr = $_POST['Page'];
		}
		else
		{
			$pagestr = null;
		}		
		$condition['AnzRelationLevel']	= $_POST['VIPEmotionStage'];
		
					
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

		if(isset($_POST['Page']))
		{
			$pagestr = $_POST['Page'];
		}
		else
		{
			$pagestr = null;
		}		
		$condition['AnzBuyHabitQuad']	= $_POST['VIPQuadrant'];
		
					
		$fieldstr = "CustomerCode,CustomerName,MaintainerCode,AnzBuyNearity,AnzContNearity,AnzMidTermBuyMoney,AnzMidTermBuyFreq,AnzRelationLevel,case when  AnzTotalScore is null then  0 else AnzTotalScore end  AnzTotalScore,case when  AnzEmotionDegree is null then  0 else AnzEmotionDegree end  AnzEmotionDegree";	
	    $rs = D("VIPObject")->getVIPs($condition,$pagestr,$fieldstr);
		
		return $this -> ajaxReturn($rs);
	}

	
/**
 * 获得单个会员的详细信息：联系记录，消费特殊，个人衣柜等，情感分数（曲线）
 */
	public function getSingleVIPDetailInfo() {
		$customercode = $_POST['CustomerCode'];
//		$customercode = '188304';//$_POST['CustomerCode'];
		$rs = D("VIPObject")->getSingleVIPDetailInfo($customercode);
		//dump($rs);
		return $this -> ajaxReturn($rs);
	}

/**
 * 保存单个会员的联系记录
 */
	public function saveSingleVIPContRecord() {
		$Condition['CustomerCode'] = $_POST['CustomerCode'];
		$Condition['ContDate'] = date('Y-m-d');
				
		$ContactRecords['CustomerCode'] = $_POST['CustomerCode'];
		$ContactRecords['ContDate'] = date('Y-m-d');
		$ContactRecords['Maintainer'] = $_POST['User'];
		$ContactRecords['ContWay'] = $_POST['ContWay'];
		$ContactRecords['ResponseLevel'] = $_POST['ResponseLevel'];
		$ContactRecords['InviteResult'] = $_POST['InviteResult'];
		$ContactRecords['ContContent'] = $_POST['CurContContent'];

		$dbm=M('contactrecords',"",getMyCon(2));
		$identify = $dbm->where($Condition)->getField("_Identify");
		
		if($identify>0)
		{
			$dbm->where($Condition)->save($ContactRecords);
		}
		else
		{
			$dbm->add($ContactRecords);
		}


		$Customers['NtContDate'] = $_POST['NtContDate'];
		$Customers['NtContContent'] = $_POST['NtContContent'];
		M('customers',"",getMyCon(2))->where($Condition)->save($Customers);
		
		
		return $this -> ajaxReturn("OK");
	}
	
/**
 * 查询会员联系记录在案
 */
	public function getContRecord() {
		$customercode = I("CustomerCode");
		
//		$customercode = '204264';
		$dbm = M('contactrecords',"",getMyCon(2));
		$rs = $dbm
		->join("left join VW_Staffs on MaintainerCode = StaffCode")
		->field(" _Identify,CustomerCode,ContDate,MaintainerCode, StaffName as MaintainerName,ContWay,ResponseLevel,InviteResult,ContContent,ROW_NUMBER() over (order by ContDate desc) as ID")
		->where("CustomerCode='" . $customercode . "'")
		->order("contdate desc")->select();
		
		return $this -> ajaxReturn($rs);
	}
	
/**
 * 查询会员消费广度
 */
	public function getSaleSpan() {

		$customercode = I("CustomerCode");
		
//		$customercode = '204264';
		
		$dbm = M('Sales',"",getMyCon(2));
		$rs = $dbm
		->join("left join SKUs on Sales.SKU = SKUs.SKU")
		->join("ProductColors on SKUs.ProductColorCode = ProductColors.ProductColorCode")
		->field("WaveBand,Series,convert(decimal(10,2),SUM(SaleMoney)) SaleMoney,SUM(SaleQty) SaleQty")
		->where("CustomerCode='" . $customercode . "'")
		->group("WaveBand,Series")
		->order("WaveBand")
		->select();
		
		return $this -> ajaxReturn($rs);
	}	
	
/**
 * 查询会员消费记录
 */
	public function getSaleRecord() {
		$customercode = I("CustomerCode");
		
//		$customercode = '204264';
		
		$dbm = M('Sales',"",getMyCon(2));
		$rs = $dbm
		->join("left join SKUs on Sales.SKU = SKUs.SKU")
		->join("ProductColors on SKUs.ProductColorCode = ProductColors.ProductColorCode")
		->field("CONVERT(varchar(10),RecordTime,23) RecordTime,Sales.SKU,Year,WaveBand,Series,ProductCode,Color,Size,convert(decimal(10,0),SaleMoney) SaleMoney,	SaleQty,PicturePath,convert(decimal(5,2),(case when isnull(TicketPrice,0)*isnull(SaleQty,0)=0 then 0 else isnull(SaleMoney,0)/(TicketPrice*SaleQty) end)) Discount")
		->where("CustomerCode='" . $customercode . "'")
		->order("RecordTime desc")
		->select();
//		setTag('sql',$dbm->_sql());
		return $this -> ajaxReturn($rs);
	}	
	
	
/**
 * 获得门店各象限消费信息
 */
	public function getStoreQuadInfo() {
		$storecode = I("StoreCode");
		$yearmonth = I("YearMonth");
		
		$dmb = D("VIPObject");
		$rs = $dmb->getStoreQuadInfo($storecode,$yearmonth);
		
		return $this -> ajaxReturn($rs);	
	}

/**
 * 获得VIP的全局参数
 */
	public function getGlobalVIPParameters() {
		$dmb = D("VIPObject");
		$paraname=I("paraname");//'体型'; 
		
		$rsarray = $dmb->getGlobalVIPParameters($paraname);
		
		$rsarray = explode("|",$rsarray);
		$i = 0;
		foreach($rsarray as $r)
		{
			$rs[$i] = array("id"=>$r,"value"=>$r);
			$i = $i + 1;
		}
//		dump($rs);
		
		return $this -> ajaxReturn($rs);	
	}
	
/**
 * 获得VIP的温度曲线
 */
	public function getVIPTEMPTrend() {

		$CustomerCode = '344302';//getInputValue("CustomerCode");		
//		$CustomerCode = getInputValue("CustomerCode");		
		$ShowDays = getInputValue("ShowDays",180);
		
		$rs = D("VIPObject")->getVIPTEMPTrend($CustomerCode,$ShowDays);
				
		return $this -> ajaxReturn($rs);	
	}	
	
		
}
?>