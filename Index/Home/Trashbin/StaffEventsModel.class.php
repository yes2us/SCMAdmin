<?php
namespace Home\Model;
use Think\Model\RelationModel;

class StaffEventsModel extends RelationModel{
	protected $trueTableName = 'ViewStaffEvents'; 
	 	
//	protected $trueTableName = 'StaffEvents';  	
//		protected $_link = array(	
//		'Staff' => array(
//		'mapping_type'  => self::HAS_ONE,
//      'class_name'    => 'staffs',
//      'foreign_key'   => 'staffcode',
//      'mapping_key'   => 'staffcode',
//      'as_fields' 	=> 'staffname',
//		),
//		'event' => array(
//		'mapping_type'  => self::HAS_ONE,
//      'class_name'    => 'events',
//      'foreign_key'   => 'eventcode',
//      'mapping_key'   => 'eventcode',
//	    'as_fields' 	=> 'event,eventtype,eventscope,yscore,xscore,remark',
//		 ),
//		 
//		);
	
}

?>
	