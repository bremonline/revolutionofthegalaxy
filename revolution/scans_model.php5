<?php 
	require_once('news_model.php5'); 

class ScansModel {
	var $scan_text;

	function get_scan_costs_and_ticks() {
		$conn = db_connect();
		$query = "select * from scan_items";
		$result = $conn->query($query);

		$scan_costs = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_costs["$row->subtype"]["mineral"] = $row->mineral;
			$scan_costs["$row->subtype"]["energy"] = $row->energy;
			$scan_costs["$row->subtype"]["ticks"] = $row->ticks;
		}	  
		 
		return $scan_costs;
	}
	
	function provision_scans($scan_type, $number, $ticks, $tick_started){
  	$player_name=$_SESSION['player_name'];
  	
	  $conn = db_connect();
		$query = "insert into player_build values 
			('$player_name', 'scan', '$scan_type', $number, $tick_started, $ticks, 'building')";
		$result = $conn->query($query);
	}

	function get_total_ticks_of_scan($scan_type) {
	  $conn = db_connect();
		$query = "select ticks from scan_items where subtype='$scan_type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->ticks;		
	}

	function add_scans_to_player($player_name, $scan_type, $number) {
		$number_existing = $this->get_number_of_scans_for_player($player_name, $scan_type);
		
		// Changed flag to -1 as 0 is a valid value
		if ($number_existing == -1) $this->insert_new_scans($player_name, $scan_type, $number);
		else {
			$new_number = $number_existing + $number;
			$this->update_scans($player_name, $scan_type, $new_number);
		}
		
	}

	function insert_new_scans($player_name, $scan_type, $number) {
	  $conn = db_connect();
		$query = "insert into player_scans values ('$player_name', '$scan_type', $number)";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function update_scans($player_name, $scan_type, $number) {
	  $conn = db_connect();
		$query = "update player_scans set number=$number where 
			player_name='$player_name' and scan_type='$scan_type'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function get_number_of_scans_for_player($player_name, $scan_type) {
	  $conn = db_connect();
		$query = "select number from player_scans where 
			player_name='$player_name' and scan_type='$scan_type'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return -1;
		
		$row = $result->fetch_object();
		return $row->number;
	}

	function get_all_research($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build 
			where player_name='$player_name' 
			  and build_type='research'
			  and status='completed'
			";
		$result = $conn->query($query);
		if ($result->num_rows==0) return "No Research Done";
		
		$scan_text = "";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_text = $scan_text . "{$row->build_item}<br />";
		}	  
		return $scan_text;
		
	}
	
	function get_all_developments($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build 
			where player_name='$player_name' 
			  and build_type='development'
			  and status='completed'
			";
		$result = $conn->query($query);
		if ($result->num_rows==0) return "No Developments Done";
		
		$scan_text = "";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_text = $scan_text . "{$row->build_item}<br />";
		}	  
		return $scan_text;
		
	}

	function get_current_research($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build 
			where player_name='$player_name' 
			  and build_type='research'
			  and status='researching'
			";
		$result = $conn->query($query);
		if ($result->num_rows==0) return "None";
		
		$row = $result->fetch_object();

		$research_item = $row->build_item;
		$ticks_remaining = $row->ticks_remaining;
		
		$query = "select ticks from research_items where name='$research_item'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ticks_completed = $row->ticks - $ticks_remaining;
		
		$scan_text = "{$research_item} ($ticks_completed/$row->ticks)<br />";

		return $scan_text;
		
	}
	
	function get_current_developments($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build 
			where player_name='$player_name' 
			  and build_type='development'
			  and status='developing'
			";
		$result = $conn->query($query);
		if ($result->num_rows==0) return "None";
		
		$row = $result->fetch_object();

		$development_item = $row->build_item;
		$ticks_remaining = $row->ticks_remaining;
		
		$query = "select ticks from development_items where name='$development_item'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ticks_completed = $row->ticks - $ticks_remaining;


		$scan_text = "{$development_item} ($ticks_completed/$row->ticks)<br />";
		return $scan_text;
		
	}
	
	function store_scan($player_name, $target_name, $scan_type, $text) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
	  $conn = db_connect();
		$query = "insert into scan_results values (0, '$player_name', '$target_name', '$scan_type', $current_tick, NOW(), '$text')";
		$result = $conn->query($query);
	}
	
	function get_scan_values() {
		$scan_values = array();
		$conn = db_connect();	
		$query = "select * from scan_items";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_values["$row->subtype"] = ($row->mineral + $row->energy) * 0.10;
		}	  
		return $scan_values;
	}

	function get_scans_for_development($development_item) {
		$conn = db_connect();	
		$query = "select * from scan_items where dependent_development='$development_item'";
	  $result = $conn->query($query);
	  $scan_list = array();
	  for ($count=0;$row = $result->fetch_object(); $count++) {
	  	$scan_list[$count] = $row->name;
	  }
		return $scan_list;
	}
	
	function check_monitor($player_name, $target_name, $type, $current_tick) {
		$conn = db_connect();	
		$query = "select * from monitor
			WHERE player_name = '$player_name'
			  AND target_name = '$target_name'
			  AND type = '$type'
			  AND start_tick <= $current_tick 
			  AND until_tick > $current_tick";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}
	
	function add_monitor($player_name, $target_name, $type, $start_tick, $until_tick) {
		if ($this->check_monitor($player_name, $target_name, $type, $start_tick)) 
			$this->update_monitor($player_name, $target_name, $type, $start_tick, $until_tick);
		else
			$this->insert_new_monitor($player_name, $target_name, $type, $start_tick, $until_tick);
	}

	function update_monitor($player_name, $target_name, $type, $start_tick, $until_tick) {
		$conn = db_connect();	
		$query = "update monitor set until_tick=$until_tick
			WHERE player_name = '$player_name'
			  AND target_name = '$target_name'
			  AND type = '$type'
			  AND until_tick > $start_tick
		";
		$result = $conn->query($query);
				
	}
	
	function insert_new_monitor($player_name, $target_name, $type, $start_tick, $until_tick) {
		$conn = db_connect();	
		$query = "INSERT INTO monitor VALUES ('$player_name', '$target_name', '$type', $start_tick, $until_tick)";
		$result = $conn->query($query);
	}
	
	function get_all_active_monitors_by_type($player_name, $type, $current_tick) {
		$monitors = array();
		$conn = db_connect();	
		$query = "select * from monitor
			WHERE player_name = '$player_name'
			  AND type = '$type'
			  AND start_tick <= $current_tick 
			  AND until_tick > $current_tick";
		$result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$monitors[$count]["player_name"] = $row->player_name;
			$monitors[$count]["target_name"] = $row->target_name;
			$monitors[$count]["type"] = $row->type;
			$monitors[$count]["start_tick"] = $row->start_tick;
			$monitors[$count]["until_tick"] = $row->until_tick;
		}
		
		return $monitors;
	}

	function get_all_active_launch_monitors_by_target($target_name, $current_tick) {
		$watchers = array();
		$conn = db_connect();	
		$query = "select * from monitor
			WHERE target_name = '$target_name'
			  AND type = 'launch'
			  AND start_tick <= $current_tick 
			  AND until_tick > $current_tick";
		$result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$watchers[$count]["player_name"] = $row->player_name;
			$watchers[$count]["target_name"] = $row->target_name;
			$watchers[$count]["type"] = $row->type;
			$watchers[$count]["start_tick"] = $row->start_tick;
			$watchers[$count]["until_tick"] = $row->until_tick;
		}
		
		return $watchers;
	}
	
	
}
?>