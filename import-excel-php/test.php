<?php
// Test CVS

require_once 'reader.php';


$data = new Spreadsheet_Excel_Reader();

$data->setOutputEncoding('UTF-8');


$data->read('grid.xls');

error_reporting(E_ALL ^ E_NOTICE);


if($data->sheets[0]['numRows']>0)
 {
 	//1.生成insert header语句：insert into #temp(排名,店长,店名,Y分)
    	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
    	{
    		$cellvalue = $data->sheets[0]['cells'][1][$j];
			if($cellvalue>"")
			{
				if($j==1)
				{
					$sqlInsertCode = "insert into #temp(" . $cellvalue;
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
		
	//2.生成插值语句
		    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
	    	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
    	{
    		$cellvalue = $data->sheets[0]['cells'][$i][$j];
			if($cellvalue>"")
			{
				if($j==1)
				{
					$sqlCode = $sqlInsertCode . "values('" . $cellvalue ."'";
				}
				else if($j == $data->sheets[0]['numCols'])
				{
					$sqlCode = $sqlCode . ",'" . $cellvalue . "') \n";
					echo $sqlCode;
				}
				else
				{
					$sqlCode = $sqlCode . ",'" . $cellvalue . "'";
				}
			}
		}
 }




//print_r($data);
//print_r($data->formatRecords);
?>
