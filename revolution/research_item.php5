<?php
	require_once('db_fns.php5'); 

	class ResearchItem {
		var $name;
		var $mineral;
		var $organic;
		var $ticks;
		var $type;
		var $pre1;
		var $pre2;
		var $pre3;
		var $level;
		var $lane;
		var $size;
		var $description;
		
		function db_fill($name) {
		  $conn = db_connect();
			$query = "select * from research_items where name='$name'";	
			$result = $conn->query($query);
			$row = $result->fetch_object(); // Should be only one row
			$this->populate($row);
		}	
	
		function populate($row) {
			$this->name = $row->name;
			$this->mineral = $row->mineral;
			$this->organic = $row->organic;
			$this->ticks = $row->ticks;
			$this->type = $row->type;
			$this->pre1 = $row->pre1 ;
			$this->pre2 = $row->pre2;
			$this->pre3 = $row->pre3;
			$this->level = $row->level;
			$this->lane = $row->lane;
			$this->size = $row->size;
			$this->color_text = $row->color_text;
		}
		
		function get_max_level_of_research($type) {
		  $conn = db_connect();
			$query = "select max(level) as max_level from research_items where type='$type'";	
			$result = $conn->query($query);
			$row = $result->fetch_object();
			
			return $row->max_level;
		}
		
		function get_dependent_developments($research_item) {
			$dependent_developments = array();
			
		  $conn = db_connect();
			$query = "select * from development_items where dependent_research='$research_item'";	
			$result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$dependent_developments[$count] = $row->name;
			}
			return $dependent_developments;
		}

		function get_ancestors($research_item) {
			$ancestors = array();
			
		  $conn = db_connect();
			$query = "select * from research_items where name='$research_item'";	
			$result = $conn->query($query);
			$row = $result->fetch_object();
			if (strlen($row->pre1) > 0) $ancestors[0] = $row->pre1;
			if (strlen($row->pre2) > 0) $ancestors[1] = $row->pre2;
			if (strlen($row->pre3) > 0) $ancestors[2] = $row->pre3;
			
			return $ancestors;
		}

		function get_dependents($research_item) {
			$dependents = array();
		  $conn = db_connect();
			$query = "select * from research_items where pre1='$research_item' or pre2='$research_item' or pre3='$research_item'";	
			$result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$dependents[$count] = $row->name;
			}
			return $dependents;
		}
	}
?>