<?php
return array(
	//'配置项'
	'DB_TYPE' => 'sqlsrv',
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_Host' => 'Localhost\SQL2008',
//	'DB_Host' => '120.24.229.218\SQL2008',
	'DB_User' => 'sa',
	'DB_PWD'  => 'Rickywang9',
	'DB_NAME' => 'eekapoa',
	
	//默认的数据库驱动类设置了 字段名强制转换为小写，如果你的数据表字段名采用大小写混合方式的话，需要在配置文件中增加如下设置
	//调用时一定要注意字段的大小写与数据库一致
//	'DB_PARAMS'=>array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL)
	//'TMPL_FILE_DEPR' => '_',
);