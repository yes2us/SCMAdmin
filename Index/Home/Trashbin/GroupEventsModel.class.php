<?php
namespace Home\Model;
use Think\Model\RelationModel;

class GroupEventsModel extends RelationModel{
	protected $trueTableName = 'GroupEvents';  
	
		protected $_link = array(	
		'group' => array(
		'mapping_type'  => self::HAS_ONE,
        'class_name'    => 'groups',
        'foreign_key'   => 'groupname',
        'mapping_key'   => 'groupname',
        'as_fields' 	=> 'grouptype,groupenabled,groupdesc',
		 ),
		'event' => array(
		'mapping_type'  => self::HAS_ONE,
        'class_name'    => 'events',
        'foreign_key'   => 'eventcode',
        'mapping_key'   => 'eventcode',
	    'as_fields' 	=> 'event,eventtype,eventscope,yscore,xscore,remark',
		 ),
		 
		);
	
}

?>
	