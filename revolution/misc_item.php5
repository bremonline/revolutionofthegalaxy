<?php
	require_once('db_fns.php5'); 

class MiscItem {
	var $name;
	var $development_item;
	var $mineral;
	var $organic;
	var $energy;
	var $ticks;
	var $description;
	
	function db_fill($name) {
	  $conn = db_connect();
		$query = "select * from misc_items where name='$name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object(); // Should be only one row
		$this->populate($row);
	}	

	function populate($row) {
		$this->name = $row->name;
		$this->development_item = $row->development_item;
		$this->mineral = $row->mineral;
		$this->organic = $row->organic;
		$this->energy = $row->energy;
		$this->ticks = $row->ticks;
		$this->description = $row->description;
	}
} 

?>