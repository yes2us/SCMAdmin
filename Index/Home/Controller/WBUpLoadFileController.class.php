<?php
namespace Home\Controller;

class WBUpLoadFileController extends \Think\Controller {
	
	public function uploadPersonPhoto() {
		setTag('path',ABSPATH);
		$PictureOwner = $_GET["PictureOwner"];
		$DSSuffix = $_GET["DSSuffix"];
				
		$config = array(    
			'maxSize'    =>    31457280,    
			'savePath'   =>    '',    
			'saveName'   =>    array('uniqid',''),    
			'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),    
			'autoSub'    =>    true,    
			'subName'    =>    array('date','Ymd'),
		);

		$upload = new \Think\Upload($config);
	
		
		// 上传单个文件
		$info   =   $upload->upload();
		if(!$info)
		{// 上传错误提示错误信息       
		 	$this->error($upload->getError());    
			return $this -> ajaxReturn("{ status: 'error'}");
		 }else{// 上传成功        
				
			$fullsavename = null;
			    foreach($info as $file){
			    	$fullsavename = "./UpLoads/".$file["savepath"].$file["savename"];
//					setTag('savepath', $fullsavename) ;
				}
//			$osspath = 	upload2OSS("eekavip",$PictureOwner,"D:/phpStudy4IIS/WWW/POAAdmin/" .$fullsavename);

			switch (strtolower($DSSuffix)) {
				case 'linesoul.com':
					  $bucket = 'linesoulperson';
					break;
				case 'eekabsc.com':
					  $bucket = 'eekaperson';
					break;
				default:
					  $bucket = 'eekaperson';
					break;
			}
			$osspath = 	upload2OSS($bucket,$PictureOwner,$fullsavename);

			//echo "{ status: 'server',fullsname:'". $osspath ."'}";
			echo "{ status: 'server',fullsname:'". $PictureOwner ."'}";
		 }
	}


	public function getFilePath() {
		$key = getInputValue('KeyId');
		$DSSuffix = getInputValue("DSSuffix");

			switch (strtolower($DSSuffix)) {
				case 'linesoul.com':
					  $bucket = 'linesoulperson';
					break;
				case 'eekabsc.com':
					  $bucket = 'eekaperson';
					break;
				default:
					  $bucket = 'eekaperson';
					break;
			}
		
		$response = getOSSFilePath($bucket,$key);
			
		echo $response;
	}
	
	public function importExcel2DB() {
		ini_set('memory_limit', '2048M');
		set_time_limit(1200);
//		echo 'mem' . ini_get('memory_limit');
		
		$config = array(    
			'maxSize'    =>    31457280,    
			'savePath'   =>    '',    
			'saveName'   =>    array('uniqid',''),    
			'exts'       =>    array('xls'),    
			'autoSub'    =>    true,    
			'subName'    =>    array('date','Ymd'),
		);

		$TargetTable = substr(strrchr($_SERVER['PHP_SELF'],"/"),1);			
		$upload = new \Think\Upload($config);
		
		// 上传单个文件
		$info   =   $upload->upload();
		if(!$info)
		{// 上传错误提示错误信息       
		 	$this->error($upload->getError());    
			return $this -> ajaxReturn("{ status: 'error'}");
		 }else{// 上传成功        
				
		$fullsavename = null;
		  foreach($info as $file){
		   	$fullSaveName = "./UpLoads/".$file["savepath"].$TargetTable. "_" .$file["savename"];
//				setTag('savepath', $fullSaveName) ;
		}

		$fullSaveName = "./UpLoads/20160417/testsmall.xls";
		$fieldArray = array("门店编号","SKUCode","消费日期","销售金额","销售数量");		
//		$tableName = 'importsale';

		importExcel2DB(getMyCon(),$TargetTable,$fullSaveName,$fieldArray);	
			return $this -> ajaxReturn("{ status: 'server',fullsname:'". $fullSaveName ."'}");
		}	
	}

	public function getImportData(){
		$TargetTable = getInputValue('TargetTable');
		$pageStr = getInputValue("Page","1,1000");
		
		$rs = M($TargetTable,"",getMyCon())
			->where($condition)
			->page($pageStr)
			->select();
		
		return $this -> ajaxReturn($rs);
	}
	
	public function clearImportData(){
		$TargetTable = getInputValue('TargetTable');
				
		$Model = new \Think\Model("","",getMyCon());
		$sqlstring = " truncate table " . $TargetTable;
		$rs = $Model->execute($sqlstring);
		return $this -> ajaxReturn($rs);
	}
	
	public function saveImportData(){
		$TargetTable = getInputValue('TargetTable');
		
		$Model = new \Think\Model("","",getMyCon());
		$sqlstring = " exec Import". $TargetTable ."Data";
		$rs = $Model->execute($sqlstring);
		return $this -> ajaxReturn("OK");
	}
}
?>