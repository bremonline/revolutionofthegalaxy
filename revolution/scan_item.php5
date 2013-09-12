<?php
	require_once('db_fns.php5'); 

class ScanItem {
	var $name;
	var $type;
	var $subtype;
	var $dependent_development;
	var $mineral;
	var $energy;
	var $ticks;
	var $description;
	
	function db_fill($name) {
	  $conn = db_connect();
		$query = "select * from scan_items where name='$name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object(); // Should be only one row
		$this->populate($row);
	}	

	function populate($row) {
		$this->name = $row->name;
		$this->type = $row->type;
		$this->subtype = $row->subtype;
		$this->dependent_development = $row->dependent_development;
		$this->mineral = $row->mineral;
		$this->energy = $row->energy;
		$this->ticks = $row->ticks;
		$this->description = $row->description;
	}
} 

?>