<?php
return array(
	"mysqli"=>array(
		"type"=>\LSYS\Database\MYSQLi::class,
		"charset"=>"UTF8",
		"table_prefix"=>"l_",
		"try_re_num"=>"2",
		"try_re_sleep"=>"1",
		"connection"=>array(
			'database' => 'test',
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'persistent' => FALSE,
			"variables"=>array(
			),
		)
	)
);