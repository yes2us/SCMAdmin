<?php
namespace Home\Model;
use Think\Model;

/**
 * 这是会员管理的对象
 */
class VIPObjectModel extends Model {
     protected $trueTableName = 'Customers';
	  
	// 获得会员计划
	public function getVIPs($condition,$pagestr,$fieldstr) {
		
		$dbt = M('customers','',getMyCon(2));

		
		$rs = $dbt
		->join('left join VW_Staffs on MaintainerCode=StaffCode')
		->join('left join VW_Depts on BelongStoreCode=DeptCode')
		->field($fieldstr)
		->where($condition)
		->page($pagestr)
		->select();
//		setTag('sql123', $dbt->_sql());
		return $rs;
	}


	// 分析会员的消费状态
	public function getSingleVIPDetailInfo($CustomerCode) {
		$dbt = M('customers','',getMyCon(2));
		
		$condition['CustomerCode'] = $CustomerCode;		
		$fieldstr = "[CustomerCode],[CustomerName],[MobileNo]  MobibleNo ,[MobileNo],[Birthday],[BodyShape],[Interest],[DressStyle],[Careen],[Characters],[PicturePath],";
		$fieldstr = $fieldstr . "[BizCircle],[PrefSize],[PrefContWay],[PrefContTime],[PrefBuySites],[PrefColor],[PrefStyle],[BuyReasons],[MateBirthday],[ChildBirthday],[WeddingAnniDate],[WeddingAnniRemark],";
		//$fieldstr = $fieldstr . "[OtherPersonalities],[NtContDate],[AnzLastContDate],[AnzLastBuyDate],[AnzBuyNearity],[AnzMidTermBuyMoney],[AnzMidTermBuyFreq],[AnzTotalBuyMoney],[AnzTotalBuyFreq],";
		//以下是错误的代码,应将'AnzLastContDate'改成'NtContDate'
		$fieldstr = $fieldstr . "[OtherPersonalities],(case when ISNULL(NtContDate,'1970-01-01')>ISNULL(PlanInviteDate,'1970-01-01') then NtContDate else PlanInviteDate end) 'AnzLastContDate',";
		$fieldstr = $fieldstr . "(case when ISNULL(NtContDate,'1970-01-01')>ISNULL(PlanInviteDate,'1970-01-01') then NtContDate else PlanInviteDate end) 'NtContDate',";
		$fieldstr = $fieldstr . "[AnzLastBuyDate],[AnzBuyNearity],[AnzMidTermBuyMoney],[AnzMidTermBuyFreq],[AnzTotalBuyMoney],[AnzTotalBuyFreq],";
		$fieldstr = $fieldstr . "PlanInviteDate,PlanInviteContent,[AnzBuyGapDays],[AnzMoneyPerSale],[AnzQtyPerSale],[AnzBuyPrice],AnzRelationLevel,AnzTotalScore";
		
		$customerinfo=$dbt
		->field($fieldstr)
		->where($condition)
		->select();
		$rs['CustomerInfo'] = $customerinfo;
		
		$dbt = M('sales','',getMyCon(2));
		$buyrecords = $dbt
		->join("LEFT JOIN SKUs on Sales.SKU=SKUs.SKU")
		->join("LEFT JOIN ProductColors on ProductColors.ProductColorCode=SKUs.ProductColorCode")
		->field("RecordTime,Sales.SKU,ProductCode,Color,Size,Year,WaveBand,Theme,Series,Class,TicketPrice,SaleQty,SaleMoney,PicturePath")
		->where($condition)
		->order("RecordTime desc")
		->select();		
		$rs['BuyRecords'] = $buyrecords;
		
		$buyspan= $dbt
		->join("LEFT JOIN SKUs on Sales.SKU=SKUs.SKU")
		->join("LEFT JOIN ProductColors on ProductColors.ProductColorCode=SKUs.ProductColorCode")
		->field("WaveBand,[WaveBand]+'—'+[Class] ProductType,sum(SaleQty) SaleQty,convert(decimal(10,0),SUM(SaleMoney)) SaleMoney,ROW_NUMBER() OVER(Order by WaveBand) WaveBandOrder")
		->where($condition)
		->group('WaveBand,Class')
		->having('SUM(SaleMoney)>0')
		->select();
		$rs['BuySpan'] = $buyspan;
//		echo $dbt->_sql();
		
		$dbt = M('contactrecords','',getMyCon(2));
		$contrecords = $dbt
		->field("CRecordCode,CustomerCode,ContDate,ContContent,MaintainerCode,ContWay,ResponseLevel,InviteResult")
		->where($condition)
		->order("ContDate desc")
		->select();
		$rs['ContRecords'] = $contrecords;

		return $rs;
	}
	
	// 获得门店各象限消费信息
 public function getStoreQuadInfo($storecode,$yearmonth) {
 				
		$storecode = $storecode;
		$preMonth = new \DateTime($yearmonth . "-01");

		$dmt = M("storeymvipdistribute","",getMyCon(2));
		$dmt->where("storecode='" . $storecode . "'")->delete();
		
		$Model = new \Think\Model("","",getMyCon(2));
		
		$preMonth = $preMonth->sub(new \DateInterval('P1M'));		
		$sqlstring = M('sqllist','',getMyCon(2))->where("SQLIndex='WBSQLStoreQuadAnz'")->getField("SQLCode");
		$sqlstring = str_replace('@parm1', $storecode, $sqlstring);
		$sqlstring = str_replace('@parm2', $preMonth->format('Y-m-d'), $sqlstring);
		setTag('sqlstring',$sqlstring);
		$Model->execute($sqlstring);

		$preMonth = $preMonth->sub(new \DateInterval('P1M'));		
		$sqlstring = M('sqllist','',getMyCon(2))->where("SQLIndex='WBSQLStoreQuadAnz'")->getField("SQLCode");
		$sqlstring = str_replace('@parm1', $storecode, $sqlstring);
		$sqlstring = str_replace('@parm2', $preMonth->format('Y-m-d'), $sqlstring);
		$Model->execute($sqlstring);
			
		$preMonth = $preMonth->sub(new \DateInterval('P1M'));		
		$sqlstring = M('sqllist','',getMyCon(2))->where("SQLIndex='WBSQLStoreQuadAnz'")->getField("SQLCode");
		$sqlstring = str_replace('@parm1', $storecode, $sqlstring);
		$sqlstring = str_replace('@parm2', $preMonth->format('Y-m-d'), $sqlstring);
		$Model->execute($sqlstring);
		
		$eachquadrs= $dmt
		->field("QuadName,sum(TotalVIPBuyMoney) TotalVIPBuyMoney, sum(isnull(TotalPersonCount,0)) TotalPersonCount,	(select SUM(TotalVIPBuyMoney) from StoreYMVIPDistribute where storecode='" . $storecode . "') TotalVIPBuyMoneyInAllQuad")
		->where("storecode='" . $storecode . "'")
		->group("QuadName")
		->select();
//		dump($eachquadrs);
				
		$i=0;
		foreach($eachquadrs as $quard) {
			$i += 1;
			if($quard['totalvipbuymoneyinallquad']>0)
			$quardinfo["Q". $i ."_CalcMoneyPercent"] = round($quard['totalvipbuymoney']/$quard['totalvipbuymoneyinallquad'],2);
			else
			$quardinfo["Q". $i ."_CalcMoneyPercent"] = 0;
			
			if($quard['totalpersoncount']>0)
			$quardinfo["Q". $i ."_CalcMoneyPerCustomer"] = round($quard['totalvipbuymoney']/$quard['totalpersoncount'],0);
			else
			$quardinfo["Q". $i ."_CalcMoneyPerCustomer"] = 0;
			
		}
//		dump($quardinfo)	;
		
		return $quardinfo;	
		
		}

	//获得VIP的一些配置信息
	public function getGlobalVIPParameters($paraname)
	{
		 $dmb = M("sysparameters","",getMyCon(2));
		 
		 $condition['Name'] = $paraname;
		 $paratype = $dmb->where($condition)->getField('type');
		 
		 $rs = $dmb->where($condition)->getField($paratype);
		 
		 return $rs;
	}

	//获得VIP的温度信息
	public function getVIPTEMPTrend($CustomerCode,$ShowDays)
	{
		/**
		 * 1.得到n天前的起点累计温度和日期
		 */
		 $dmb = M("vw_viptemprecords","",getMyCon(2));
	
		 $daysAgo = new \DateTime(date('Y-m-d'));
		 $daysAgo->sub(new \DateInterval('P' . $ShowDays .'D'));
		 
		 $condition['RecordDate'] = array('lt',$daysAgo->format('Y-m-d'));
		 $condition['CustomerCode'] = $CustomerCode;
				 
		 $BasicTEMP = $dmb->where($condition)->Sum('AddTemperature'); 
		 $BasicYScore = $dmb->where($condition)->Sum('YScore'); 
		 $BasicDate = $dmb->where($condition)->Min('RecordDate');

				 
		 if(!$BasicTEMP)  $BasicTEMP = 0;
		 if(!$BasicYScore) $BasicYScore = 0;

		 if(!$BasicDate) 
		{
			 if($BasicDate<$daysAgo->format('Y-m-d')) 
			 {
			 	$BasicDate = $daysAgo->format('Y-m-d');
			 } 
		}
		else
			{
				$BasicDate = $daysAgo->format('Y-m-d');
			}
		 
		/**
		 * 2.得到每天增加的温度
		 */
		 unset($condition);
		 $condition['Date'] = array(array('egt',$BasicDate),array('elt',date('Y-m-d')));
		 $array_AddTEMPYScore = M("vw_dateinfo","",getMyCon(2))
		 ->join("left join vw_viptemprecords on vw_dateInfo.date=vw_viptemprecords.RecordDate and vw_viptemprecords.CustomerCode='" . $CustomerCode . "'")
		 ->field("convert(varchar,MONTH(date))+'.'+convert(varchar,DAY(date)) Date,WeekDay,sum(isnull(AddTemperature,0)) temp,sum(isnull(YScore,0)) YScore")
		 ->where($condition)
		 ->group('Date,WeekDay')
		 ->order('Date asc')
		 ->select();

		 		 
		 $totalTEMP = $BasicTEMP;
		 $totalYScore = $BasicYScore;
		 
		 $j = 0;
		 for ($i=0; $i <count($array_AddTEMPYScore); $i++) {

		 	 $item = $array_AddTEMPYScore[$i];
						
			 $totalTEMP = $totalTEMP + $item['temp'];
		 	 $totalYScore = $totalYScore + $item['yscore'];
			
			if($item['weekday']==1)
			{
				$rs['imgdata'][$j]['date'] = $item['date'];
				$rs['imgdata'][$j]['temp'] = $totalTEMP;
				$rs['imgdata'][$j]['yscore'] = $totalYScore;
				$j = $j+1;
			}
		 	
		 }
			$rs['YValueLimit'] = $totalYScore+5;
		 return $rs;
	}

}
?>
	