<?php
	require_once('news_model.php5');
	require_once('fleet_model.php5');
	require_once('development_model.php5');
	require_once('creatures_model.php5');
	require_once('fleet_data.php5');
	require_once('player_data.php5');

class BombTrapsCalculator {
	
	function bombs_traps_calculator($current_tick) {
		
		$cm = new CreaturesModel();
		$creature_stats = $cm->get_all_creatures_stats();
		
//		$target_list = $this->get_all_targets($current_tick);
//		$battle_list = $this->get_all_fighters($current_tick, $target_list);

		srand(time());

		$this->drop_bombs($current_tick); // Both regular and poison
		$this->trip_traps($current_tick, $creature_stats);
	}	
	
	function mark_single_creature_for_destruction($creature_list, &$destroyed_list, $total, $base_chance, $creature_stats, $stat) {
		
		$a = (rand() % $total);
		foreach ($creature_list as $creature => $number) {
			if (strcmp("total", $creature) != 0) {
				if ($a < $number) {
					// let critter make its discipline check
					list($owner_name, $fleet, $creature_name) = split(':', $creature);
					
					if ($stat = "DIS") $chance = $base_chance - $creature_stats["$creature_name"]["dis"];
					else $chance = $base_chance - $creature_stats["$creature_name"]["int"];
					
					$roll = (rand() % 100);
					if ($roll < $chance) $destroyed_list["$creature"]++;
					break;
				} else {
					$a -= $number;	
				}
			}
		}			
	}

	function drop_bombs($current_tick) {
		
		$conn = db_connect();	
		$query = "select * from player_orders where arrival_tick = $current_tick and mission_type='attack'";
	  $result = $conn->query($query);

		// Loop over all the attackers
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$this->drop_individual_bomb($current_tick, $row->player_name, $row->target_name, $row->fleet);
			$this->drop_individual_poison_bomb($current_tick, $row->player_name, $row->target_name, $row->fleet);
		}
		
	}
	
	function trip_traps($current_tick, $creature_stats) {
		$conn = db_connect();	
		$query = "select distinct(target_name) from player_orders where arrival_tick = $current_tick and mission_type='attack'";
	  $result = $conn->query($query);
	  $target_names_only = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$this->trip_individual_psych_trap($current_tick, $row->target_name, $creature_stats); 
			$this->trip_individual_trap($current_tick, $row->target_name, $creature_stats); 
		}
		
	}
	
	function drop_individual_bomb($current_tick, $player_name, $target_name, $fleet) {
		$bm = new BombsModel();
		$fm = new FortsModel();
		$nm = new NewsModel();

		$number_bombs = $bm->get_number_bombs_at_location($player_name, "Bomb", $fleet);
		if ($number_bombs <= 0) return;
		$original_bombs = $number_bombs;  // Used for news
		
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Advanced Bombs")) {
			$number_bombs = $number_bombs * 2;
			$advanced_flag = 'advanced';
		}

		if ($dm->does_player_know_development($target_name, "Ultimate Bomb Resistance")) {
			$number_bombs = ceil($number_bombs / 4);
			$resistent_fort_flag = 'resistent';			
		} else if ($dm->does_player_know_development($target_name, "Fort Bomb Resistance")) {
			$number_bombs = ceil($number_bombs / 2);
			$resistent_fort_flag = 'resistent';			
		}
				
		$number_forts = $fm->get_number_forts($target_name);
		
		$bm->remove_row($player_name, "Bomb", $fleet);
		
		if ($dm->does_player_know_development($target_name, "Fort Bomb Immunity")) {
			// News message to the target
			$target_subject = "Your forts have been unsuccessfully bombed";
			$target_text = "$player_name has dropped $original_bombs $advanced_flag bombs from $fleet on you forts.  Fortunately, you have Total Fort Protection so your forts are unharmed.";
			
			$nm->add_new_news($target_name, 'player', 'items', $target_subject, $target_text);
	
			$player_subject = "You tried to drop all of your bombs in $fleet";
			$player_text = "$fleet has dropped $original_bombs $advanced_flag bombs on {$target_name}\'s forts.  Unfortunately, the player has Total Fort Protection.  Your bombs failed to do any damage";
	
			$nm->add_new_news($player_name, 'player', 'items', $player_subject, $player_text);

		} else {
			
			if ($number_bombs >= $number_forts) {
				$fm->remove_all_forts($target_name);
				$target_text = "$player_name has dropped $original_bombs $advanced_flag bombs from $fleet on your forts.  It destroyed $number_forts of $number_forts forts";
				$player_text = "$fleet has dropped $original_bombs $advanced_flag bombs on {$target_name}\'s forts.  It destroyed $number_forts of $number_forts forts ";
			} else {
				$new_forts = $number_forts - $number_bombs;
				$fm->update_forts($target_name, $new_forts);
				$target_text = "$player_name has dropped $original_bombs $advanced_flag bombs from $fleet on your forts.  It destroyed $number_bombs of $number_forts forts";
				$player_text = "$fleet has dropped $original_bombs $advanced_flag bombs on {$target_name}\'s forts.  It destroyed $number_bombs of $number_forts forts";
			}
			
			// News message to the target
			$target_subject = "Your forts have been bombed";
			$nm->add_new_news($target_name, 'player', 'items', $target_subject, $target_text);
	
			$player_subject = "You have dropped all of your bombs in $fleet";
			$nm->add_new_news($player_name, 'player', 'items', $player_subject, $player_text);
		}
	}
	
	function drop_individual_poison_bomb($current_tick, $player_name, $target_name, $fleet) {
		$bm = new BombsModel();
		$fm = new FortsModel();
		$nm = new NewsModel();

		$number_bombs = $bm->get_number_bombs_at_location($player_name, "Poison Bomb", $fleet);
		if ($number_bombs <= 0) return;
		$original_bombs = $number_bombs;  // Used for news and for removing them from inventory

		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Advanced Bombs")) {
			$number_bombs = $number_bombs * 2;
			$advanced_flag = 'advanced';
		}
	
		// Next get list of all creatures
		$creature_list = $this->get_list_of_defenders($current_tick, $target_name);
		$total = $creature_list["total"];
		
		// Do not drop more bombs than creatures
		if ($number_bombs > $total) {
			// If they were using advaned bombs, then you have to destroy ony half of the total
			if ($dm->does_player_know_development($player_name, "Advanced Bombs")) {
				$lost_bombs = ceil($total/2);
			} else {
				$lost_bombs = $total;
			}
			$bm->subtract($player_name, "Poison Bomb", $fleet, $lost_bombs);
			
		} else {
			$bm->remove_row($player_name, "Poison Bomb", $fleet);
			$lost_bombs = $original_bombs;
		}

		
		// Initialize destroyed list
		$destroyed_list = array();
		
		foreach ($creature_list as $creature => $number) {
			if (strcmp("total", $creature) != 0) $destroyed_list["$creature"] = 0;
		}

		$total = $creature_list["total"];
		for ($i=0; $i<$number_bombs; $i++) {
			$this->mark_single_creature_for_destruction($creature_list, $destroyed_list, $total, 130, $creature_stats, "DIS");
		}


		$cm = new CreaturesModel();
		
		$text = "";
		$players_involved = array();
		foreach ($destroyed_list as $destroyed_creature => $number_destroyed) {
			if ($number_destroyed > 0) {
				
				$number_started = $creature_list["$destroyed_creature"];
				if ($number_destroyed > $number_started) $number_destroyed = $number_started;  // Can't destroy more than they have
				list($owner_name, $fleet, $creature) = split(':', $destroyed_creature);

				// Check to see if player is immune
				if ($dm->does_player_know_development($owner_name, "Effects Immunity")) {
					$text = $text . "$owner_name is immune to poison bombs.  $number_destroyed out of $number_started of $creature in $fleet were saved from destruction</br>\n";
				} else {
					$text = $text . "$owner_name lost $number_destroyed out of $number_started of $creature in $fleet </br>\n";
					if ($number_destroyed >= $number_started) {
						$cm->remove_all_creatures($owner_name, $creature, $fleet);
					} else {
						$number_left = $number_started - $number_destroyed;
						$cm->update_creatures($owner_name, $creature, $number_left, $fleet);
					}		
				}
				
				$players_involved["$owner_name"] = true;
			}
		}		
				
		$subject = "$lost_bombs $advanced_flag poison bombs were dropped on your continent";
		$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
		$subject = "You dropped $lost_bombs $advanced_flag poison bombs";
		$nm->add_new_news($player_name, 'player', 'items', $subject, $text);
		
		$subject = "Some of your forces were destroyed by $advanced_flag poison bombs";
		foreach($players_involved as $involved_player => $true_value) {
			if (strcmp($involved_player, $player_name) != 0 && strcmp($involved_player, $target_name) != 0) {
				$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
			}
		}
		
	}
	
	function trip_individual_trap($current_tick, $target_name, $creature_stats) {
		$bm = new BombsModel();
		$fm = new FortsModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		$cm = new CreaturesModel();

		$number_traps = $bm->get_number_bombs_at_location($target_name, "Trap", "active");
		if ($number_traps <= 0) return;

		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($target_name, "Advanced Traps")) {
			$number_traps = $number_traps * 2;
		}

		// Next get list of all creatures
		$creature_list = $this->get_list_of_attackers($current_tick, $target_name);
		$total = $creature_list["total"];

		// Do not drop more bombs that creatures
		if ($number_traps > $total) {
			$number_traps = $total;
			$bm->subtract($target_name, "Trap", "active", $number_traps);
		} else {
			$bm->remove_row($target_name, "Trap", "active");
		}
		
		// Initialize destroyed list
		$destroyed_list = array();
		foreach ($creature_list as $creature => $number) {
			if (strcmp("total", $creature) != 0) $destroyed_list["$creature"] = 0;
		}

		for ($i=0; $i < $number_traps; $i++) {
			// chance to kill critter is equal to 130-int 
			$trap_kill = 130;
			
			$this->mark_single_creature_for_destruction($creature_list, $destroyed_list, $total, $trap_kill, $creature_stats, "INT");
		}

		$destroyed=0;
		foreach ($destroyed_list as $destroyed_creatures) {
			$destroyed += $destroyed_creatures;
		}

		$text = "$number_traps of traps were triggered and destroyed $destroyed out of $total of the following creatures: <br />\n";
		$players_involved = array();
		foreach ($destroyed_list as $destroyed_creature => $number_destroyed) {
			if ($number_destroyed > 0) {
				
				$number_started = $creature_list["$destroyed_creature"];
				if ($number_destroyed > $number_started) $number_destroyed = $number_started;  // Can't destroy more than they have
				list($owner_name, $fleet, $creature) = split(':', $destroyed_creature);

				// Check to see if player is immune
				if ($dm->does_player_know_development($owner_name, "Effects Immunity")) {
					$text = $text . "$owner_name is immune to traps.  $number_destroyed out of $number_started of $creature in $fleet were saved from destruction</br>\n";
				} else {
					$text = $text . "$owner_name lost $number_destroyed out of $number_started of $creature in $fleet </br>\n";
					if ($number_destroyed >= $number_started) {
						$cm->remove_all_creatures($owner_name, $creature, $fleet);
					} else {
						$number_left = $number_started - $number_destroyed;
						$cm->update_creatures($owner_name, $creature, $number_left, $fleet);
					}		
				}
				
				$players_involved["$owner_name"] = true;
			}
		}		

		$subject = "$number_traps of your traps were triggered";
		$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
		
		$subject = "Some of your forces were destroyed by $number_traps traps";
		foreach($players_involved as $involved_player => $true_value) {
			if (strcmp($involved_player, $target_name) != 0) {
				$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
			}
		}
	}
	
	function trip_individual_psych_trap($current_tick, $target_name, $creature_stats) {
		$bm = new BombsModel();
		$fm = new FortsModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		$cm = new CreaturesModel();

		$number_traps = $bm->get_number_bombs_at_location($target_name, "Psychological Trap", "active");	
		if ($number_traps <= 0) return;

		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($target_name, "Advanced Traps")) {
			$number_traps = $number_traps * 2;
		}

		// Next get list of all creatures
		$creature_list = $this->get_list_of_attackers($current_tick, $target_name);
		$total = $creature_list["total"];
		
		// Do not drop more bombs that creatures
		if ($number_traps > $total) {
			$number_traps = $total;
			$bm->subtract($target_name, "Psychological Trap", "active", $number_traps);
		} else {
			$bm->remove_row($target_name, "Psychological Trap", "active");
		}
		
		// Initialize destroyed list
		$destroyed_list = array();
		foreach ($creature_list as $creature => $number) {
			if (strcmp("total", $creature) != 0) $destroyed_list["$creature"] = 0;
		}


		for ($i=0; $i < $number_traps; $i++) {
			// chance to kill critter is equal to 130-int	
			$this->mark_single_creature_for_destruction($creature_list, $destroyed_list, $total, 130, $creature_stats, "INT");
		}

		$destroyed=0;
		foreach ($destroyed_list as $destroyed_creatures) {
			$destroyed += $destroyed_creatures;
		}

		$text = "$number_traps of psychological traps were triggered and captured $destroyed out of $total of the following creatures: <br />\n";
		
		$players_involved = array(); 
		foreach ($destroyed_list as $destroyed_creature => $number_destroyed) { 
			if ($number_destroyed > 0) {
				
				$number_started = $creature_list["$destroyed_creature"];
				if ($number_destroyed > $number_started) $number_destroyed = $number_started;  // Can't destroy more than they have
				list($owner_name, $fleet, $creature) = split(':', $destroyed_creature);

				// Check to see if player is immune
				if ($dm->does_player_know_development($owner_name, "Effects Immunity")) {
					$text = $text . "$owner_name is immune to traps.  $number_destroyed out of $number_started of $creature in $fleet were saved from destruction</br>\n";
				} else {
					$text = $text . "$owner_name lost $number_destroyed out of $number_started of $creature in $fleet </br>\n";
					if ($number_destroyed >= $number_started) {
						$cm->remove_all_creatures($owner_name, $creature, $fleet);
					} else {
						$number_left = $number_started - $number_destroyed;
						$cm->update_creatures($owner_name, $creature, $number_left, $fleet);
					}		
					$cm->add_creatures_to_player($target_name, $creature, $number_destroyed, "home");
				}
				
				$players_involved["$owner_name"] = true;
			}
		}		

		$subject = "$number_traps of your psychological traps were triggered";
		$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
		
		$subject = "Some of your forces were captured by $number_traps psychological traps";
		foreach($players_involved as $involved_player => $true_value) {
			if (strcmp($involved_player, $target_name) != 0) {
				$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
			}
		}	
	}

	

// Database Functions
	
	function get_all_targets($current_tick) {
		
		$conn = db_connect();	
		$query = "select distinct(target_name) from player_orders where arrival_tick = $current_tick and depart_tick > $current_tick";
	  $result = $conn->query($query);
	  $target_names_only = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$target_names_only[$count] = $row->target_name;
		}
		return $target_names_only;
	}	

	function get_list_of_defenders($current_tick, $target_name) {
		$fm = new FleetModel();
		$creature_list = array();
		$conn = db_connect();	
		$query = "select * from player_orders po, player_creatures pc where 
			po.arrival_tick <= $current_tick and po.depart_tick > $current_tick and 
			po.mission_type='defense' and target_name='$target_name'
			and po.player_name = pc.player_name and po.fleet = pc.fleet_location
			";
	  $result = $conn->query($query);

		// Loop over all the attackers
		$total = 0;
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_list["{$row->player_name}:{$row->fleet}:{$row->creature}"] = $row->number;
			$total += $row->number;
		}


				
		// Now add target_players creatures at home
		$query = "select * from player_creatures where fleet_location='home' and player_name='$target_name' ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_list["{$target_name}:home:{$row->creature}"] = $row->number;
			$total += $row->number;
		}

		if (! $fm->is_active_fleet_orders($target_name, "fleet1") )  {
			$query = "select * from player_creatures where fleet_location='fleet1' and player_name='$target_name' ";
		  $result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$creature_list["{$target_name}:fleet1:{$row->creature}"] = $row->number;
				$total += $row->number;
			}
		}
		if (! $fm->is_active_fleet_orders($target_name, "fleet2") )  {
			$query = "select * from player_creatures where fleet_location='fleet2' and player_name='$target_name' ";
		  $result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$creature_list["{$target_name}:fleet2:{$row->creature}"] = $row->number;
				$total += $row->number;
			}
		}
		if (! $fm->is_active_fleet_orders($target_name, "fleet3") )  {
			$query = "select * from player_creatures where fleet_location='fleet3' and player_name='$target_name' ";
		  $result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$creature_list["{$target_name}:fleet3:{$row->creature}"] = $row->number;
				$total += $row->number;
			}
		}
		
		
		$creature_list["total"] = $total;
		
		return $creature_list;
	}


	function get_list_of_attackers($current_tick, $target_name) {
		$fm = new FleetModel();
		$creature_list = array();
		$conn = db_connect();	
		$query = "select * from player_orders po, player_creatures pc where 
				  po.arrival_tick = $current_tick  
			and po.mission_type='attack'
			and po.player_name = pc.player_name 
			and po.fleet = pc.fleet_location
			and po.target_name = '$target_name'
			";
	  $result = $conn->query($query);

		// Loop over all the attackers
		$total = 0;
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_list["{$row->player_name}:{$row->fleet}:{$row->creature}"] = $row->number;
			$total += $row->number;
		}

		$creature_list["total"] = $total;
		return $creature_list;
	}
	
}

?>
