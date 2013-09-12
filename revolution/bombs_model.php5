<?php 

class BombsModel {

	function get_number_bombs($player_name, $bomb_type) {
		$conn = db_connect();
		$query = "select SUM(number) as sum from player_items where player_name='$player_name' and item_type='$bomb_type'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return -1; // no entries, no bombs
		$row = $result->fetch_object();		
		return $row->sum;
	}

	function get_number_bombs_at_location($player_name, $bomb_type, $location) {
		$conn = db_connect();
		$query = "select number from player_items where player_name='$player_name' and item_type='$bomb_type' and status='$location'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return -1; // no entries, no bombs
		$row = $result->fetch_object();		
		return $row->number;
	}


	function get_current_bombs_and_forts_in_production($player_name) {
	  $conn = db_connect();
		$query = "SELECT sum(number) as sum FROM `player_build` WHERE player_name='$player_name' 
			and build_type in ('bomb', 'fort') and status='building'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->sum;
	}

	function get_bomb_details($bomb_type) {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='$bomb_type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		
		$details["mineral"] = $row->mineral;
		$details["organic"] = $row->organic;
		$details["ticks"] = $row->ticks;
		$details["description"] = $row->description;
		
		return $details;
	}

	function get_total_ticks_of_bomb($bomb_type) {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='$bomb_type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	

		return $row->ticks;
	}

	function provision_bombs($player_name, $bomb_type, $number, $ticks, $tick_started) {
  	
	  $conn = db_connect();
		$query = "insert into player_build values 
			('$player_name', 'bomb', '$bomb_type', $number, $tick_started, $ticks, 'building')";
		$result = $conn->query($query);
	}

	function add_bombs_to_player($player_name, $type, $number) {
		if (strcmp($type, "Trap") == 0 || strcmp($type, "Psychological Trap") == 0) $initial_status = "active";
		else $initial_status = "home";
		$number_existing = $this->get_number_bombs_at_location($player_name, $type, $initial_status );
		
		// Changed flag to -1 as 0 is a valid value
		if ($number_existing == -1) $this->insert_new_bombs($player_name, $type, $number, $initial_status );
		else {
			$new_number = $number_existing + $number;
			$this->update_bombs($player_name, $type, $new_number, $initial_status );
		}
		
	}


	function insert_new_bombs($player_name, $type, $number, $initial_status ) {
	  $conn = db_connect();
		$query = "insert into player_items values ('$player_name', '$type', $number, '$initial_status ')";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function update_bombs($player_name, $type, $number, $fleet) {
	  $conn = db_connect();
		$query = "update player_items set number=$number where player_name='$player_name' and item_type='$type' and status='$fleet'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function get_bomb_locations($player_name, $type) {
		$locations = array();
		$conn = db_connect();
		$query = "select * from player_items where player_name='$player_name' and item_type='$type'";
		$result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$loc = trim($row->status);
			$locations["$loc"] = $row->number;
		}
		return $locations;
	}
	
	function remove_row($player_name, $type, $from) {
	  $conn = db_connect();
		$query = "delete from player_items where player_name='$player_name' 
				and item_type = '$type' and status='$from'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem removing items");
			return false;
		}
		return true;
	}
	
	function subtract($player_name, $type, $from, $number_subtracted) {
	  $conn = db_connect();
		$query = "update player_items set number=number-$number_subtracted 
				where player_name='$player_name' 
				and item_type= '$type' and status='$from'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem subtracting items");
			return false;
		}
		return true;
		
	}
	
	function add($player_name, $type, $to, $number_added) {
	  $conn = db_connect();
		$query = "update player_items set number=number+$number_added 
				where player_name='$player_name' 
				and item_type= '$type' and status='$to'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem adding items");
			return false;
		}
		return true;
		
	}
	
	function make_new_row($player_name, $type, $to, $number) {
	  $conn = db_connect();
		$query = "insert into player_items values ('$player_name', '$type', $number, '$to')";
		$result = $conn->query($query);
		if (!$result) {
			show_error("Problem making new row of items");
			return false;
		}
		return true;
		
		
	}
	
	
}

?>