<?php 
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('game_model.php5'); 

class ResearchModel {
	
	
	function add_new_research($player_name, $research_item) {
		$gm = new GameModel();
		$tick_started = $gm->get_current_tick();
		$total_ticks = $this->get_research_time($research_item);

	  $conn = db_connect();
		$query = "insert into player_build values ('$player_name', 'research', '$research_item', 1, $tick_started, $total_ticks, 'researching')"; 
		$result = $conn->query($query);
		if (!$result) show_error("Error is query: $query");
	}

	function get_research_time($research_item) {
	  $conn = db_connect();
		$query = "select ticks from research_items where name='$research_item'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->ticks;
	}
	
	function get_cost($type, $research_item) {
	  $conn = db_connect();
		$query = "select $type as cost from research_items where name='$research_item'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->cost;
	}

	function get_currently_researching($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and status='researching'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;
		$row = $result->fetch_object();
		return $row->build_item;		
	}
	
	function get_current_research_details($player_name) {
	  $conn = db_connect();
		$query = "select ri.name, pb.ticks_remaining, ri.ticks from player_build pb, research_items ri where player_name='$player_name' and ri.name=pb.build_item 
			and build_type='research'
			and status='researching'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;
		$row = $result->fetch_object();
		$retval = array('name' => $row->name, 'total_ticks'=> $row->ticks, 'ticks_remaining'=> $row->ticks_remaining);
		return $retval;		
		
	}
	
	function get_list_of_all_research() {
		$conn = db_connect();	
		$query = "select name, ticks from research_items order by name";
		$result = $conn->query($query);
			  
		$ist = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$list[$count]['item'] = $row->name;
			$list[$count]['ticks'] = $row->ticks;
		}	  
		return $list;
	}
	
	function is_researchable($player_name, $research_name) {

		// If anything is currently being researched, then nothing is researchable until it is done.
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and status='researching'";
		$result = $conn->query($query);
		if ($result->num_rows>0) return false;		

		$query = "select * from research_items where name='$research_name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$pre1=$row->pre1;
		$pre2=$row->pre2;
		$pre3=$row->pre3;
		
		$researchable = true;
		if ($pre1 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre1' and status='completed'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		if ($pre2 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre2'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		if ($pre3 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre3'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		return $researchable;	
	}
	
	function is_researched($player_name, $research_name) {

	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and build_item='$research_name' and status='completed'";	
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;	
	}

}
?>