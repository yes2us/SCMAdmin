<?php
namespace Home\Controller;

class WBCURDMngController extends \Think\Controller {

	/**
	 * 获得操作表
	 */
	 private function _CURDOperation($tableName,$POSTData,$attArray,$uniqArray)
	 {

	 	$dbm = M(strtolower($tableName),"",getMyCon()); 
		
	 	switch ($POSTData['webix_operation']) {
			
				case 'insert':				
				
				if($uniqArray && (count($uniqArray)>0))	
				{
					foreach($uniqArray as $uniqatt)  $condition[$uniqatt] = $POSTData[strtolower($uniqatt)];	
					$rs=$dbm->where($condition)->select();
//					setTag('sssql', $dbm->_sql());
					if(count($rs)>0) return "duplicate record";
				}
				
				foreach($attArray as $att) 
				{
					if(isset($POSTData[strtolower($att)])) $data[$att] = $POSTData[strtolower($att)];
				}	

				$recordid = $dbm -> add($data);
				
//				dump($dbm ->_sql());			
				if($recordid<1) return 'fail';
				
				return $recordid;
				break;
				
			case 'update':
				
				foreach($attArray as $att) 
				{
					if(isset($POSTData[strtolower($att)])) $data[$att] = $POSTData[strtolower($att)];
				}	
						
				$saveresult = $dbm->where('_Identify=' . $POSTData['_identify']) ->save($data);
				if($saveresult===false) return "fail";//$saveresult为更新的记录条数，$saveresult=0表示更新数据与原数据没变化
				
				return "success";
				break;
				
			case 'delete':
				
				$saveresult = $dbm->where('_Identify=' . $POSTData['_identify']) ->delete();
				if($saveresult===false) return "fail";//$saveresult为更新的记录条数，$saveresult=0表示更新数据与原数据没变化
				
				return "success";
			break;
				
			default:
				
				break;
		}
	 }

 	  /**
	  * ***************操作debug***************************
	  */
	  public function saveDebugRecord()
	  {
	  	if(stripos("delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
//	  	if(stripos("insert|update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
	  	$tableName = "boperationrecord";
		$attArray = array('ModuleName','RecordLabel');
		$uniqArray = nulll;
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	 /**
	  * ***************操作参数表***************************
	  */
	  public function saveParameter()
	  {
	  	if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
		
	  	$tableName = "bsyspara";
		$attArray = array('Name','Type','VInteger','VFloat','VDate','VBool','VString','VText','Desc');
		$uniqArray = array("Name");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  
	  
	/**
	  * ***************操作角色表***************************
	  */  
	  	public function saveRole()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "brole";
		$attArray = array('RoleName','RoleEnabled','RoleType','RoleDesc');
		$uniqArray = array("RoleName");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqAtt);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	
	  /**
	  * ***************操作角色用户***************************
	  */ 
	  	 public function saveRoleUser()
	  {
	    if(stripos("insert|delete|update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "buserrole";
		$attArray = array('RoleName','UserCode');
		$uniqArray = array('RoleName','UserCode');
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	
	  /**
	  * ***************操作权限表***************************
	  */ 	  
	   public function savePrevilege()
	  {
	    if(stripos("insert|delete|update",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "bprevilege";
		$attArray = array("RoleName","ModuleID","Open","Operation");
		$uniqArray = array("RoleName","ModuleID");
		
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	
	  /**
	  * ***************操作用户表***************************
	  */ 
	  public function saveUser()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "buser";
		$attArray = array("UserCode","UserTrueName","UserType","UserEnabled");
		$uniqArray = array("UserCode");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
		
		if($_POST['webix_operation']=='insert' || I("isresetpwd"))
		{
			$initPWD = '123456';
			$Model = new \Think\Model('',getMyCon());
			$sqlstr = "update buser Set [UserPassword]= pwdencrypt('" . $initPWD . "')  where UserCode ='" . I('UserCode') . "'";
			$rs = $Model -> execute($sqlstr);
			$status = "OK";
		}
				
		return $this -> ajaxReturn($status);
	  }
	  
	  
	   /**
	  * ***************操作仓库表***************************
	  */ 
	  public function saveParty()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "bparty";
		$attArray = array("PartyCode","PartyName","PartyType","PartyLevel","PartyEnabled","IsReplenish","RepPriority",
						"RepBatchSize","RepNextDate","RepOrderCycle","RepSupplyTime","RepRollSpan","IsReturnStock",
						"RetBatchSize","IsRetOverStock","RetOverStockNextDate","RetOverStockCycle","IsRetDeadStock",
						"RetDeadStockNextDate","RetDeadStockCycle","IsBM","IsUseSKUBMPara","BMUpChkPeriod",
						"BMUpFreezePeriod","BMUpErodeLmt","BMDnChkPeriod","BMDnFreezePeriod","BMDnErodeLmt");
		$uniqArray = array("PartyCode");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	 
	 
	   /**
	  * ***************操作仓库与仓库的关系***************************
	  */ 
	  public function saveParty2PartyRelation()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "bparty2partyrelation";
		$attArray = array("PartyCode","ParentCode","RelationType","RelationOrder");
		$uniqArray = array("PartyCode","ParentCode","RelationType");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	    /**
	  * ***************操作仓库与货品的关系***************************
	  */ 
	  public function saveParty2ProdRelation()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "bparty2prodrelation";
		$attArray = array("PartyCode","ProdAttName","ProdAttValue","ProdIsReplenish","ApplyOrder");
		$uniqArray = array("PartyCode","ProdAttName","ProdAttValue");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	      /**
	  * ***************操作模块表***************************
	  */ 
	  public function saveModule()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "bwbmodule";
		$attArray = array("ParentModuleID","ModuleID","ModuleName","ModuleICON","ModuleDesc","ModuleLevel");
		$uniqArray = array("ModuleID");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	    /**
	  * ***************操作调拨计划表 :SKC***************************
	  */ 
	  public function saveMovSKCPlan()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "dmovskcplan";
		$attArray = array("PlanType","MakeDate","SrcPartyCode","TrgPartyCode","SKCCode","MovQty","Operator","DealState");
		$uniqArray = array("PlanType","MakeDate","SrcPartyCode","TrgPartyCode","SKCCode");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  	    /**
	  * ***************操作调拨计划表 :SKU***************************
	  */ 
	  public function saveMovSKUPlan()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "dmovskuplan";
		$attArray = array("PlanType","MakeDate","SrcPartyCode","TrgPartyCode","SKUCode","MovQty","Operator","DealState");
		$uniqArray = array("PlanType","MakeDate","SrcPartyCode","TrgPartyCode","SKUCode");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	  
	  
	   /* ***************操作库存表 ***************************
	  */ 
	    public function saveStock()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "dstock";
		$attArray = array("IsSKURep","IsSKURet","IsBM","SugTarget","TargetQty",
								"SupplyTime","OrderCycle","BMUpChkPeriod","BMUpFreezePeriod",
								"BMUpErodeLmt","BMUpErodePer","BMUpMoniterDate","BMDnChkPeriod",
								"BMDnFreezePeriod","BMDnErodeLmt","BMDnErodeTimes","BMDnMoniterDate");
		$uniqArray = array("PartyCode","SKUCode");
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,$uniqArray);
				
		return $this -> ajaxReturn($status);
	  }
	  
	   /* ***************操作调整记录表***************************
	  */ 
	    public function saveBMRecord()
	  {
	    if(stripos("insert|update|delete",$_POST['webix_operation'])===false)  return $this -> ajaxReturn("not permit");
	  		  	
	  	$tableName = "dbmrecord";
		$attArray = array("PartyCode","SKUCode","RecordDate","OldTargetQty","SugTargetQty","BMReason","Operator","DealState");
		
		$status = $this->_CURDOperation($tableName,$_POST,$attArray,null);
				
		return $this -> ajaxReturn($status);
	  }
	    	  
}
?>