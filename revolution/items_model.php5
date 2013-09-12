<?php

class ItemsModel {
	function get_items_values() {
		$item_values = array();
		$conn = db_connect();	
		$query = "select * from misc_items";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			// Fix to deal with fort not capitalized in dataset
			if ($row->name == 'Fort') $item_name = 'fort';  // Fix to deal with fort not capitalized in dataset
			else $item_name = $row->name;
			$item_values["$item_name"] = ($row->mineral + $row->organic + $row->energy) * 0.10;
		}	  
		return $item_values;
	}

	function get_items_for_development($development_item) {
		$conn = db_connect();	
		$query = "select * from misc_items where development_item='$development_item'";
	  $result = $conn->query($query);
	  $item_list = array();
	  for ($count=0;$row = $result->fetch_object(); $count++) {
	  	$item_list[$count] = $row->name;
	  }
		return $item_list;
		
	}
}
?>