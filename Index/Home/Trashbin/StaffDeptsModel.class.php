<?php
namespace Home\Model;
use Think\Model\RelationModel;

class StaffDeptsModel extends RelationModel{
	
	protected $trueTableName  = 'DeptStaffs';		
	protected $_link = array(	
		'Staff' => array(
		'mapping_type'  => self::HAS_ONE,
        'class_name'    => 'staffs',
        'foreign_key'   => 'staffcode',
        'mapping_key'   => 'staffcode',
        'as_fields' 	=> 'staffname,isonjob',
		),
		'depts' => array(
		'mapping_type'  => self::HAS_ONE,
        'class_name'    => 'depts',
        'foreign_key'   => 'deptcode',
        'mapping_key'   => 'deptcode',
        'as_fields' 	=> 'deptlevel,deptname,deptsname,depttype,enabled,t2yconvertrate,tvalue2tscorerate',
		 ),
		 
		);
	
}

?>
