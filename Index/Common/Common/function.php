<?php

require_once './oss-php/sdk.class.php';
require_once './import-excel-php/reader.php';
 
function p($info) {
	dump($info);
}

function setTag($label, $context) {
	$debug = D("Debug");
	// 实例化User对象
	$debug -> tag($label, $context);
}

//$today = date("Y-m-d");
//$day = getthemonth($today);
//$currentDate = date('Y-m-d', time());
function getthemonth($date) {
	$firstday = date('Y-m-01', strtotime($date));
	$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
	return array($firstday, $lastday);
}

function getVIPConnectionInfo()
{
	return array(	'DB_TYPE' => 'sqlsrv',
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_Host' => 'Localhost\SQL2008',
//	'DB_Host' => '120.24.229.218\SQL2008',
	'DB_User' => 'sa',
	'DB_PWD'  => 'Rickywang9',
	'DB_NAME' => 'eekavip');
}

//function getMyCon($POST,$GET,$DSNo=1)
function getMyCon($DSNo=1)
{
	$DSSuffix = null;
		
  			
	switch ($_SERVER['REQUEST_METHOD']) {
		case "POST":
				if(isset($_POST['DSSuffix']))
				{
					$DSSuffix = strtolower(trim($_POST['DSSuffix']));
				}
			break;
			
		case "GET":
				if(isset($_GET['DSSuffix']))
				{
					$DSSuffix = strtolower(trim($_GET['DSSuffix']));
				}
			break;
			
		default:
			break;
	}

					
	switch ($DSSuffix) {
		case 'linesoul.com':
				if($DSNo==1)
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'linesoulpoa');
			  }
			  else
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'linesoulvip');
			  }
			break;

		case 'eekabsc.com':
				if($DSNo==1)
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'eekapoa');
			  }
			  else
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'eekavip');
			  }
			break;
		
		default:
			  if($DSNo==1)
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'eekapoa');
			  }
			  else
			  {
			  		return array(	'DB_TYPE' => 'sqlsrv',
					'DB_CHARSET'=> 'utf8', // 字符集
					'DB_Host' => 'Localhost\SQL2008',
					'DB_User' => 'sa',
					'DB_PWD'  => 'Rickywang9',
					'DB_NAME' => 'eekavip');
			  }
			break;
	}

}

function getInputValue($attName,$default=null)
{
	$attValue = $default;
	
	switch ($_SERVER['REQUEST_METHOD']) {
		case "POST":
				if(isset($_POST[$attName]))
				{
					$attValue = $_POST[$attName];
				}
			break;
			
		case "GET":
				if(isset($_GET[$attName]))
				{
					$attValue = $_GET[$attName];
				}
			break;
			
		default:
			break;
	}	
	return $attValue;
}

function upload2OSS($bucket,$key,$filepath)
{	
	$oss = new ALIOSS();
	$oss->set_debug_mode(TRUE);
	
	try
	{		
		$response = $oss->upload_file_by_file($bucket,$key,$filepath);		
//		echo $response;
		$timeout = 3600*24*365*10;
		$response = $oss->get_sign_url($bucket,$key,$timeout);
		return $response;

	}catch (Exception $ex){
	die($ex->getMessage());
	}
	
	return null;
}

function getOSSFilePath($bucket,$key)
{	
	$oss = new ALIOSS();
	$oss->set_debug_mode(TRUE);
	try
	{		
		$timeout = 3600*24;
		$response = $oss->get_sign_url($bucket,$key,$timeout);
		return $response;

	}catch (Exception $ex){
	die($ex->getMessage());
	}
}

  function importExcel2DB($conn,$tableName,$fileName,$fieldArray=null)
  {
  	$dbmodel = new \Think\Model("","",$conn);
	
  	 $data = new Spreadsheet_Excel_Reader();
	 $data->setOutputEncoding('UTF-8');
//	 $data->setOutputEncoding('gbk');
//	$data->setUTFEncoder('mb');
	
	 $data->read($fileName);
	 error_reporting(E_ALL ^ E_NOTICE);

	 if($data->sheets[0]['numRows']>0)
 {
 		$colArray = array();
 	//1.生成insert header语句：insert into #temp(排名,店长,店名,Y分)
    	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
    	{
    		$cellvalue = $data->sheets[0]['cells'][1][$j];
				
			if($cellvalue=='消费日期')		$dateColIndex = $j;
			
			if($cellvalue>"")
			{
				if($fieldArray)
				{
					$result = array_search($cellvalue,$fieldArray);
					if(gettype($result) == 'boolean')
					{
						continue;
					}
					else
					{
						array_push($colArray,$j);
					}
				}
		
				
				if($j==1)
				{
					$sqlInsertCode = "insert into " . $tableName . "(" . $cellvalue;
				}
				else if($j == $data->sheets[0]['numCols'])
				{
					$sqlInsertCode = $sqlInsertCode . "," . $cellvalue . ") \n";
				}
				else
				{
					$sqlInsertCode = $sqlInsertCode . "," . $cellvalue;
				}
			}
		}
		
		//如果最后一个有效列名不是最后一列,那么加上反括号
		if(count($colArray)>0)
		{
			if($j-1 != $colArray[count($colArray)-1]) 
			 $sqlInsertCode = $sqlInsertCode . ")";
		}
		
//		p($colArray);
	//2.生成插值语句
	
		    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
	    	for ($j = 0; $j < count($colArray); $j++) 
    	{
    		$cellvalue = $data->sheets[0]['cells'][$i][$colArray[$j]];
			
				if($j==0)
				{
					$sqlCode = $sqlInsertCode . "values('" . $cellvalue ."'";
				}
				else if($j == count($colArray)-1)
				{
					$sqlCode = $sqlCode . ",'" . $cellvalue . "') \n";
//					echo $sqlCode;
//					setTag('sql',$sqlCode);
					$dbmodel -> execute($sqlCode);
				}
				else
				{
					$sqlCode = $sqlCode . ",'" . $cellvalue . "'";
				}

		}
 }

  }
  
?>