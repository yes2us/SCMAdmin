<?php
namespace Home\Model;
use Think\Model;

/**
 * 这是会员管理的对象
 */
class ProdObjectModel extends Model {
     protected $trueTableName = 'Customers';
	  
	// 获得产品列表
	public function getProductList($condition,$pagestr,$fieldstr) {
		
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


	//获得产品的指标
	public function getProdIndicator($StoreCode) {
		

		return $rs;
	}
	
	//获得产品的销售
	public function getProdSale($StoreCode) {
		

		return $rs;
	}
	

}
?>
	