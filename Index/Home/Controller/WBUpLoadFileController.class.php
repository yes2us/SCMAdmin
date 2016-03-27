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
		$config = array(    
			'maxSize'    =>    31457280,    
			'savePath'   =>    '',    
			'saveName'   =>    array('uniqid',''),    
			'exts'       =>    array('xls'),    
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
		   	$fullSaveName = "./UpLoads/".$file["savepath"].$file["savename"];
				setTag('savepath', $fullSaveName) ;
		}
	
//		$fullSaveName = "./UpLoads/20150602/556da9ef2417e.xls";
		$fieldArray = array("渠道四级名称","会员名称","会员编号","渠道五级名称","会员手机号码","会员归属品牌","产品编号",
		"吊牌价","产品年代","产品波段","产品主题","产品系列","零售单号","总销售金额","总销售数量","消费日期");		
		$tableName = 'TempSaleBill';
		importExcel2DB(getMyCon(2),$tableName,$fullSaveName,$fieldArray);	
		}	
	}

	public function getImportData(){
		$PageIndex = $_POST['PageIndex'];
		$PageLen = $_POST['PageLen'];

		$Model = new \Think\Model("","",getMyCon(2));
		$sqlstring = M('sqllist','',getMyCon())->where("SQLIndex='WBSQL_ImpSaleData'")->getField("SQLCode");
		$sqlstring = str_replace('@parm1', $PageIndex, $sqlstring);
		$sqlstring = str_replace('@parm2', $PageLen, $sqlstring);
		
		$rs = $Model -> query($sqlstring);
		
		return $this -> ajaxReturn($rs);
	}
	
	public function clearImportData(){
		$Model = new \Think\Model("","",getMyCon(2));
		$sqlstring = " truncate table TempSaleBill";
		$rs = $Model->execute($sqlstring);
		return $this -> ajaxReturn($rs);
	}
	
	public function saveImportData(){
		$Model = new \Think\Model("","",getMyCon(2));
		$sqlstring = " exec ImportVIPData";
		$rs = $Model->execute($sqlstring);
		return $this -> ajaxReturn("OK");
	}
}
?>