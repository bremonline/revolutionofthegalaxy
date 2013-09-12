<?php
	require_once('db_fns.php5'); 

class DevelopmentItem {
	var $name;
	var $dependent_research;
	var $ticks;
	var $type;
	var $proficiency;
	var $description;
	
		function db_fill($name) {
		  $conn = db_connect();
			$query = "select * from development_items where name='$name'";	
			$result = $conn->query($query);
			$row = $result->fetch_object(); // Should be only one row
			$this->populate($row);
		}	
	
		function populate($row) {
			$this->name = $row->name;
			$this->dependent_research = $row->dependent_research;
			$this->ticks = $row->ticks;
			$this->type = $row->type;
			$this->proficiency = $row->proficiency ;
			$this->description = $row->description;
		}
	
}