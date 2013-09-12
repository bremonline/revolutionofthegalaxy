<?php
	require_once('db_fns.php5'); 

class HelpModel { 
	function get_all_names_from_table($type) { 
		$item_names = array(); 
		$conn = db_connect(); 
		$query = "select name from $type"; 
		$result = $conn->query($query); 
	
		for ($count=0;$row = $result->fetch_object();$count++) { 
			$item_names[$count]["name"] = $row->name;
		}	
		
		return $item_names; 
	}

	function get_all_names_from_misc_items_table($type) { 
		$item_names = array(); 
		$conn = db_connect(); 
		$query = "select name from misc_items where type='$type' "; 
		$result = $conn->query($query); 
	
		for ($count=0;$row = $result->fetch_object();$count++) { 
			$item_names[$count]["name"] = $row->name;
		}	
		
		return $item_names; 
	}


	function get_description_matrix($category, $type) { 
		$description_names = array(); 
		$conn = db_connect(); 
		$query = "select name from description where category='$category' and type='$type' "; 
		$result = $conn->query($query); 
	
		for ($count=0;$row = $result->fetch_object();$count++) { 
			$description_names["$row->name"]["$row->type"] = true;
		}	
		
		return $description_names; 
	}

	function get_basic_concepts() { 
		$description_names = array(); 
		$conn = db_connect(); 
		$query = "select name, text from description where category='concept' and type='basic' order by ordinality "; 
		$result = $conn->query($query); 
	
		for ($count=0;$row = $result->fetch_object();$count++) { 
			$description_names[$count]["name"] = $row->name;
			$description_names[$count]["text"] = $row->text;
		}	
		
		return $description_names; 
	}

	function get_faq_information() { 
		$faq_names = array(); 
		$conn = db_connect(); 
		$query = "select name, text from description where category='faq' and type='question' order by ordinality "; 
		$result = $conn->query($query); 
	
		for ($count=0;$row = $result->fetch_object();$count++) { 
			$faq_names[$count]["id"] = $row->name;
			$faq_names[$count]["question"] = $row->text;
			$faq_names[$count]["answer"] = "UNKNOWN";
		}	
		
		return $faq_names; 
	}
	
	function insert_new_description($player_name, $name, $category) {
		$conn = db_connect(); 
		$query = "select max(ordinality) as max from description where category='$category' and type='basic'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		$max = $row->max;
		$newmax = $max + 1.0;
		
		$query = "insert into description values ('$name', '$category', 'basic', '$player_name', 'No Description Available', NOW(), $newmax)"; 
		$result = $conn->query($query); 
	}	
	
	function get_max_ordinality($category, $type) {
		$conn = db_connect(); 
		$query = "select max(ordinality) as max from description where category='$category' and type='$type'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		$max = $row->max;
		return $max;		
	}
	
}
?>