<?php

	require_once('./Database/Mysql.php');
	require_once('./Database/Mysql/Exception.php');
	require_once('./Database/Mysql/Statement.php');

	$db = Database_Mysql::create('localhost', 'root', 'r00t')
           ->setCharset('utf8')
           ->setDatabaseName('test');

	


	