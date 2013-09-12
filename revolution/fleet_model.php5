<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('game_model.php5');
	require_once('development_model.php5');
	require_once('news_model.php5');
	require_once('fleet_data.php5');
	require_once('email_helper.php5');


class FleetModel{

	function get_number_creatures($player_name, $creature_type, $fleet_name) {
	  $conn = db_connect();
		$query = "select number from player_creatures where player_name='$player_name' 
				and creature= '$creature_type' and fleet_location='$fleet_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		
		return $row->number;
		
	}

	function remove_row($player_name, $creature, $from) {
	  $conn = db_connect();
		$query = "delete from player_creatures where player_name='$player_name' 
				and creature= '$creature' and fleet_location='$from'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem removing creatures");
			return false;
		}
		return true;
	}
	
	function subtract($player_name, $creature, $from, $number_subtracted) {
	  $conn = db_connect();
		$query = "update player_creatures set number=number-$number_subtracted 
				where player_name='$player_name' 
				and creature= '$creature' and fleet_location='$from'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem subtracting creatures");
			return false;
		}
		return true;
		
	}
	
	function add($player_name, $creature, $to, $number_added) {
	  $conn = db_connect();
		$query = "update player_creatures set number=number+$number_added 
				where player_name='$player_name' 
				and creature= '$creature' and fleet_location='$to'";
		$result = $conn->query($query);
		if (!result) {
			show_error("Problem adding creatures");
			return false;
		}
		return true;
		
	}
	
	function make_new_row($player_name, $creature, $to, $number) {
	  $conn = db_connect();
		$query = "insert into player_creatures values ('$player_name', '$creature', $number, '$to')";
		$result = $conn->query($query);
		if (!$result) {
			show_error("Problem making new row of creatures");
			return false;
		}
		return true;
		
		
	}
	
	function get_weight_of_fleet($player_name, $fleet) {
	  $conn = db_connect();
		$query = "select max(ci.weight) as max from player_creatures pc, creature_items ci 
			WHERE pc.creature = ci.name
				AND pc.player_name='$player_name' 
				AND pc.fleet_location='$fleet'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->max;
	}
	
	function get_fleet_speed($player_name, $trip_type, $weight) {
		$antigrav = $this->get_antigrav_level($player_name);
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, 'Power Engines')) $speed=5;
		else if ($dm->does_player_know_development($player_name, 'Quantum Drives')) $speed=4;
		else if ($dm->does_player_know_development($player_name, 'Fusion Drives')) $speed=3;
		else if ($dm->does_player_know_development($player_name, 'Nuclear Drives')) $speed=2;
		else if ($dm->does_player_know_development($player_name, 'Ion Drives')) $speed=1; 
		else $speed=0;

		if (strcmp($trip_type, "galaxy") == 0) {
			$trip_time = 18 - $speed;
		} else if (strcmp($trip_type, "star") == 0) {
			$trip_time = 12 - $speed;
		} else if (strcmp($trip_type, "planet") == 0) {
			$trip_time = 10 - $speed;
		} else if (strcmp($trip_type, "continent") == 0) {
			$trip_time = 8 - $speed;			
		}
		if ($weight < $antigrav) $adjusted_weight = 0;
		else $adjusted_weight = $weight - $antigrav;
		$trip_time += $adjusted_weight;
		return $trip_time;
	}
	
	function get_antigrav_level($player_name) {
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, 'Advanced Antigravity')) return 4;
		else if ($dm->does_player_know_development($player_name, 'Improved Antigravity')) return 2;
		else if ($dm->does_player_know_development($player_name, 'Antigravity')) return 1;
		else return 0;
	}
	
	
	function set_launch_orders($player_name, $fleet, $weight, $galaxy, $star, $planet, $continent, $mission) {
		$email = new EmailHelper();
		$dm = new DevelopmentModel();
		$nm = new NewsModel();
		$pd = new PlayerData();
		$fleet = "fleet{$fleet}";
	  $conn = db_connect();
		$target_player = $pd->get_player_name_from_location($galaxy, $star, $planet, $continent);

		// Check to see that the fleet is not out, and that there are creatures in it
	  if ($target_player == "" ) {
	  	show_error ("Target Player does not exist.");
	  	return;
	  }
		
		// Check to see that the fleet is not out, and that there are creatures in it
	  if ($this->is_active_fleet_orders($player_name, $fleet) ) {
	  	show_error ("You cannot launch a fleet that is still on orders");
	  	return;
	  }
		
		// Get player and Target's data
		$pd->db_fill($player_name);
		$td = new PlayerData();
		$td->db_fill($target_player);
		
		$mission_time = $this->get_mission_time($mission);
		$mission_type = $this->get_mission_type($mission);

		if ($mission_type != 'attack' && $mission_type != 'defense') {
			show_error("Invalid Mission type " . $mission_type);
			return;			
		}

	
		// Check for vacation modes
		if ($pd->status != 'active') {
			show_error("You cannot launch when you are on vacation");
			return;
		}
		if ($td->status != 'active') {
			show_error("You cannot launch on a player who is on vacation");
			return;
		}
		
		// Check to see that te fleet is not empty
		if ($this->determine_fleet_launch_cost($player_name, $fleet) == 0) {
			show_error("You cannot launch an empty fleet");
			return;
		}
		// Check score difference, do not allow launches if target score is < 50% of player score
		if (strcmp($mission_type, "attack") == 0 && $pd->score * 0.50 > $td->score) {
			show_error("You cannot target a player with less than 50% of your score");
			return;
		}
	
		// Check for illegal launches
		if ($galaxy != $pd->galaxy && !$dm->does_player_know_development($player_name, "Intergalactic Vehicles") ) {
			// First tell launcher that he launched
			$subject = "You attempted an illegal launch on $td->name";
			$text = "An admin has been notified";
			$nm->add_new_news($player_name, 'player', 'misc', $subject, $text);

			$subject = "$pd->name attempted an illegal launch on $td->name";
			$text = "It was an intergalactic launch from galaxy $pd->galaxy to $td->galaxy";
			$nm->add_new_news('judal', 'player', 'misc', $subject, $text);
			show_error("You attempted an illegal launch on $td->name");
			return;
		}
		if ($star != $pd->star && !$dm->does_player_know_development($player_name, "Intergalactic Vehicles")
			&& !$dm->does_player_know_development($player_name, "Interstellar Vehicles") ) {
			// First tell launcher that he launched
			$subject = "You attempted an illegal launch on $td->name";
			$text = "An admin has been notified";
			$nm->add_new_news($player_name, 'player', 'misc', $subject, $text);

			$subject = "$pd->name attempted an illegal launch on $td->name";
			$text = "It was an interstellar launch from star $pd->star to $td->star";
			$nm->add_new_news('judal', 'player', 'misc', $subject, $text);
			show_error("You attempted an illegal launch on $td->name");
			return;
		}
		if ($planet != $pd->planet && !$dm->does_player_know_development($player_name, "Intergalactic Vehicles")
			&& !$dm->does_player_know_development($player_name, "Interstellar Vehicles")
			&& !$dm->does_player_know_development($player_name, "Interplanetary Vehicles") ) {
			// First tell launcher that he launched
			$subject = 'You attempted an illegal launch on $td->name';
			$text = "An admin has been notified";
			$nm->add_new_news($player_name, 'player', 'misc', $subject, $text);

			$subject = "$pd->name attempted an illegal launch on $td->name";
			$text = "It was an interplanetary launch from planet $pd->planet to $td->planet";
			$nm->add_new_news('judal', 'player', 'misc', $subject, $text);
			show_error("You attempted an illegal launch on $td->name");
			return;
		}
		if ($continent != $pd->continent && !$dm->does_player_know_development($player_name, "Intergalactic Vehicles")
			&& !$dm->does_player_know_development($player_name, "Interstellar Vehicles")
			&& !$dm->does_player_know_development($player_name, "Interplanetary Vehicles")
			&& !$dm->does_player_know_development($player_name, "Intercontinental Vehicles") ) {
			// First tell launcher that he launched
			$subject = "You attempted an illegal launch on $td->name";
			$text = "An admin has been notified";
			$nm->add_new_news($player_name, 'player', 'misc', $subject, $text);

			$subject = "$pd->name attempted an illegal launch on $td->name";
			$text = "It was an intercontinental launch from continent $pd->continent to $td->continent";
			$nm->add_new_news('judal', 'player', 'misc', $subject, $text);
			show_error("You attempted an illegal launch on $td->name");
			return;
		}

		
		// Get travel_time
		if ($galaxy != $pd->galaxy) $travel_time = $this->get_fleet_speed($player_name, 'galaxy', $weight);
		else if ($star != $pd->star) $travel_time = $this->get_fleet_speed($player_name, 'star', $weight);
		else if ($planet != $pd->planet) $travel_time = $this->get_fleet_speed($player_name, 'planet', $weight);
		else if ($continent != $pd->continent) $travel_time = $this->get_fleet_speed($player_name, 'continent', $weight);
		else {
			show_error("You cannot target yourself");
			return;
		}
		
		if ($dm->does_player_know_development($player_name, "Energy Conservation")) {
			show_info("Launching at $target_player for a travel time of $travel_time, launch cost is free as you have Energy Conservation");
		} else {
			
			$cost = $this->determine_fleet_launch_cost($player_name, $fleet);
			if ($pd->energy < $cost) {
				show_error("Not enough energy to launch");
				return;
			}
			$pd->subtract("energy", $cost);
			show_info("Launching at $target_player for a travel time of $travel_time, launch cost is: {$cost}e ");
		}
		
		
		// Get current tick
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		$launch_tick = $current_tick;
		$arrival_tick = $launch_tick + $travel_time;
		$depart_tick = $arrival_tick + $mission_time;
		if ($dm->does_player_know_development($player_name, "Teleportation")) {
			$return_tick = $depart_tick + 1;
		} else {
			$return_tick = $depart_tick + $travel_time;
		}
		
		$query = "insert into player_orders values ('$player_name', '$target_player', '$mission_type', $mission_time, 
			'{$fleet}', NOW(), $launch_tick, $arrival_tick, $depart_tick, $return_tick, 0, 0, 0, 0, 0)";
		$result = $conn->query($query);

		
		// First tell launcher that he launched
		$subject = 'Fleet Launched';
		$text = "$fleet has been launched at $target_player for mission type: $mission </BR>
		  Launch Tick: $launch_tick <BR/>
		  Arrival Tick: $arrival_tick <BR />
		  Depart Tick: $depart_tick <BR />
		  Return Tick: $return_tick <BR />  ";
		$nm->add_new_news($player_name, 'player', 'launch', $subject, $text);
		  // Also notify via email if applicable
		  $email->send_launch_email($player_name, $player_name, $target_player, $fleet, $mission, $launch_tick, $arrival_tick, $depart_tick, $return_tick);

		// Then tell target that has been launched on
		$subject = 'A Fleet has been launched at you';
		$text = "$player_name lauched $fleet at you for mission type: $mission_type </BR>
		  Launch Tick: $launch_tick <BR/>
		  Arrival Tick: $arrival_tick <BR />
		  Depart Tick: $depart_tick <BR />
		  Return Tick: $return_tick <BR />  ";
		$nm->add_new_news($target_player, 'player', 'launch', $subject, $text);
		  // Also notify via email if applicable
		  $email->send_launch_email($target_player, $player_name, $target_player, $fleet, $mission, $launch_tick, $arrival_tick, $depart_tick, $return_tick);

		// If there are any launch monitors of this player or the target, notify the person monitoring
		$subject = "$player_name launched $fleet at $target_player";
		$text = "$player_name lauched $fleet at $target_player for mission type: $mission_type </BR>
		  Launch Tick: $launch_tick <BR/>
		  Arrival Tick: $arrival_tick <BR />
		  Depart Tick: $depart_tick <BR />
		  Return Tick: $return_tick <BR />  ";
		  
		  
		$sm = new ScansModel();
//		$watchers = $sm->get_all_active_launch_monitors_by_target($target_player, $current_tick);
//		for ($i=0;$i<count($watchers);$i++) {
//			$nm->add_new_news($watchers[$i]["player_name"], 'player', 'launch', $subject, $text);
//		}
		
		$watchers = $sm->get_all_active_launch_monitors_by_target($player_name, $current_tick);
		for ($i=0;$i<count($watchers);$i++) {
			$nm->add_new_news($watchers[$i]["player_name"], 'player', 'launch', $subject, $text);
		}

		return $target_player;
		
	}
	
	function get_mission_time($mission) {
		if (strcmp($mission, 'attack1') == 0) return 1;
		if (strcmp($mission, 'attack2') == 0) return 2;
		if (strcmp($mission, 'attack3') == 0) return 3;
		if (strcmp($mission, 'defense1') == 0) return 1;
		if (strcmp($mission, 'defense2') == 0) return 2;
		if (strcmp($mission, 'defense3') == 0) return 3;
		if (strcmp($mission, 'defense4') == 0) return 4;
		if (strcmp($mission, 'defense5') == 0) return 5;
		if (strcmp($mission, 'defense6') == 0) return 6;
		return 0;
	}

	function get_mission_type($mission) {
		if (strcmp($mission, 'attack1') == 0) return 'attack';
		if (strcmp($mission, 'attack2') == 0) return 'attack';
		if (strcmp($mission, 'attack3') == 0) return 'attack';
		if (strcmp($mission, 'defense1') == 0) return 'defense';
		if (strcmp($mission, 'defense2') == 0) return 'defense';
		if (strcmp($mission, 'defense3') == 0) return 'defense';
		if (strcmp($mission, 'defense4') == 0) return 'defense';
		if (strcmp($mission, 'defense5') == 0) return 'defense';
		if (strcmp($mission, 'defense6') == 0) return 'defense';
		return 0;
	}

	function is_active_fleet_orders($player_name, $fleet) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
	  $conn = db_connect();
		$query = "select * from player_orders
			where player_name='$player_name' 
			  and fleet='$fleet'
			  and return_tick>$current_tick";
		$result = $conn->query($query);
		if (!$result || $result->num_rows == 0) return false;
		else return true;
	}

	function is_any_active_fleet_orders($player_name) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
	  $conn = db_connect();
		$query = "select * from player_orders
			where player_name='$player_name' 
			  and return_tick>$current_tick";
		$result = $conn->query($query);
		if (!$result || $result->num_rows == 0) return false;
		else return true;
	}
	
	function determine_fleet_launch_cost($player_name, $fleet) {
	  $conn = db_connect();
		$query = "select * from player_creatures pc, creature_items ci
			where pc.player_name='$player_name' 
			  and pc.fleet_location='$fleet'
			  and pc.creature = ci.name ";
		$result = $conn->query($query);
		$cost = 0;
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$cost += $row->number * ($row->mineral/100 + $row->organic/100);
		}
		return $cost;
	}
	
	function add_structures($player, $fleet, $current_tick, $unassigned, $extractors, $genetic_labs, $powerplants, $factories) {
	  $conn = db_connect();
		$query = "update player_orders 
			set 
			  unassigned = unassigned + $unassigned,
			  extractors = extractors + $extractors,
			  genetic_labs = genetic_labs + $genetic_labs,
				powerplants = powerplants + $powerplants,
				factories = factories + $factories
			where
				player_name='$player'
				and fleet='$fleet'
				and launch_tick < $current_tick
				and return_tick > $current_tick
			";
		$result = $conn->query($query);
	}
	
	function get_fleet_orders($player_name, $fleet) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
	  $conn = db_connect();
		$query = "select * from player_orders
			where player_name='$player_name' 
			  and fleet='$fleet'
			  and return_tick>$current_tick";
		$result = $conn->query($query);
		if (!$result || $result->num_rows == 0) return false;
		
		$row = $result->fetch_object();
		$orders = array();
		$orders["fleet_name"] = $fleet;
		$orders["mission"] = $row->mission_type;
		$orders["target"] = $row->target_name;
		$orders["launch"] = $row->launch_tick;
		$orders["arrive"] = $row->arrival_tick;
		$orders["depart"] = $row->depart_tick;
		$orders["return"] = $row->return_tick;
		
		return $orders;
	}
	
	function change_fleet_orders($player_name, $fleet, $new_launch_tick, $new_arrive_tick, $new_depart_tick, $new_return_tick) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
	  $conn = db_connect();
		$query = "update player_orders
			set launch_tick=$new_launch_tick, arrival_tick=$new_arrive_tick, depart_tick=$new_depart_tick, return_tick=$new_return_tick
			where player_name='$player_name' 
			  and fleet='$fleet'
			  and return_tick>$current_tick";
		$result = $conn->query($query);
	}
	
	function get_incoming($target_name) {
		$incoming_list = array();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
	  $conn = db_connect();
	  $query = "select * from player_orders where target_name = '$target_name' and launch_tick <= $current_tick and depart_tick >= $current_tick and mission_type != 'move'";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$incoming_list[$count]["launcher_name"] = $row->player_name;
			$incoming_list[$count]["launch_tick"] = $row->launch_tick;
			$incoming_list[$count]["arrival_tick"] = $row->arrival_tick;
			$incoming_list[$count]["depart_tick"] = $row->depart_tick;
			$incoming_list[$count]["return_tick"] = $row->return_tick;
			$incoming_list[$count]["mission_type"] = $row->mission_type;
			$incoming_list[$count]["fleet"] = $row->fleet;
		}
	  return $incoming_list;
	  
	}

	function is_move_order($player_name, $fleet) {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
	  $conn = db_connect();
		$query = "select * from player_orders
			where player_name='$player_name' 
			  and fleet='$fleet'
			  and return_tick>$current_tick";
		$result = $conn->query($query);
		if (!$result || $result->num_rows == 0) return false; // No order means no move order
		$row = $result->fetch_object();
		if (strcmp($row->mission_type, "move") == 0) return true;
		else return false;
	}
	
	function get_all_players_launching() {
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();

		$launchers_list = array();
	  $conn = db_connect();
		$query = "select distinct(player_name) from player_orders
			where launch_tick <= $current_tick
			  and return_tick > $current_tick";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$launchers_list[$count] = $row->player_name;
		}

		return $launchers_list;		
	}
	
	function get_fleet_details($player_name, $fleet, $current_tick) {
		$fd = new FleetData();
		$fd->creatures = array();

		$conn = db_connect();	
		$query = "select * from player_creatures pc, creature_items ci 
			 where pc.player_name='$player_name'
			   and pc.fleet_location='$fleet'
			   and pc.creature=ci.name";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fd->creatures["$row->creature"] = $row->number;
			
			$fd->att += $row->number * $row->attack;	
			$fd->def += $row->number * $row->defense;	
			$fd->foc += $row->number * $row->focus;	
			$int_total += ceil (($row->number * $row->attack * $row->intelligence));	
			$dis_total += ceil (($row->number * $row->defense * $row->discipline));	
		}
		$fd->int=ceil ($int_total/100);
		$fd->dis=ceil ($dis_total/100);
			
		$query = "select * from player_orders  
			 where player_name='$player_name'
			   and fleet='$fleet'
			   and return_tick>$current_tick";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		$fd->unassigned = $row->unassigned;	
		$fd->extractors = $row->extractors;	
		$fd->genetic_labs = $row->genetic_labs;	
		$fd->powerplants = $row->powerplants;	
		$fd->factories = $row->factories;	
		
		return $fd;
	}

}
?>