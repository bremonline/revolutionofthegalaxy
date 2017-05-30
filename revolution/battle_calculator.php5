<?php
	require_once('news_model.php5');
	require_once('fleet_model.php5');
	require_once('creatures_model.php5');
	require_once('development_model.php5');
	require_once('fleet_data.php5');
	require_once('player_data.php5');
	require_once('email_helper.php5');

class BattleCalculator {

	function battle_calculator($current_tick) {
		$nm = new NewsModel();
		$fm = new FleetModel();
		$dm = new DevelopmentModel();

		$target_list = $this->get_all_targets($current_tick);
		$battle_list = $this->get_all_fighters($current_tick, $target_list);
		
		$target_string = "";
		// Each Target is a seperate battle
		foreach($battle_list as $target => $battle_data_by_player_fleet) {
			$pd = new PlayerData();
			$pd->db_fill($target);

			//add home defender
			$fd = $this->get_fleet_characteristics($target, "home", $target, "attack");
			$target_list["$target"]["{$target}:home"] = $fd;
	
			
			$attacker_att_bonus = false; $defender_att_bonus = false; 
			$attacker_def_bonus = false; $defender_def_bonus = false; 
			$attacker_foc_bonus = false; $defender_foc_bonus = false; 
			$attacker_int_bonus = false; $defender_int_bonus = false; 
			$attacker_dis_bonus = false; $defender_dis_bonus = false; 
			
			$attacker_has_capture_mastery = false; $defender_has_capture_mastery = false; 
			
			// First loop adds totals
			$total_attackers = new FleetData();
			$total_defenders = new FleetData();
			foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
				$player_fleet_array = split(":", $player_and_fleet);
				$player = $player_fleet_array[0];
				$fleet = $player_fleet_array[1];
				
				if (strcmp($fd->mission, "attack") == 0) {
					$this->add_to_total($total_attackers, $fd);
					if ($dm->does_player_know_development($player, "Combined Attack")) $attacker_att_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Defense")) $attacker_def_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Focus")) $attacker_foc_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Intelligence")) $attacker_int_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Discipline")) $attacker_dis_bonus=true;
					
					if ($dm->does_player_know_development($player, "Creature Capture")) $attacker_has_capture_mastery=true;
			}	else {
					$this->add_to_total($total_defenders, $fd);
					if ($dm->does_player_know_development($player, "Combined Attack")) $defender_att_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Defense")) $defender_def_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Focus")) $defender_foc_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Intelligence")) $defender_int_bonus=true;
					if ($dm->does_player_know_development($player, "Combined Discipline")) $defender_dis_bonus=true;

					if ($dm->does_player_know_development($player, "Creature Capture")) $defender_has_capture_mastery=true;
				}
				// check to see if there is any of the expert creature skills
				
			}
			
			if ($attacker_att_bonus) $total_attackers->att *= 1.20;
			if ($attacker_def_bonus) $total_attackers->def *= 1.20;
			if ($attacker_foc_bonus) $total_attackers->foc *= 1.20;
			if ($attacker_int_bonus) $total_attackers->int *= 1.20;
			if ($attacker_dis_bonus) $total_attackers->dis *= 1.20;
			
			if ($defender_att_bonus) $total_defenders->att *= 1.20;
			if ($defender_def_bonus) $total_defenders->def *= 1.20;
			if ($defender_foc_bonus) $total_defenders->foc *= 1.20;
			if ($defender_int_bonus) $total_defenders->int *= 1.20;
			if ($defender_dis_bonus) $total_defenders->dis *= 1.20;
			
			
			// Only if there is an attacker do we add the target to the universe news
			if ($total_attackers->att > 0) $target_string = $target_string . " " . $target;  

			// Time to compute Damage
			$total_attackers->damage = $this->get_damage($total_defenders, $total_attackers);
			$total_defenders->damage = $this->get_damage($total_attackers, $total_defenders);
			$total_attackers->captured = $this->get_creature_capture_att($total_defenders, $total_attackers);
			$total_defenders->captured = $this->get_creature_capture_def($total_attackers, $total_defenders);
			$total_attackers->structures_captured = $this->get_structure_capture($total_attackers, $total_defenders);
			$cap_structures = floor (($pd->unassigned + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory) * 0.10);
			if ($total_defenders->damage == 100) {
				$cap_structures *= 2; // Double the cap (to 20%) if there is no significant 
				$total_attackers->doubled=true;
			}
			
			if ($total_attackers->structures_captured > $cap_structures) {
				$total_attackers->structures_captured = $cap_structures;
				$total_attackers->capped=true;
			}
			
			// First destroy Forts 
			$fm = new FortsModel();
			$forts_destroyed = 0;
			$number_forts = $fm->get_number_forts($target);
			if ($number_forts < 0 ) $number_forts = 0;
			if ($number_forts > 0 ) {
				$fort_stats = $fm->get_fort_stats($target);
				$battle = $fort_stats["battle"];
				$fort_damage = $total_defenders->damage * (100 - $battle) / 100;
				$forts_destroyed = ceil ($number_forts * $fort_damage / 100);
				
			if ($dm->does_player_know_development($target, "Total Fort Protection")) {
				$forts_destroyed = 0;
			}
//				show_warning("Forts: $number_forts, Damage: $fort_damage, Destroyed: $forts_destroyed");
				if ($forts_destroyed > 0) {
					$new_forts = $number_forts - $forts_destroyed;
					$fm->update_forts($target, $new_forts);
				}
			}
			
			// Ok now time to calculate creatures killed and captured
			foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
				$player_fleet_array = split(":", $player_and_fleet);
				$player = $player_fleet_array[0];
				$fleet = $player_fleet_array[1];
//				show_warning("$player_and_fleet $player $fleet");

				foreach ($fd->creatures as $creature => $number) {
					if (strcmp($fd->mission,"attack") == 0) {
						$fd->creatures_lost["$creature"] = ceil($number * $total_attackers->captured / 100);
						$fd->creatures_killed["$creature"] = ceil( ($number - $fd->creatures_lost["$creature"]) * $total_attackers->damage / 100);
		
						// For capture mastery, note that this follows the above. 
						if ($defender_has_capture_mastery) {
							$fd->creatures_lost["$creature"] += $fd->creatures_killed["$creature"];
							$fd->creatures_killed["$creature"] = 0;
						}

						// For Mastery Skill: Battlefield Immunity, Zero out creatures lost
						if ($dm->does_player_know_development($player, "Battlefield Immunity")) {
// Commented out to allow BFI creatures to be captured
//							$fd->creatures_lost["$creature"] = 0; 
							$fd->creatures_killed["$creature"] = 0; 
						}
						

						$total_attackers->creatures_lost["$creature"] += $fd->creatures_lost["$creature"];
						$total_attackers->creatures_killed["$creature"] += $fd->creatures_killed["$creature"];
						$total_defenders->creatures_gained["$creature"] += $fd->creatures_lost["$creature"];
					} else {
						$fd->creatures_lost["$creature"] = ceil($number * $total_defenders->captured / 100);
						$fd->creatures_killed["$creature"] = ceil( ($number - $fd->creatures_lost["$creature"])* $total_defenders->damage / 100);

						// For Mastery Skill: Creature Rescue, Zero out creatures lost
						if ($dm->does_player_know_development($player, "Creature Rescue"))
							$fd->creatures_lost["$creature"] = 0; 

						// For capture mastery, note that this follows the above. 
						if ($attacker_has_capture_mastery) {
							$fd->creatures_lost["$creature"] += $fd->creatures_killed["$creature"];
							$fd->creatures_killed["$creature"] = 0;
						}

						// For Mastery Skill: Battlefield Immunity, Zero out creatures lost
						if ($dm->does_player_know_development($player, "Battlefield Immunity")) {
// Commented out to allow BFI creatures to be captured
//							$fd->creatures_lost["$creature"] = 0; 
							$fd->creatures_killed["$creature"] = 0; 
						}


						$total_defenders->creatures_lost["$creature"] += $fd->creatures_lost["$creature"];
						$total_defenders->creatures_killed["$creature"] += $fd->creatures_killed["$creature"];
						$total_attackers->creatures_gained["$creature"] += $fd->creatures_lost["$creature"];
					}
				}	
			}

				


			
			// Ok now that we computed how much was lost (or killed), it is time to figure out who gets the critters
			// First we make 100 bins based on the % of int each person has
			
		  srand(time());
			// First do attacker captures
			if (count($total_defenders->creatures_lost) > 0) {
				foreach ($total_defenders->creatures_lost as $creature_name => $number) {
					for ($i = 0; $i < $number; $i++) {
						$a = (rand() % $total_attackers->int);
						foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
							if (strcmp($fd->mission, "attack") == 0) {
								if ($a < $fd->int) {
									$fd->creatures_gained["{$creature_name}"]++;
									break;
								} else {
									$a = $a - $fd->int; 
								} // else of $a < int
							} // if mission attack
						} // foreach battle_data_by_player_fleet
					} // for loop
				}
			}
		  srand(time());
			// Then do defenders

			if ($total_attackers->creatures_lost != NULL) {
				foreach ($total_attackers->creatures_lost as $creature_name => $number) {
					for ($i = 0; $i < $number; $i++) {
						$a = (rand() % $total_defenders->int);
						foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
							if (strcmp($fd->mission, "defense") == 0) {
								if ($a < $fd->int) {
									$fd->creatures_gained["{$creature_name}"]++;
									break;
								} else {
									$a = $a - $fd->int; 
								} // else of $a < int
							} // if mission defense
						} // foreach battle_data_by_player_fleet
					} // for loop
				}
			}
			
			// Now do structures
			if ($attacker_foc_bonus) $foc_bonus = 1.2;  // Goto add combined focus to each sctructure capture.
			else $foc_bonus = 1.0;
			
			$total_targets_structures = ($pd->unassigned + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory);
			$total_attackers->unassigned = 0; 
			$total_attackers->extractors = 0; 
			$total_attackers->genetic_labs = 0; 
			$total_attackers->powerplants = 0; 
			$total_attackers->factories = 0; 
			if ($total_targets_structures > 0) { 
				$remaining_structures = $total_attackers->structures_captured;
				$total_attackers->unassigned = ceil ($total_attackers->structures_captured * ($pd->unassigned / $total_targets_structures));
				$remaining_structures -= $total_attackers->unassigned;

				$total_attackers->extractors = ceil ($total_attackers->structures_captured * ($pd->extractor / $total_targets_structures));
				if ($total_attackers->extractors > $remaining_structures) $total_attackers->extractors = $remaining_structures;
				$remaining_structures -= $total_attackers->extractors;

				$total_attackers->genetic_labs = ceil ($total_attackers->structures_captured * ($pd->genetic_lab / $total_targets_structures));
				if ($total_attackers->genetic_labs > $remaining_structures) $total_attackers->genetic_labs = $remaining_structures;
				$remaining_structures -= $total_attackers->genetic_labs;

				$total_attackers->powerplants = ceil ($total_attackers->structures_captured * ($pd->powerplant / $total_targets_structures));
				if ($total_attackers->powerplants > $remaining_structures) $total_attackers->powerplants = $remaining_structures;
				$remaining_structures -= $total_attackers->powerplants;

				$total_attackers->factories = ceil ($total_attackers->structures_captured * ($pd->factory / $total_targets_structures));
				if ($total_attackers->factories > $remaining_structures) $total_attackers->factories = $remaining_structures;
				$remaining_structures -= $total_attackers->factories;
			}

			srand(time());
			for ($i = 0; $i < $total_attackers->unassigned; $i++) {
				$a = (rand() % $total_attackers->foc);
				foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
					if (strcmp($fd->mission, "attack") == 0) {
						if ($a < $fd->foc * $foc_bonus) {
							$fd->unassigned++;
							break;
						} else {
							$a = $a - $fd->foc * $foc_bonus; 
						} //else of a < foc
					} // If mission attack
				} // foreach battle_data_by_player_fleet
			} //structures loop
			
				
			srand(time());
			for ($i = 0; $i < $total_attackers->extractors; $i++) {
				$a = (rand() % $total_attackers->foc);
				foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
					if (strcmp($fd->mission, "attack") == 0) {
						if ($a < $fd->foc * $foc_bonus) {
							$fd->extractors++;
							break;
						} else {
							$a = $a - $fd->foc * $foc_bonus; 
						} //else of a < foc
					} // If mission attack
				} // foreach battle_data_by_player_fleet
			} //structures loop
			
			for ($i = 0; $i < $total_attackers->genetic_labs; $i++) {
				$a = (rand() % $total_attackers->foc);
				foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
					if (strcmp($fd->mission, "attack") == 0) {
						if ($a < $fd->foc * $foc_bonus) {
							$fd->genetic_labs++;
							break;
						} else {
							$a = $a - $fd->foc * $foc_bonus; 
						} //else of a < foc
					} // If mission attack
				} // foreach battle_data_by_player_fleet
			} //structures loop
			
			for ($i = 0; $i < $total_attackers->powerplants; $i++) {
				$a = (rand() % $total_attackers->foc);
				foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
					if (strcmp($fd->mission, "attack") == 0) {
						if ($a < $fd->foc * $foc_bonus) {
							$fd->powerplants++;
							break;
						} else {
							$a = $a - $fd->foc * $foc_bonus; 
						} //else of a < foc
					} // If mission attack
				} // foreach battle_data_by_player_fleet
			} //structures loop
			
			for ($i = 0; $i < $total_attackers->factories; $i++) {
				$a = (rand() % $total_attackers->foc);
				foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
					if (strcmp($fd->mission, "attack") == 0) {
						if ($a < $fd->foc * $foc_bonus) {
							$fd->factories++;
							break;
						} else {
							$a = $a - $fd->foc * $foc_bonus; 
						} //else of a < foc
					} // If mission attack
				} // foreach battle_data_by_player_fleet
			} //structures loop
		
		
			// Remove the structures from the target
			$this->remove_structures_from_target($target, $total_attackers);

			// OK now move the structure from the target player to the appropriate fleet
			foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
				$player_fleet_array = split(":", $player_and_fleet);
				$player = $player_fleet_array[0];
				$fleet = $player_fleet_array[1];
				
				$this->add_structures_to_fleet_orders($player, $fleet, $fd, $current_tick);
			}
		
		
			// Kill and Capture Creatures
			foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
				$player_fleet_array = split(":", $player_and_fleet);
				$player = $player_fleet_array[0];
				$fleet = $player_fleet_array[1];

				$this->kill_and_capture($player, $fleet, $fd, $current_tick);
			}
			
			
		
			// For each fleet involved in the fight, we need to give them news
			// Also compile a list of all the fleets attacking and defending 
			$fleets_attacking_string = "";
			$fleets_defending_string = "";
			$fleets_by_player = array();
			foreach($battle_data_by_player_fleet as $player_and_fleet => $fd) {
				// Break the player fleet into seperate variables
				$player_fleet_array = split(":", $player_and_fleet);
				$player = $player_fleet_array[0];
				$fleet = $player_fleet_array[1];
				
				$fleets_by_player["$player"]["$fleet"] = $fd;
				if (strcmp($fd->mission, "attack") == 0) {
					$fleets_attacking_string = "$fleets_attacking_string $player_and_fleet <BR/>";
				} else {
					$fleets_defending_string = "$fleets_defending_string $player_and_fleet <BR/>";
					
				}
			}
			
			foreach ($fleets_by_player as $player => $players_fleets) {
				$this->battle_news($player, $target, $total_attackers, $total_defenders, $players_fleets,
					$fleets_attacking_string, $fleets_defending_string, $forts_destroyed) ;
			}
			
		}
		$this->universe_battle_news($current_tick, $target_string);
	}

	function get_all_targets($current_tick) {
		$conn = db_connect();	
		$query = "select distinct(target_name) from player_orders where arrival_tick<=$current_tick and depart_tick>$current_tick";
	  $result = $conn->query($query);
	  $target_names_only = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$target_names_only[$count] = $row->target_name;
		}
		return $target_names_only;
	}	


	function get_all_fighters($current_tick, $target_list_incoming) {
		$fleetModel = new FleetModel();
	  $target_list = array();
		// First add all home fleets of all targets
		foreach($target_list_incoming as $target_name) {
			$fd = $this->get_fleet_characteristics($target_name, "home", $target_name, "defense");
			$target_list["$target_name"]["{$target_name}:home"] = $fd;
	
			if (! $fleetModel->is_active_fleet_orders($target_name, "fleet1") )  {
				$fd = $this->get_fleet_characteristics($target_name, "fleet1", $target_name, "defense");
				$target_list["$target_name"]["{$target_name}:fleet1"] = $fd;
			}
			if (! $fleetModel->is_active_fleet_orders($target_name, "fleet2") )  {
				$fd = $this->get_fleet_characteristics($target_name, "fleet2", $target_name, "defense");
				$target_list["$target_name"]["{$target_name}:fleet2"] = $fd;
			}
			if (! $fleetModel->is_active_fleet_orders($target_name, "fleet3") )  {
				$fd = $this->get_fleet_characteristics($target_name, "fleet3", $target_name, "defense");
				$target_list["$target_name"]["{$target_name}:fleet3"] = $fd;
			}
			$fort_data = array();

			// OK now add the defense of the forts
			
			$fortModel = new FortsModel();
			$number_forts = $fortModel->get_number_forts($target_name);
			if ($number_forts < 0) $number_forts = 0;
			$fort_stats = $fortModel->get_fort_stats($target_name);
			$fort_attack = $number_forts * $fort_stats["att"];
			$fort_defense = $number_forts * $fort_stats["def"];
			
			$fort_data = new FleetData();
			$fort_data->def += $fort_defense;
			$fort_data->att += $fort_attack;
			$fort_data->foc += 0;
			$fort_data->dis += 0;
			$fort_data->int += 0;
			$fort_data->creatures = array();
			$fort_data->target = $target;
			$fort_data->mission = "defense";
			$fort_data->fleet = "forts";

			$target_list["$target_name"]["{$target_name}:forts"] = $fort_data;
		}
		
		// Then add the attackers
		$conn = db_connect();	
		$query = "select * from player_orders where arrival_tick<=$current_tick and depart_tick>$current_tick";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fd = $this->get_fleet_characteristics($row->player_name, $row->fleet, $row->target_name, $row->mission_type);
			$target_list["$row->target_name"]["{$row->player_name}:{$row->fleet}"] = $fd;
		}
		return $target_list;
	}	

	
	function get_fleet_characteristics($player_name, $fleet, $target, $mission) {
		$fd = new FleetData();
		$fd->creatures = array();

		$fd->target = $target;
		$fd->mission = $mission;
		$fd->fleet = $fleet;
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
		return $fd;
	}

	function add_to_total(&$total, $fd) {
		$total->att += $fd->att;
		$total->def += $fd->def;
		$total->foc += $fd->foc;
		$total->int += $fd->int;
		$total->dis += $fd->dis;
		
		foreach($fd->creatures as $creature_name => $number) {
			if ($total->creatures["$creature_name"] > 0) {
				$total->creatures["$creature_name"] += $number;
			} else {
				$total->creatures["$creature_name"] = $number;
			}
		}
	}
	
	function get_damage($attack, $defense) {
		$attack_ratio = $attack->att/ ($defense->def + 1);
		if ($attack_ratio > 50.0) $damage = 100;
		else if ($attack_ratio > 20.0) $damage = 40;
		else if ($attack_ratio > 10.0) $damage = 25;
		else if ($attack_ratio > 5.0) $damage = 20;
		else if ($attack_ratio > 3.0) $damage = 15;
		else if ($attack_ratio > 2.0) $damage = 12;
		else if ($attack_ratio > 1.5) $damage = 10;
		else if ($attack_ratio > 1.0) $damage = 8;
		else if ($attack_ratio > 0.9) $damage = 5;
		else if ($attack_ratio > 0.7) $damage = 3;
		else if ($attack_ratio > 0.5) $damage = 1;
		else $damage = 0;
		
		return $damage;
	}

	function get_creature_capture_att($attack, $defense) {
		$capture_ratio = ($attack->int*2)/($defense->dis+1);
		if ($capture_ratio > 2.0) $damage = 10;
		else if ($capture_ratio > 1.0) $damage = 5;
		else if ($capture_ratio > 0.5) $damage = 3;
		else if ($capture_ratio > 0.1) $damage = 2;
		else if ($capture_ratio > 0.05) $damage = 1;
		
		else $damage = 0;
		
		return $damage;
	}
	function get_creature_capture_def($attack, $defense) {
		$capture_ratio = $attack->int/($defense->dis+1);
		if ($capture_ratio > 2.0) $damage = 10;
		else if ($capture_ratio > 1.0) $damage = 5;
		else if ($capture_ratio > 0.5) $damage = 3;
		else if ($capture_ratio > 0.1) $damage = 2;
		else if ($capture_ratio > 0.05) $damage = 1;
		
		else $damage = 0;
		
		return $damage;
	}

	function get_structure_capture($attack, $defense) {
		$capture_ratio = ($attack->att - $defense->def) / ($attack->att+1) ;
		$capture_focus = $attack->foc * $capture_ratio;
		
		if ($attack->att > ($defense->def * 20) ) $capture_focus *= 2;
		
		if ($capture_focus < 0) $capture_focus = 0;

		$structures_captured = ceil ($capture_focus / 5000);
		
		return $structures_captured;
	}

	function battle_news($player, $target, $total_attackers, $total_defenders, $fleet_list,
			$fleets_attacking, $fleets_defending, $forts_destroyed) {
		$gm = new GameModel();
		$ct = $gm->get_current_tick();
		$nm = new NewsModel();
		$td = new PlayerData();
		$td->db_fill($target);
		$fm = new FortsModel();
		$forts_left = $fm->get_number_forts($target);
		if ($forts_left < 0) $forts_left = 0;
		$location = "{$td->galaxy}:{$td->star}:{$td->planet}:{$td->continent}";
		
		if (strcmp($player, $target) == 0){
			$subject = "Your continent is under attack";	
		} else {
			$subject = "Your fleet has landed on the continent of $location - $target";	
		}	
		
		$header_row = "<TR><TH class=\'STD\'>Tick: $ct </TH><TH class=\'STD\'>Attackers</TH><TH class=\'STD\'>Defenders</TH></TR>";
		
		$totals_row = "<TR>
				 <TH class=\'STD\'> Totals (All Fleets)</TH>
				 <TD class=\'STD\'>
					 Att:{$total_attackers->att} Def:{$total_attackers->def} 
					 Foc:{$total_attackers->foc} Int:{$total_attackers->int} Dis:{$total_attackers->dis}<BR />
				 </TD>
				 <TD class=\'STD\'>
					 Att:{$total_defenders->att} Def:{$total_defenders->def} 
					 Foc:{$total_defenders->foc} Int:{$total_defenders->int} Dis:{$total_defenders->dis}<BR />
				 </TD>
				</TR>"; 

		$capture_row = "<TR><TH class=\'STD\'> Projected Creatures Captured </TH>
			<TD class=\'STD\'>{$total_attackers->captured}%</TD>
			<TD class=\'STD\'>{$total_defenders->captured}%</TD></TR>";

		$forts_row = "<TR><TH class=\'STD\'> Forts Destroyed / Left</TH>
			<TD class=\'STD\'> &nbsp;</TD>
			<TD class=\'STD\'>$forts_destroyed / $forts_left </TD></TR>";
			
		$damage_row = "<TR><TH class=\'STD\'> Projected Creatures Killed </TH>
			<TD class=\'STD\'>{$total_attackers->damage}%</TD>
			<TD class=\'STD\'>{$total_defenders->damage}%</TD></TR>";

		// Show initial structures taken by fleet

		$target_structures_row = "<TR><TH class=\'STD\'> Structures After Attack</TH>
			<TD class=\'STD\'>&nbsp;</TD>
			<TD class=\'STD\'> {$td->unassigned}u/{$td->extractor}e/{$td->genetic_lab}g/{$td->powerplant}p/{$td->factory}f </TD></TR>";



		// Compile all structures taken by fleet
		$structure_statistics = "";
		foreach ($fleet_list as $fleet_name => $fd) {
			if (strcmp($fd->mission, "attack") == 0) {
				$structure_statistics = $structure_statistics . 
				" $player $fd->fleet {$fd->unassigned}u/{$fd->extractors}e/{$fd->genetic_labs}g/{$fd->powerplants}p/{$fd->factories}f<BR />\n";
			}
		}

		$structures_row = "<TR><TH class=\'STD\'> Structures Taken (Total)</TH>
			<TD class=\'STD\'>
				{$total_attackers->structures_captured} - 
				{$total_attackers->unassigned}u/{$total_attackers->extractors}e/{$total_attackers->genetic_labs}g/" . 
				"{$total_attackers->powerplants}p/{$total_attackers->factories}f <BR\>
				$structure_statistics";
				
		if ($total_attackers->doubled == true && $total_attackers->capped == true ) $structures_row .= "<BR />(doubled)(capped)";
		else if ($total_attackers->doubled == true) $structures_row .= "<BR />(doubled)";
		else if ($total_attackers->capped == true ) $structures_row .= "<BR />(capped)";
		$structures_row .= "</TD><TD class=\'STD\'>&nbsp;</TD></TR>";

		// Compile a listing of all fleets involved in a fight
		if (strlen($fleets_attacking) == 0) $fleets_attacking = "&nbsp;";
		if (strlen($fleets_defending) == 0) $fleets_defending = "&nbsp;";		
		$all_fleets_row = "<TR><TH class=\'STD\'> All Fleet(s)</TH>
			<TD class=\'STD\'>$fleets_attacking</TD>
			<TD class=\'STD\'>$fleets_defending</TD></TR>";
		
		
		// Compile all fleets of the player together
		$sp="&nbsp;";
		$fleet_statistics_attack = "";
		$fleet_statistics_defense = "";
		foreach ($fleet_list as $fleet_name => $fd) {
			if (strcmp($fd->mission, "attack") == 0){
				$fleet_statistics_attack = $fleet_statistics_attack . 
				" $fd->fleet <BR /> Att:{$fd->att}{$sp}Def:{$fd->def}{$sp}Foc:{$fd->foc}{$sp}Int:{$fd->int}{$sp}Dis:{$fd->dis}<BR />\n";
			} else {
				$fleet_statistics_defense = $fleet_statistics_defense . 
				" $fd->fleet <BR /> Att:{$fd->att}{$sp}Def:{$fd->def}{$sp}Foc:{$fd->foc}{$sp}Int:{$fd->int}{$sp}Dis:{$fd->dis}<BR />\n";
			
			}
		}

		if (strlen($fleet_statistics_attack) == 0) $fleet_statistics_attack = "&nbsp;";
		if (strlen($fleet_statistics_defense) == 0) $fleet_statistics_defense = "&nbsp;";
		$current_fleet_row = "<TR><TH class=\'STD\'> Your Fleet(s)</TH>
			<TD class=\'STD\'>$fleet_statistics_attack</TD>
			<TD class=\'STD\'>$fleet_statistics_defense</TD></TR>";


		// Compile a list of all players creautres killed (Currently not displayed)
		if (strcmp($fd->mission, "attack") == 0){
			$current_ceatures = "";
			foreach($fd->creatures as $creature => $number) {
				$current_creatures = $current_creatures . "{$creature}:{$number} <BR />\n";
			}
			$current_creatures_row = "<TR><TH class=\'STD\'> Your Creature(s) </TH><TD class=\'STD\'> $current_creatures </TD> <TD class=\'STD\'> &nbsp; </TD></TR>";
		} else {
			$current_creatures_row = "<TR><TH class=\'STD\'> Your Creature(s) </TH><TD class=\'STD\'> &nbsp; </TD><TD class=\'STD\'> $current_creatures </TD> </TR>";
		}

		if (strcmp($fd->mission, "attack") == 0){
			$creatures_killed = "";
			if ($fd->creatures_killed != NULL) {
				foreach($fd->creatures_killed as $creature => $number) {
					$creatures_killed = $creatures_killed . "{$creature}:{$number} <BR />\n";
				}
			}
			$creatures_killed_row = "<TR><TH class=\'STD\'> Your Creature(s) Killed </TH><TD class=\'STD\'> $creatures_killed </TD> <TD class=\'STD\'> &nbsp; </TD></TR>";
		} else {
			$creatures_killed_row = "<TR><TH class=\'STD\'> Your Creature(s) Killed </TH><TD class=\'STD\'> &nbsp; </TD><TD class=\'STD\'> $creatures_killed </TD> </TR>";
		}
				
				
		$battle_table = "
			<TABLE class=\'STD\'>
			$header_row
			$totals_row
			$capture_row
			$damage_row
			$forts_row
			$structures_row
			$target_structures_row
			$all_fleets_row
			$current_fleet_row
			</TABLE>
			";

		$creature_table = $this->get_creature_table($player, $target, $total_attackers, $total_defenders, $fleet_list);
					
		
		$text = $pretext . $battle_table . $creature_table;
		
		$nm->add_new_news($player, 'player', 'battle', $subject, $text);
		
		$email= new EmailHelper();		
		$email->send_battle_email($player, $target, 
					$total_attackers->att, $total_attackers->def, $total_attackers->foc, $total_attackers->int, $total_attackers->dis,
					$total_defenders->att, $total_defenders->def, $total_defenders->foc, $total_defenders->int, $total_defenders->dis,
					$total_attackers->captured, $total_attackers->damage, $total_defenders->captured, $total_defenders->damage,
					$total_attackers->structures_captured, $total_attackers->unassigned, $total_attackers->extractors,
					$total_attackers->genetic_labs, $total_attackers->powerplants, $total_attackers->factories,
					$forts_destroyed);			
	}
	
	function get_creature_table($player, $target, $total_attackers, $total_defenders, $fleet_list) {
		
		$header_row = "
			<TR>
				<TD class=\'STD\' colspan=\'2\'>&nbsp;</TD>
				<TD class=\'STD\' colspan=\'5\'>Attackers</TD>
				<TD class=\'STD\' colspan=\'5\'>Defenders</TD>
			</TR>
			<TR>
				<TH class=\'STD\'>Creature</TH>
				<TH class=\'STD\'>Fleet</TH>
				<TH class=\'STD\'>Before</TH>
				<TH class=\'STD\'>Killed</TH>
				<TH class=\'STD\'>Lost</TH>
				<TH class=\'STD\'>Gained</TH>
				<TH class=\'STD\'>After</TH>
				<TH class=\'STD\'>Before</TH>
				<TH class=\'STD\'>Killed</TH>
				<TH class=\'STD\'>Lost</TH>
				<TH class=\'STD\'>Gained</TH>
				<TH class=\'STD\'>After</TH>
			</TR>";

		$imp_row = $this->get_creature_row("Imp", $total_attackers, $total_defenders, $fleet_list);
		$wyrm_row = $this->get_creature_row("Wyrm", $total_attackers, $total_defenders, $fleet_list);
		$wyvern_row = $this->get_creature_row("Wyvern", $total_attackers, $total_defenders, $fleet_list);
		$dragon_row = $this->get_creature_row("Dragon", $total_attackers, $total_defenders, $fleet_list);

		$sprite_row = $this->get_creature_row("Sprite", $total_attackers, $total_defenders, $fleet_list);
		$dryad_row = $this->get_creature_row("Dryad", $total_attackers, $total_defenders, $fleet_list);
		$centaur_row = $this->get_creature_row("Centaur", $total_attackers, $total_defenders, $fleet_list);
		$unicorn_row = $this->get_creature_row("Unicorn", $total_attackers, $total_defenders, $fleet_list);
		
		$ogre_row = $this->get_creature_row("Ogre", $total_attackers, $total_defenders, $fleet_list);
		$troll_row = $this->get_creature_row("Troll", $total_attackers, $total_defenders, $fleet_list);
		$giant_row = $this->get_creature_row("Giant", $total_attackers, $total_defenders, $fleet_list);
		$demon_row = $this->get_creature_row("Demon", $total_attackers, $total_defenders, $fleet_list);
		
		$cheetah_row = $this->get_creature_row("Cheetah", $total_attackers, $total_defenders, $fleet_list);
		$panther_row = $this->get_creature_row("Panther", $total_attackers, $total_defenders, $fleet_list);
		$tiger_row = $this->get_creature_row("Tiger", $total_attackers, $total_defenders, $fleet_list);
		$lion_row = $this->get_creature_row("Lion", $total_attackers, $total_defenders, $fleet_list);
		
		$cyborg_row = $this->get_creature_row("Cyborg", $total_attackers, $total_defenders, $fleet_list);
		$spider_row = $this->get_creature_row("Spider", $total_attackers, $total_defenders, $fleet_list);
		$mantis_row = $this->get_creature_row("Mantis", $total_attackers, $total_defenders, $fleet_list);
		$megadon_row = $this->get_creature_row("Megadon", $total_attackers, $total_defenders, $fleet_list);
		
		$humvee_row = $this->get_creature_row("Humvee", $total_attackers, $total_defenders, $fleet_list);
		$tank_row = $this->get_creature_row("Tank", $total_attackers, $total_defenders, $fleet_list);
		$crusher_row = $this->get_creature_row("Crusher", $total_attackers, $total_defenders, $fleet_list);
		$doomcrusher_row = $this->get_creature_row("Doomcrusher", $total_attackers, $total_defenders, $fleet_list);
		
		$creature_table = "<BR />
			<TABLE class=\'STD\'>
			$header_row
			
			$imp_row
			$wyrm_row
			$wyvern_row
			$dragon_row

			$sprite_row
			$dryad_row
			$centaur_row
			$unicorn_row

			$ogre_row
			$troll_row
			$giant_row
			$demon_row

			$cheetah_row
			$panther_row
			$tiger_row
			$lion_row

			$cyborg_row
			$spider_row
			$mantis_row
			$megadon_row

			$humvee_row
			$tank_row
			$crusher_row
			$doomcrusher_row

			</TABLE>
			";
		return $creature_table;	
	}
	
	function get_creature_row($creature_name, $total_attackers, $total_defenders, $fleet_list) {
		$total_attackers_creatures = $total_attackers->creatures["{$creature_name}"];
		$total_defenders_creatures = $total_defenders->creatures["{$creature_name}"];
		$total_attackers_killed_creatures = $total_attackers->creatures_killed["{$creature_name}"];
		$total_defenders_killed_creatures = $total_defenders->creatures_killed["{$creature_name}"];
		$total_attackers_lost_creatures = $total_attackers->creatures_lost["{$creature_name}"];
		$total_defenders_lost_creatures = $total_defenders->creatures_lost["{$creature_name}"];
		$total_attackers_gained_creatures = $total_attackers->creatures_gained["{$creature_name}"];
		$total_defenders_gained_creatures = $total_defenders->creatures_gained["{$creature_name}"];
		$total_attackers_after_creatures = 
				$total_attackers_creatures + $total_attackers_gained_creatures - $total_attackers_killed_creatures - $total_attackers_lost_creatures;
		$total_defenders_after_creatures = 
				$total_defenders_creatures + $total_defenders_gained_creatures - $total_defenders_killed_creatures - $total_defenders_lost_creatures;

		// If there are no attackers and no defenders, do not display row at all
		if ($total_attackers_creatures == 0 &&
		    $total_defenders_creatures == 0 ) return "";
		
		if (strcmp($fleet_list['home']->mission, "attack") == 0) {
			$home_creatures_attack = $fleet_list['home']->creatures["{$creature_name}"];
			$home_creatures_killed_attack = $fleet_list['home']->creatures_killed["{$creature_name}"];
			$home_creatures_lost_attack = $fleet_list['home']->creatures_lost["{$creature_name}"];
			$home_creatures_gained_attack = $fleet_list['home']->creatures_gained["{$creature_name}"];

			$home_creatures_after_attack = 
					$home_creatures_attack + $home_creatures_gained_attack - $home_creatures_killed_attack - $home_creatures_lost_attack;
		} else {
			$home_creatures_defense = $fleet_list['home']->creatures["{$creature_name}"];	
			$home_creatures_killed_defense = $fleet_list['home']->creatures_killed["{$creature_name}"];	
			$home_creatures_lost_defense = $fleet_list['home']->creatures_lost["{$creature_name}"];	
			$home_creatures_gained_defense = $fleet_list['home']->creatures_gained["{$creature_name}"];	

			$home_creatures_after_defense = 
					$home_creatures_defense + $home_creatures_gained_defense - $home_creatures_killed_defense - $home_creatures_lost_defense;
		}
		
		if (strcmp($fleet_list['fleet1']->mission, "attack") == 0) {
			$fleet1_creatures_attack = $fleet_list['fleet1']->creatures["{$creature_name}"];
			$fleet1_creatures_killed_attack = $fleet_list['fleet1']->creatures_killed["{$creature_name}"];
			$fleet1_creatures_lost_attack = $fleet_list['fleet1']->creatures_lost["{$creature_name}"];
			$fleet1_creatures_gained_attack = $fleet_list['fleet1']->creatures_gained["{$creature_name}"];

			$fleet1_creatures_after_attack = 
					$fleet1_creatures_attack + $fleet1_creatures_gained_attack - $fleet1_creatures_killed_attack - $fleet1_creatures_lost_attack;
		} else {
			$fleet1_creatures_defense = $fleet_list['fleet1']->creatures["{$creature_name}"];
			$fleet1_creatures_killed_defense = $fleet_list['fleet1']->creatures_killed["{$creature_name}"];
			$fleet1_creatures_lost_defense = $fleet_list['fleet1']->creatures_lost["{$creature_name}"];
			$fleet1_creatures_gained_defense = $fleet_list['fleet1']->creatures_gained["{$creature_name}"];

			$fleet1_creatures_after_defense = 
					$fleet1_creatures_defense + $fleet1_creatures_gained_defense - $fleet1_creatures_killed_defense - $fleet1_creatures_lost_defense;
		}
		
		if (strcmp($fleet_list['fleet2']->mission, "attack") == 0) {
			$fleet2_creatures_attack = $fleet_list['fleet2']->creatures["{$creature_name}"];
			$fleet2_creatures_killed_attack = $fleet_list['fleet2']->creatures_killed["{$creature_name}"];
			$fleet2_creatures_lost_attack = $fleet_list['fleet2']->creatures_lost["{$creature_name}"];
			$fleet2_creatures_gained_attack = $fleet_list['fleet2']->creatures_gained["{$creature_name}"];

			$fleet2_creatures_after_attack = 
					$fleet2_creatures_attack + $fleet2_creatures_gained_attack - $fleet2_creatures_killed_attack - $fleet2_creatures_lost_attack;
		} else {
			$fleet2_creatures_defense = $fleet_list['fleet2']->creatures["{$creature_name}"];
			$fleet2_creatures_killed_defense = $fleet_list['fleet2']->creatures_killed["{$creature_name}"];
			$fleet2_creatures_lost_defense = $fleet_list['fleet2']->creatures_lost["{$creature_name}"];
			$fleet2_creatures_gained_defense = $fleet_list['fleet2']->creatures_gained["{$creature_name}"];

			$fleet2_creatures_after_defense = 
					$fleet2_creatures_defense + $fleet2_creatures_gained_defense - $fleet2_creatures_killed_defense - $fleet2_creatures_lost_defense;
		}
		
		if (strcmp($fleet_list['fleet3']->mission, "attack") == 0) {
			$fleet3_creatures_attack = $fleet_list['fleet3']->creatures["{$creature_name}"];
			$fleet3_creatures_killed_attack = $fleet_list['fleet3']->creatures_killed["{$creature_name}"];
			$fleet3_creatures_lost_attack = $fleet_list['fleet3']->creatures_lost["{$creature_name}"];
			$fleet3_creatures_gained_attack = $fleet_list['fleet3']->creatures_gained["{$creature_name}"];

			$fleet3_creatures_after_attack = 
					$fleet3_creatures_attack + $fleet3_creatures_gained_attack - $fleet3_creatures_killed_attack - $fleet3_creatures_lost_attack;
		} else {
			$fleet3_creatures_defense = $fleet_list['fleet3']->creatures["{$creature_name}"];
			$fleet3_creatures_killed_defense = $fleet_list['fleet3']->creatures_killed["{$creature_name}"];
			$fleet3_creatures_lost_defense = $fleet_list['fleet3']->creatures_lost["{$creature_name}"];
			$fleet3_creatures_gained_defense = $fleet_list['fleet3']->creatures_gained["{$creature_name}"];

			$fleet3_creatures_after_defense = 
					$fleet3_creatures_defense + $fleet3_creatures_gained_defense - $fleet3_creatures_killed_defense - $fleet3_creatures_lost_defense;
		}

		// 0s should be displayed as blanks
		if ($home_creatures_after_attack == 0) $home_creatures_after_attack="";
		if ($home_creatures_after_defense == 0) $home_creatures_after_defense="";
		if ($fleet1_creatures_after_attack == 0) $fleet1_creatures_after_attack="";
		if ($fleet1_creatures_after_defense == 0) $fleet1_creatures_after_defense="";
		if ($fleet2_creatures_after_attack == 0) $fleet2_creatures_after_attack="";
		if ($fleet2_creatures_after_defense == 0) $fleet2_creatures_after_defense="";
		if ($fleet3_creatures_after_attack == 0) $fleet3_creatures_after_attack="";
		if ($fleet3_creatures_after_defense == 0) $fleet3_creatures_after_defense="";
		
		$creature_row = "
			<TR>
				<TD class=\'STD\'>$creature_name</TD>
				<TD class=\'STD\' style=\'text-align:right\'>
					Yours: home<BR />
					Yours: fleet1<BR />
					Yours: fleet2<BR />
					Yours: fleet3<BR />
					<B>All Fleets:</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_attack}<BR />
					{$fleet1_creatures_attack}<BR />
					{$fleet2_creatures_attack}<BR />
					{$fleet3_creatures_attack}<BR />
					<B>{$total_attackers_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_killed_attack}<BR />
					{$fleet1_creatures_killed_attack}<BR />
					{$fleet2_creatures_killed_attack}<BR />
					{$fleet3_creatures_killed_attack}<BR />
					<B>{$total_attackers_killed_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_lost_attack}<BR />
					{$fleet1_creatures_lost_attack}<BR />
					{$fleet2_creatures_lost_attack}<BR />
					{$fleet3_creatures_lost_attack}<BR />
					<B>{$total_attackers_lost_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_gained_attack}<BR />
					{$fleet1_creatures_gained_attack}<BR />
					{$fleet2_creatures_gained_attack}<BR />
					{$fleet3_creatures_gained_attack}<BR />
					<B>{$total_attackers_gained_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_after_attack}<BR />
					{$fleet1_creatures_after_attack}<BR />
					{$fleet2_creatures_after_attack}<BR />
					{$fleet3_creatures_after_attack}<BR />
					<B>{$total_attackers_after_creatures}</B><BR />
				</TD>
								
				<TD class=\'STD\'>
					{$home_creatures_defense}<BR />
					{$fleet1_creatures_defense}<BR />
					{$fleet2_creatures_defense}<BR />
					{$fleet3_creatures_defense}<BR />
					<B>{$total_defenders_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_killed_defense}<BR />
					{$fleet1_creatures_killed_defense}<BR />
					{$fleet2_creatures_killed_defense}<BR />
					{$fleet3_creatures_killed_defense}<BR />
					<B>{$total_defenders_killed_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_lost_defense}<BR />
					{$fleet1_creatures_lost_defense}<BR />
					{$fleet2_creatures_lost_defense}<BR />
					{$fleet3_creatures_lost_defense}<BR />
					<B>{$total_defenders_lost_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_gained_defense}<BR />
					{$fleet1_creatures_gained_defense}<BR />
					{$fleet2_creatures_gained_defense}<BR />
					{$fleet3_creatures_gained_defense}<BR />
					<B>{$total_defenders_gained_creatures}</B><BR />
				</TD>
				<TD class=\'STD\'>
					{$home_creatures_after_defense}<BR />
					{$fleet1_creatures_after_defense}<BR />
					{$fleet2_creatures_after_defense}<BR />
					{$fleet3_creatures_after_defense}<BR />
					<B>{$total_defenders_after_creatures}</B><BR />
				</TD>
			</TR>								
		";
		
		return $creature_row;
	}
	
	function universe_battle_news($current_tick, $targets) {
		$nm = new NewsModel();
		$subject = "Tick #{$current_tick} battle report";
		if (strlen($targets) > 0) {	
			$text = "The following players are targets of attacks this tick: $targets ";
			$nm->add_new_news("", 'universe', 'battle', $subject, $text);
		} 
	}
	
	// You add structure to the fleet orders, when the fleet returns it adds them to the player
	function add_structures_to_fleet_orders($player, $fleet, $fd, $current_tick) {
		if ($fd->unassigned == 0 && $fd->extractors == 0 && $fd->genetic_labs == 0 && $fd->powerplants == 0 && $fd->factories == 0) return; // Nothing to add
		
		$fm = new FleetModel();
		$fm->add_structures($player, $fleet, $current_tick, $fd->unassigned, $fd->extractors, $fd->genetic_labs, $fd->powerplants, $fd->factories);
	}
	
	// You subtract structures directly from the player
	function remove_structures_from_target($target, $totals) {
		$pd = new PlayerData();
		$pd->subtract_structures_from_player($target, $totals->unassigned, $totals->extractors, $totals->genetic_labs, $totals->powerplants, $totals->factories);
	}
	
	// Creatures
	function kill_and_capture($player, $fleet, $fd, $current_tick) {
		$cm = new CreaturesModel();
		$dm = new DevelopmentModel();
		
		// First kill and lose ceatures		
		foreach ($fd->creatures as $creature => $number_started) {
			$number_lost = $fd->creatures_lost["$creature"];
			$number_killed = $fd->creatures_killed["$creature"];

			$number_gone = $number_lost + $number_killed;
			if ($number_gone >= $number_started) {
				$cm->remove_all_creatures($player, $creature, $fleet);
			} else {
				$number_left = $number_started - $number_gone;
				$cm->update_creatures($player, $creature, $number_left, $fleet);
			}
		}


		// Next capture creatures gained 
		if ($fd->creatures_gained != NULL) {		
			foreach ($fd->creatures_gained as $creature => $number_gained) {
				$cm->add_creatures_to_player($player, $creature, $number_gained, $fleet);			
			}
		}	
	}
}
