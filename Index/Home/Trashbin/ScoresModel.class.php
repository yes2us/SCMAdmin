<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ScoresModel extends RelationModel{
		protected $trueTableName  = 'ScoreRecords';
		
	protected $_link = array(	
		'Staffs' => array(
		'mapping_type'  => self::HAS_ONE,
        'class_name'    => 'Staffs',
        'foreign_key'   => 'staffcode',
        'mapping_key'   => 'staffcode',
        'as_fields' 	=> 'staffname',
		)
		);
	
}

?>
