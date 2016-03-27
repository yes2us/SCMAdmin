<?php
namespace Home\Model;
use Think\Model;

class SysParaModel extends Model{
	protected $trueTableName = 'sysparameters';  
	public function getSys($filter)
	{
		echo 'you are in get sys function';
	}
}

?>
	