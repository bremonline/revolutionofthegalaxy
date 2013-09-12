<?php

class CreaturesModel {
	function creature_available($creature_name) {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from creature_items ci, player_build pb 
			where ci.development_item = pb.build_item
			and pb.build_type='development'
			and pb.player_name = '$player_name'
			and pb.status='completed'
			and ci.name='$creature_name'";
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;
	}

function get_number_of_creatures_for_player($player_name) {
	  $conn = db_connect();
		$query = "select sum(number) as sum from player_creatures where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return 0;
		
		$row = $result->fetch_object();
		$sum = $row->sum;
		if (strlen($sum) == 0) $sum=0;
		return $sum;
	}

	function get_creature_list_for_player($player_name) {
	  $conn = db_connect();
		$query = "select creature, sum(number) as number from player_creatures where player_name='$player_name' group by creature";
		$result = $conn->query($query);

		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_list["$row->creature"] = $row->number;
		}	  
		return $creature_list;
	}
		
	function get_number_of_creatures($player_name, $creature) {
	  $conn = db_connect();
		$query = "select sum(number) as sum from player_creatures where 
			player_name='$player_name' and creature='$creature'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return 0;
		
		$row = $result->fetch_object();
		$sum = $row->sum;
		if (strlen($sum) == 0) $sum=0;
		return $sum;
	}


	function get_number_of_creatures_in_fleet($player_name, $creature, $fleet) {
	  $conn = db_connect();
		$query = "select number from player_creatures where 
			player_name='$player_name' and creature='$creature' and fleet_location='$fleet'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return 0;
		
		$row = $result->fetch_object();
		return $row->number;
	}

	function get_total_ticks_of_creature($creature) {
	  $conn = db_connect();
		$query = "select ticks from creature_items where name='$creature'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->ticks;		
	}

	function add_creatures_to_player($player_name, $creature, $number, $fleet) {
		$number_existing = $this->get_number_of_creatures_in_fleet($player_name, $creature, $fleet);
		
		if ($number_existing == 0) $this->insert_new_creatures($player_name, $creature, $number, $fleet);
		else {
			$new_number = $number_existing + $number;
			$this->update_creatures($player_name, $creature, $new_number, $fleet);
		}
	}
	
	function insert_new_creatures($player_name, $creature, $number, $fleet) {
	  $conn = db_connect();
		$query = "insert into player_creatures values ('$player_name', '$creature', $number, '$fleet')";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function remove_all_creatures($player_name, $creature, $fleet) {
	  $conn = db_connect();
		$query = "delete from player_creatures where player_name = '$player_name' and fleet_location='$fleet' and creature='$creature'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}

	function update_creatures($player_name, $creature, $number, $fleet) {
	  $conn = db_connect();
		$query = "update player_creatures set number=$number where 
			player_name='$player_name' and creature='$creature' and fleet_location='$fleet'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
	}
	
	function get_current_creatures_in_production($player_name) {
	  $conn = db_connect();
		$query = "SELECT sum(number) as sum FROM `player_build` WHERE player_name='$player_name' 
			and build_type='creature' and status='building'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->sum;
	}

	function get_attack_value($creature_name) {
	  $conn = db_connect();
		$query = "SELECT attack from creature_items WHERE name='$creature_name'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->attack;
	}	

	function get_defense_value($creature_name) {
	  $conn = db_connect();
		$query = "SELECT defense from creature_items WHERE name='$creature_name'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->defense;
	}	

	function get_weight_value($creature_name) {
	  $conn = db_connect();
		$query = "SELECT weight from creature_items WHERE name='$creature_name'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->weight;
	}	

	function get_focus_value($creature_name) {
	  $conn = db_connect();
		$query = "SELECT focus from creature_items WHERE name='$creature_name'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return $row->focus;
	}	

	function get_launch_cost($creature_name) {
	  $conn = db_connect();
		$query = "SELECT mineral, organic from creature_items WHERE name='$creature_name'";
		$result = $conn->query($query);
		if (!$result) show_error($query);
		$row = $result->fetch_object();
		return ceil(($row->mineral + $row->organic) / 100);
	}	

	function get_creature_values() {
		$creature_values = array();
		$conn = db_connect();	
		$query = "select * from creature_items";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_values["$row->name"] = ($row->mineral + $row->organic) * 0.10;
		}	  
		return $creature_values;
	}
	
	function get_all_creatures_stats() {
		$creature_stats = array();
		$conn = db_connect();	
		$query = "select * from creature_items";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_stats["$row->name"]["att"] = $row->attack;
			$creature_stats["$row->name"]["def"] = $row->defense;
			$creature_stats["$row->name"]["foc"] = $row->focus;
			$creature_stats["$row->name"]["int"] = $row->intelligence;
			$creature_stats["$row->name"]["dis"] = $row->discipline;
		}	  

		return $creature_stats;
		
	}

	function get_all_creature_totals_for_player($player_name) {
		$creature_totals = array();
		$creature_totals["att"] = 0;
		$creature_totals["def"] = 0;
		$creature_totals["foc"] = 0;
		$creature_totals["int"] = 0;
		$creature_totals["dis"] = 0;
		$int_total = 0;
		$dis_total = 0;
		
		$conn = db_connect();	
		$query = "select * from player_creatures pc, creature_items ci 
			 where pc.player_name='$player_name'
			   and pc.creature=ci.name";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_totals["att"] += $row->number * $row->attack;	
			$creature_totals["def"] += $row->number * $row->defense;	
			$creature_totals["foc"] += $row->number * $row->focus;	
			$int_total += ceil (($row->number * $row->attack * $row->intelligence));	
			$dis_total += ceil (($row->number * $row->defense * $row->discipline));	
		}
		$creature_totals["int"] = ceil ($int_total/100);
		$creature_totals["dis"] = ceil ($dis_total/100);
		return $creature_totals;
	}

	function get_creature_totals_for_player_by_fleet($player_name, $fleet) {
		$creature_totals = array();
		$creature_totals["att"] = 0;
		$creature_totals["def"] = 0;
		$creature_totals["foc"] = 0;
		$creature_totals["int"] = 0;
		$creature_totals["dis"] = 0;
		$int_total = 0;
		$dis_total = 0;
		
		$conn = db_connect();	
		$query = "select * from player_creatures pc, creature_items ci 
			 where pc.player_name='$player_name'
			   and pc.creature=ci.name
			   and pc.fleet_location = '$fleet'";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_totals["att"] += $row->number * $row->attack;	
			$creature_totals["def"] += $row->number * $row->defense;	
			$creature_totals["foc"] += $row->number * $row->focus;	
			$int_total += ceil (($row->number * $row->attack * $row->intelligence));	
			$dis_total += ceil (($row->number * $row->defense * $row->discipline));	
		}
		$creature_totals["int"] = ceil ($int_total/100);
		$creature_totals["dis"] = ceil ($dis_total/100);
		return $creature_totals;
	}



	function damage_cybernetic_creatures($player_name, $fleet, $damage) {
		$conn = db_connect();	
		$query = "update player_creatures set number = CEIL(number * $damage) 
			 where player_name='$player_name' and fleet_location='$fleet' and creature in
			 ('Cyborg', 'Spider', 'Mantis', 'Megadon', 'Humvee', 'Tank', 'Crusher', 'Doomcrusher')";
	  $result = $conn->query($query);
	}

	function damage_hybrid_creatures($player_name, $fleet, $damage) {
		$conn = db_connect();	
		$query = "update player_creatures set number = CEIL(number * $damage) 
			 where player_name='$player_name' and fleet_location='$fleet' and creature in
			 ('Ogre', 'Troll', 'Giant', 'Demon', 'Cheetah', 'Panther', 'Tiger', 'Lion')";
	  $result = $conn->query($query);
	}

	function damage_genetic_creatures($player_name, $fleet, $damage) {
		$conn = db_connect();	
		$query = "update player_creatures set number = CEIL(number * $damage) 
			 where player_name='$player_name' and fleet_location='$fleet' and creature in
			 ('Imp', 'Wyrm', 'Wyvern', 'Dragon', 'Sprite', 'Dryad', 'Centaur', 'Unicorn')";
	  $result = $conn->query($query);
	}


	function get_creature_for_development($development_item) {
		$conn = db_connect();	
		$query = "select * from creature_items where development_item='$development_item'";
	  $result = $conn->query($query);
	  $row = $result->fetch_object();
		return $row->name;
	}
	
}



?>