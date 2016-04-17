<?php
namespace Home\Controller;

class WBEventMngController extends \Think\Controller {
	 /**
	 * 获得事件范围
	 */
	 public function getEventScope()
	 {	 			
			$rs = M('eventscopes',"",getMyCon())
		 	->field("distinct _identify,eventscopecode,eventscope,eventscopeorder,EventScopeEnabled")
			->order("eventscopeorder")
		 	->select();
			
		return $this -> ajaxReturn($rs);
	 }

	 /**
	 * 获得事件范围下拉列表
	 */
	 public function getScopeSelectList()
	 {
	 			
			$rs = M('eventscopes',"",getMyCon())
		 	->field("distinct eventscopecode as id,eventscope as value,eventscopeorder")
			->order("eventscopeorder")
		 	->select();
		  return $this -> ajaxReturn($rs);
	 }

	/**
	 * 获得事件类型
	 */
	 public function getEventType()
	 {
	 		$eventscopecode = getInputValue('EventScopeCode');
	 	    if($eventscopecode)
			{
				$condition['eventscopes.EventScopeCode'] = $eventscopecode;
				$rs = M('eventtypes',"",getMyCon())
				->join("left join eventscopes on eventscopes.eventscopecode=eventtypes.eventscopecode")
			 	->field("distinct eventtypes._identify,eventscopes.eventscopecode,eventscope,eventscopeorder,eventtypecode,eventtype,eventtypeenabled,eventtypeorder")
				->where($condition)
				->order("eventscopeorder,eventtypeorder")
			 	->select();
			}
			else
			{
				$rs = M('eventtypes',"",getMyCon())
				->join("left join eventscopes on eventscopes.eventscopecode=eventtypes.eventscopecode")
			 	->field("distinct eventtypes._identify,eventscopes.eventscopecode,eventscope,eventscopeorder,eventtypecode,eventtype,eventtypeenabled,eventtypeorder")
				->order("eventscopeorder,eventtypeorder")
			 	->select();
			}

		return $this -> ajaxReturn($rs);
	 }
	 
	/**
	 * 获得事件类型下拉列表
	 */
	 public function getTypeSelectList()
	 {
		$condition['eventscopes.EventScopeCode'] = I("EventScopeCode");

		$rs = M('eventtypes',"",getMyCon())
		->join("left join eventscopes on eventscopes.eventscopecode=eventtypes.eventscopecode")
		->field("distinct eventtypecode as id,eventtype as value,eventtypeorder")
		->where($condition)
		->order("eventtypeorder")
		->select();
		
		return $this -> ajaxReturn($rs);
	 }
	 
	/**
	 * 获得事件内容
	 */
	 public function getEvent()
	 {
	 	    if(isset($_POST['EventTypeCode']))
			{
				$condition['Events.EventTypeCode'] = I("EventTypeCode");
				$rs = M('events',"",getMyCon())
				->where($condition)
				->join("left join EventTypes on EventTypes.EventTypeCode = Events.EventTypeCode")
				->join("left join EventScopes on EventScopes.EventScopeCode = EventTypes.EventScopeCode")
				->field("events.[_Identify],[EventCode],[Event],[Events].[EventTypeCode],[EventTypes].[EventType],[EventTypes].[EventScopeCode],[EventScopes].[EventScope],[YScore],[XScore],[Remark],[DeliveryWay],[MaintainTime],[MaintainerCode],[EventEnabled],eventscopeorder,eventtypeorder,eventorder")
				->order("eventscopeorder,eventtypeorder,eventorder")
			 	->select();
//			 	echo M('events')->_sql();
			}
			else
			{
				$rs = M('events',"",getMyCon())
				->join("left join EventTypes on EventTypes.EventTypeCode = Events.EventTypeCode")
				->join("left join EventScopes on EventScopes.EventScopeCode = EventTypes.EventScopeCode")
				->field("events.[_Identify],[EventCode],[Event],[Events].[EventTypeCode],[EventTypes].[EventType],[EventTypes].[EventScopeCode],[EventScopes].[EventScope],[YScore],[XScore],[Remark],[DeliveryWay],[MaintainTime],[MaintainerCode],[EventEnabled],eventscopeorder,eventtypeorder,eventorder")
				->order("eventscopeorder,eventtypeorder,eventorder")
			 	->select();
//			 	echo M('events')->_sql();
			}
		return $this -> ajaxReturn($rs);
	 }

	 	/**
	 * 获得事件内容
	 */
	 public function getEventSelectName()
	 {
				$condition['events.EventTypeCode'] = I("EventTypeCode");
				$rs = M('events',"",getMyCon())
				->join("left join EventTypes on EventTypes.EventType = Events.EventType")
				->field("[EventCode] as id,[Event] as value,eventorder")
				->where($condition)
				->order("eventorder")
			 	->select();
//		echo M('events',"",getMyCon())->_sql();
		return $this -> ajaxReturn($rs);
	 }
	 
	
}
?>