<?php

define('FORMAT_ERROR', -1001);
define('CONNECT_ERROR', -1002);
define('SQL_ERROR', -1003);

/*
class EasyDB
{
}
*/

class EasyMysql
{
	private $_host;
	private $_port;
	private $_user;
	private $_passwd;
	private $_db_name;

	private $_mysqli;
	private $_query_result;

	public $_last_errno = 0;
	public $_sql_errno = 0;
	public $_last_error = '';

	private $_has_connected = false;

	private $_insert_id;
	private $_result;

	private $_table_charset;

    public function __construct($user, $passwd, $db_name, $host, $port = 3306)
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_user = $user;
		$this->_passwd = $passwd;
		$this->_db_name = $db_name;

		$this->connect();
	}

    private function connect()
	{
		$this->_mysqli = mysqli_init();
		$client_flags = null;

		$ret = $this->_mysqli->real_connect( 
								 $this->_host
								,$this->_user
								,$this->_passwd
								,$this->_db_name
								,$this->_port
								,null
								,$client_flags
				);

		if($ret == false)
		{
			$this->_last_errno = $this->_mysqli->connect_errno();
			$this->_last_error = $this->_mysqli->connect_error();
			$this->_has_connected = false;
			return false;
		}

		$this->_has_connected = true;
		return true;
	}

    public function prepare()
	{
		// not used yet
	}

    public function insert($table, $data, $format)
	{
		$this->_insert_id = false;

		if(count($data) != count($format)){
			$this->_last_errno = FORMAT_ERROR;
			return false;
		}

		$values = $this->process_insert_fields($data, $format);
		if($values == false){
			return false;
		}
	
		$columns = array();
		foreach($data as $col => $val)
		{
			$columns[] = $col;
		}

		$sql = 'INSERT INTO ' . $table . ' ( ' . implode(', ', $columns) . ' ) VALUES ( ' . implode(', ', $values) . ' )';

		$ret = $this->query($sql);
		if($ret == true && $this->_query_result == true){
			$this->_insert_id = $this->_mysqli->insert_id;
		}

		return $this->_insert_id;
		
	}

    public function update($table, $data, $where, $format, $where_format)
	{
		if(count($data) != count($format) || count($where) != count($where_format)){
			$this->_last_errno = FORMAT_ERROR;
			return false;
		}

		$values = $this->process_update_fields($data, $format);
		if($values == false){
			return false;
		}

		$conditions = $this->process_update_fields($where, $where_format);
		if($conditions == false){
			return false;
		}

		$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $values) . ' WHERE ' . implode(' and ', $conditions);

		return $this->query($sql);
	}

    public function delete($table, $where, $where_format)
	{
		if( count($where) != count($where_format)){
			$this->_last_errno = FORMAT_ERROR;
			return false;
		}

		$conditions = $this->process_update_fields($where, $where_format);
		if($conditions == false){
			return false;
		}

		$sql = 'DELETE FROM ' . $table . ' WHERE ' . implode(' and ', $conditions);

		return $this->query($sql);
	}

    public function query($sql)
	{
		if($this->_has_connected != true){
    		if($this->connect() != true){ // try connect
				$this->_last_errno = CONNECT_ERROR;
				return false;
			}
		}

		$this->_query_result = $this->_mysqli->query($sql);
		if($this->_query_result === false){ // save error
			$this->_last_errno = SQL_ERROR;
			$this->_sql_errno = $this->_mysqli->errno;
			$this->_last_error = $this->_mysqli->error;
		}

		return $this->_query_result;
	}

    public function get_row($sql)
	{
    	$this->query($sql);
		if($this->_query_result == false){
			return false;
		}

		//results rows: $this->_query_result->num_rows
		$this->_query_result->data_seek(0);
		$result = $this->_query_result->fetch_object();
		$this->_query_result->free();
		return $result;
	}

    public function get_results()
	{
		if($this->_query_result == false){
			return false;
		}

		$results = array();

		//results rows: $this->_query_result->num_rows
		$this->_query_result->data_seek(0);
		while ($row = $this->_query_result->fetch_object()) {
			$results[] = $row;
		}
		$this->_query_result->free();
		return $results;
	}

	// $format: '%s', '%d', '%f'
	private function process_insert_fields($data, $format)
	{
		$values = array();
		$i = 0;
		foreach($data as $col => $val)
		{
			if($format[$i] == '%s'){
				$values[] = '"' . $val . '"';
			}else if($format[$i] == '%d'){
				$values[] = $val;
			}else if($format[$i] == '%f'){
				$values[] = $val;
			}else{
				// unknown format
				$this->_last_errno = FORMAT_ERROR;
				return false;
			}
			$i++;
		}

		return $values;
	}

	// $format: '%s', '%d', '%f'
	private function process_update_fields($data, $format)
	{
		$values = array();
		$i = 0;
		foreach($data as $col => $val)
		{
			if($format[$i] == '%s'){
				$values[] = $col . ' = "' . $val . '"';
			}else if($format[$i] == '%d'){
				$values[] = $col . ' = ' . $val;
			}else if($format[$i] == '%f'){
				$values[] = $col . ' = ' . $val;
			}else{
				// unknown format
				$this->_last_errno = FORMAT_ERROR;
				return false;
			}
			$i++;
		}

		return $values;
	}

}

