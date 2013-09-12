<?php 

class PulsesModel {

	function get_number_pulses($player_name, $type) {
		$conn = db_connect();
		$query = "select * from player_items where player_name='$player_name' and item_type='$type'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return -1; // no entries, no pulses
		$row = $result->fetch_object();		
		return $row->number;
	}

	function has_pulse_been_fired($player_name, $current_tick) {
		$conn = db_connect();
		$query = "select * from pulse_use where player_name='$player_name' and tick >= $current_tick ";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true; // A pulse was found on the use table
		else return false;
	}

	function is_shield_active($player_name, $shield_type, $current_tick) {
		$conn = db_connect();
		$query = "select * from pulse_use where player_name='$player_name' and pulse_type='$shield_type' and tick >= $current_tick ";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true; // A shield was found on the use table
		else return false;
	}
	
	function active_pulse($player_name, $current_tick) {
		$ret = array();
		$conn = db_connect();
		$query = "select * from pulse_use where player_name='$player_name' and tick >= $current_tick ";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ret[0] = $row->pulse_type;
		$ret[1] = $row->tick;
		return $ret;
	}
	
	function set_pulse_use($player_name, $pulse_type, $current_tick) {
		$conn = db_connect();
		$query = "insert into pulse_use values ('$player_name', '$pulse_type', $current_tick)";
		$result = $conn->query($query);
	}

	function get_pulse_details($type) {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='$type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		
		$details["mineral"] = $row->mineral;
		$details["organic"] = $row->organic;
		$details["energy"] = $row->energy;
		$details["ticks"] = $row->ticks;
		$details["description"] = $row->description;
		
		return $details;
	}


	function get_total_ticks_of_pulse($type) {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='$type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	

		return $row->ticks;
	}

	
	function provision_pulses($player_name, $type, $number, $ticks, $tick_started) {
  	
	  $conn = db_connect();
		$query = "insert into player_build values 
			('$player_name', 'pulse', '$type', $number, $tick_started, $ticks, 'building')";
		$result = $conn->query($query);
	}

	
	function add_pulses_to_player($player_name, $type, $number) {
		$number_existing = $this->get_number_pulses($player_name, $type);
		
		// Changed flag to -1 as 0 is a valid value
		if ($number_existing == -1) $this->insert_new_pulses($player_name, $type, $number);
		else {
			$new_number = $number_existing + $number;
			$this->update_pulses($player_name, $type, $new_number);
		}
		
	}

	function insert_new_pulses($player_name, $type, $number) {
	  $conn = db_connect();
		$query = "insert into player_items values ('$player_name', '$type', $number, 'active')";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function update_pulses($player_name, $type, $number) {
	  $conn = db_connect();
		$query = "update player_items set number=$number where player_name='$player_name' and item_type='$type'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function get_pulse_victims($player_name, $current_tick) {
		$pulsed_fleets = array();
	  $conn = db_connect();
		$query = "select * from player_orders where target_name='$player_name' and arrival_tick = $current_tick+1";
		$result = $conn->query($query);
		for ($count=0;$row = $result->fetch_object();$count++) {	
			$pulsed_fleets[$count] = "{$row->player_name}:{$row->fleet}";
		}
		
		return $pulsed_fleets;
	}
	
	function get_damage($ratio) {
		if ($ratio > 4.0) return 10;
		if ($ratio > 3.0) return 9;
		if ($ratio > 2.5) return 8;
		if ($ratio > 2.0) return 7;
		if ($ratio > 1.5) return 6;
		if ($ratio > 1.0) return 5;
		if ($ratio > 0.85) return 4;
		if ($ratio > 0.75) return 3;
		if ($ratio > 0.66) return 2;
		if ($ratio > 0.5) return 1;
		return 0;
	}

}

?>