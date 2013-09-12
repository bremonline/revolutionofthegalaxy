<?php 
	require_once('development_model.php5');

class FortsModel {

	function get_number_forts($player_name) {
		$conn = db_connect();
		$query = "select * from player_items where player_name='$player_name' and item_type='fort'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return -1; // no entries, no forts
		$row = $result->fetch_object();		
		return $row->number;
	}

	function get_fort_details() {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='Fort'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		
		$details["mineral"] = $row->mineral;
		$details["organic"] = $row->organic;
		$details["ticks"] = $row->ticks;
		
		return $details;
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

	function get_total_ticks_of_fort() {
		$details = array();
		$conn = db_connect();
		$query = "select * from misc_items where name='Fort'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	

		return $row->ticks;
	}
	
	function get_fort_technologies() {
		$techs = array();
		$conn = db_connect();
		$query = "select * from fort_technologies";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$techs["$row->development_name"]["att"] = $row->attack_bonus;
			$techs["$row->development_name"]["def"] = $row->defense_bonus;
			$techs["$row->development_name"]["battle"] = $row->battle_resistance;
			$techs["$row->development_name"]["bomb"] = $row->bomb_resistance;
		}
		return $techs;
		
	}
	
	function get_fort_stats($player_name) {
		$stats = array();
		$dm = new DevelopmentModel();
		$techs = $this->get_fort_technologies();
		$stats["att"] = 0;
		$stats["def"] = 0;
		$stats["survive"] = 0;
		foreach ($techs as $tech_name => $details) {
			if ($dm->does_player_know_development($player_name, $tech_name) ) {
				$stats["att"] += $details["att"];
				$stats["def"] += $details["def"];
				$stats["battle"] += $details["battle"]; if ($stats["battle"] > 100) $stats["battle"] = 100;
				$stats["bomb"] += $details["bomb"]; if ($stats["bomb"] > 100) $stats["bomb"] = 100;
			}
		}
		return $stats;
	}

	function provision_forts($player_name, $number, $ticks, $tick_started) {
  	
	  $conn = db_connect();
		$query = "insert into player_build values 
			('$player_name', 'fort', 'Fort', $number, $tick_started, $ticks, 'building')";
		$result = $conn->query($query);
	}
	
	function add_forts_to_player($player_name, $number) {
		$number_existing = $this->get_number_forts($player_name);
		
		// Changed flag to -1 as 0 is a valid value
		if ($number_existing == -1) $this->insert_new_forts($player_name, $number);
		else {
			$new_number = $number_existing + $number;
			$this->update_forts($player_name, $new_number);
		}
		
	}


	function insert_new_forts($player_name, $number) {
	  $conn = db_connect();
		$query = "insert into player_items values ('$player_name', 'fort', $number, 'active')";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}


	function remove_all_forts($player_name) {
	  $conn = db_connect();
		$query = "delete from player_items where player_name='$player_name'	and item_type='fort'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function update_forts($player_name, $number) {
	  $conn = db_connect();
		$query = "update player_items set number=$number where player_name='$player_name' and item_type='fort'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

}

?>