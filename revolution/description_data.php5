<?php
	require_once('db_fns.php5'); 

class DescriptionData {
	var $name;
	var $category; // research, development, scan, creature, pulse, bomb
	var $type; // basic, color, help, evohelp, advanced
	var $author;
	var $text;
	
	function does_description_exist($name, $type, $category) {
	  $conn = db_connect();
		$query = "select * from description where name='$name' and type='$type' and category='$category'"; 
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}
	
	function db_fill($name, $type, $category) {
	  $conn = db_connect();
		$query = "select * from description where name='$name' and type='$type' and category='$category'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$this->name = $row->name;
		$this->category = $row->category;
		$this->type = $row->type;
		$this->author = $row->author;
		$this->text = $row->text;
	}
	
	
}

?>