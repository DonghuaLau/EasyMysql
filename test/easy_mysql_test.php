<?php

include(__dir__ . "/../php/easy_mysql.php");

$create_table = "
CREATE TABLE `task` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `done` tinyint(1) DEFAULT '0',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8
";

function test_insert()
{
	$em = new EasyMysql('root', 'password', 'table', '127.0.0.1');
	$id = $em->insert(
			'task'
			,array(
				 'name' => 'Mike'
				,'done' => 24 
				,'time' => '2017-01-04 12:21:08' 
			)
			,array(
				 '%s'
				,'%d'
				,'%s'
			)
		);
	echo 'insert id: ' . $id . ", sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
}

function test_update()
{
	$em = new EasyMysql('root', 'password', 'table', '127.0.0.1');
	$ret = $em->update(
			'task'
			,array(
				 'name' => 'Jackey'
				,'done' => 25
				,'time' => '2017-01-05 02:31:19' 
			)
			,array(
				 'id' => 7
			)
			,array(
				 '%s'
				,'%d'
				,'%s'
			)
			,array(
				 '%d'
			)
		);
	echo "update ret: $ret" . ", sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";

	$ret = $em->update(
			 'task'
			,array(
				 'name' => 'Mike'
				,'done' => 24 
				,'time' => '2017-01-05 09:36:52' 
			)
			,array(
				  'name' => 'Polo'
				 ,'done' => 32
			)
			,array(
				 '%s'
				,'%d'
				,'%s'
			)
			,array(
				  '%s'
				 ,'%d'
			)
		);
	echo "update ret: $ret" . ", sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
}

function test_delete()
{
	$em = new EasyMysql('root', 'password', 'table', '127.0.0.1');
	$ret = $em->delete(
			 'task'
			,array(
				 'id' => 4
			)
			,array(
				 '%d'
			)
		);
	echo "delete ret: $ret" . ", sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";

	$ret = $em->delete(
			 'task'
			,array(
				  'done' => 25
				 ,'name' => 'Jackey'
			)
			,array(
				  '%d'
				 ,'%s'
			)
		);
	echo "delete ret: $ret" . ", sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
}

function test_select()
{
	$em = new EasyMysql('root', 'password', 'table', '127.0.0.1');
	$sql = "select * from task";
	$em->query($sql);
	$results = $em->get_results();
	echo "select sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
	var_dump($results);
}


function test_get_row()
{
	$em = new EasyMysql('root', 'password', 'table', '127.0.0.1');
	$sql = "select * from task";
	$result = $em->get_row($sql);
	echo "get row sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
	var_dump($result);
}

function test_desc_table()
{
	$em = new EasyMysql('root', 'tango9896', 'shangping_test', '127.0.0.1');
	$sql = "desc task";
	$em->query($sql);
	$results = $em->get_results();
	echo "get row sql error: " . $em->_sql_errno . ", last error: " . $em->_last_error . "\n";
	var_dump($results);
}

//test_insert();
//test_update();
//test_delete();
//test_select();
//test_get_row();
test_desc_table();

