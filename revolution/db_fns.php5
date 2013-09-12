<?php

global $conn;

function db_connect()
{
	global $conn;
	if ($conn) return $conn;
	else {
		$start_time = microtime();
		$conn = old_db_connect();
		$connect_time = microtime() - $start_time;
//		echo "(C:$connect_time)";
		return $conn;
	}
}

function single_connect() {
		$db = mysqli_init();
		$db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1);
		//Connect to the database. (host,username,password,database)
		@$db->real_connect("[host]", "[db_user]", "[db_password]", "[database]");
		// Report connection error.
		if (mysqli_connect_errno()) return false;
   	else return $db;
	
}

function old_db_connect()
{
  $result = new mysqli('[host]', '[db_user]', '[db_password]', '[database]'); 
	 if (!$result) echo "DATABASE CONNECT ERROR";
   else return $result;
}

function get_db_time() {
	$conn = db_connect();
	$query = "select NOW() as time";
	$result = $conn->query($query);
	$row = $result->fetch_object();
	return $row->time;
}

function get_db_unix_time() {
	$conn = db_connect();
	$query = "select UNIX_TIMESTAMP() as time";
	$result = $conn->query($query);
	$row = $result->fetch_object();
	return $row->time;
	
}
?>
