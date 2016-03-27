<?php
namespace Home\Model;
use Think\Model\RelationModel;

class DeptsModel extends RelationModel{
	protected $_link = array(
	   'Depts' => array
	   (
	     'mapping_type'  => self::HAS_ONE,
         'class_name'    => 'Depts',
         'foreign_key'   => 'SupDeptCode',
      	 'mapping_key'   => 'DeptCode',
         'parent_key' 	=> 'DeptCode',
        'as_fields' 	=> 'DeptName:SupDeptName,DeptType:SubDeptType', 
	   ),
	  );
}

?>