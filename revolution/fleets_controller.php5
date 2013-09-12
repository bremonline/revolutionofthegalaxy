<?php
	require_once("fleet_model.php5");
	require_once("game_model.php5");
	require_once("news_model.php5");
	require_once("pulses_model.php5");
	require_once("scans_model.php5");
	require_once("development_model.php5");
	require_once("view_fns.php5");
	require_once("email_helper.php5");

class FleetsController {
	function move_fleets() {
		$fm = new FleetModel();
  	$player_name=$_SESSION['player_name'];
  	$total_rows = $_REQUEST['total_rows'];
  	$from_home = $_REQUEST['from_home'];
  	$from_1 = $_REQUEST['from_1'];
 		$from_2 = $_REQUEST['from_2'];
 		$from_3 = $_REQUEST['from_3'];
 		
 		$rows = array();
 		for ($i=0;$i< $total_rows;$i++) {
 			$critter_list[$i]['home']=$_REQUEST["row{$i}_home"];
 			$critter_list[$i]['fleet1']=$_REQUEST["row{$i}_fleet1"];
 			$critter_list[$i]['fleet2']=$_REQUEST["row{$i}_fleet2"];
 			$critter_list[$i]['fleet3']=$_REQUEST["row{$i}_fleet3"];

 			$critter_list[$i]['creature']=$_REQUEST["row{$i}_creature"];
 		}
 		
 		if (is_null($critter_list)) return;
 		foreach ($critter_list as $critter) {
 			
 			if ($critter['home'] > 0) {
 				if (strcmp($from_home, "to_1") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet1") ) 
 					$this->move_creatures($critter['creature'], $critter['home'], 'home', 'fleet1');
 				if (strcmp($from_home, "to_2") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet2")) 
 					$this->move_creatures($critter['creature'], $critter['home'], 'home', 'fleet2');
 				if (strcmp($from_home, "to_3") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet3")) 
 					$this->move_creatures($critter['creature'], $critter['home'], 'home', 'fleet3');
			}
			
  		if ($critter['fleet1'] > 0) {
  			if (!$fm->is_active_fleet_orders($player_name, "fleet1")) {
					if (strcmp($from_1, "to_home") == 0) $this->move_creatures($critter['creature'], $critter['fleet1'], 'fleet1', 'home');
	 				if (strcmp($from_1, "to_2") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet2")) 
	 					$this->move_creatures($critter['creature'], $critter['fleet1'], 'fleet1', 'fleet2');
	 				if (strcmp($from_1, "to_3") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet3")) 
	 					$this->move_creatures($critter['creature'], $critter['fleet1'], 'fleet1', 'fleet3');
				}
			}
			
			
  		if ($critter['fleet2'] > 0) {
  			if (!$fm->is_active_fleet_orders($player_name, "fleet2")) {
	 				if (strcmp($from_2, "to_home") == 0) $this->move_creatures($critter['creature'], $critter['fleet2'], 'fleet2', 'home');
	 				if (strcmp($from_2, "to_1") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet1"))
	 					$this->move_creatures($critter['creature'], $critter['fleet2'], 'fleet1', 'fleet1');
	 				if (strcmp($from_2, "to_3") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet1")) 
	 					$this->move_creatures($critter['creature'], $critter['fleet2'], 'fleet3', 'fleet3');
				}
			}

  		if ($critter['fleet3'] > 0) {
  			if (!$fm->is_active_fleet_orders($player_name, "fleet3")) {
	 				if (strcmp($from_3, "to_home") == 0) $this->move_creatures($critter['creature'], $critter['fleet3'], 'fleet3', 'home');
	 				if (strcmp($from_3, "to_1") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet1")) 
	 					$this->move_creatures($critter['creature'], $critter['fleet3'], 'fleet3', 'fleet1');
	 				if (strcmp($from_3, "to_2") == 0 && !$fm->is_active_fleet_orders($player_name, "fleet2")) 
	 					$this->move_creatures($critter['creature'], $critter['fleet3'], 'fleet3', 'fleet2');
				}
 			}
 		}
 	}
	
	function move_creatures($creature, $number, $from, $to) {
		if ((strlen ($number) == 0) || $number == 0) return; // If you are not moving anything, do not move anything
  	$player_name=$_SESSION['player_name'];
		
		
		// 1. Check to see you have enough
		$fm = new FleetModel();
		$number_available = $fm->get_number_creatures($player_name, $creature, $from);
		
		if ($number == $number_available) {
			$fm->remove_row($player_name, $creature, $from);
		} else if ($number < $number_available) {
			$fm->subtract($player_name, $creature, $from, $number);
		} else {
			show_error ("You do not have enough creatures of type $creature");
			return;
		}
		
		$destination_number = $fm->get_number_creatures($player_name, $creature, $to);
		if ($destination_number && $destination_number > 0) {
			$fm->add($player_name, $creature, $to, $number);
		} else {
			$fm->make_new_row($player_name, $creature, $to, $number);
		
		}
		
		// 2. Subtract them from the old fleet
		// 2a. If you are taking all, delete the row
		// 2b. If you are not taking all subtract them
		// 3. Add them to the new fleet
		// 3a. If there is a row already add to it
		// 3b. If there is no row, then insert one.
	}
	
	function launch_fleet() {
  	$player_name=$_SESSION['player_name'];
  	$fleet = $_REQUEST['fleet'];
  	$galaxy = $_REQUEST['galaxy'];
  	$star = $_REQUEST['star'];
  	$planet = $_REQUEST['planet'];
  	$continent = $_REQUEST['continent'];
  	$target_name = $_REQUEST['target_name'];
  	$mission = $_REQUEST['mission'];
		$fm = new FleetModel();
		$pd = new PlayerData();
			
		if (strcmp($mission, "nothing") == 0) {
			show_error("No mission selected");
			return;
		}
		
		if ($target_name != "") {
			list($galaxy, $star, $planet, $continent) = $pd->get_location_from_player_name($target_name);
		}

		// Weight no longer from the view, it is computed each time.
  	$weight = $fm->get_weight_of_fleet($player_name, "fleet" . $fleet);		
		$target_player = $fm->set_launch_orders($player_name, $fleet, $weight, $galaxy, $star, $planet, $continent, $mission);
		
	}
	
	function recall_fleet() {
  	$player_name=$_SESSION['player_name'];
  	$fleet = $_REQUEST['fleet'];
  	
  	$fm = new FleetModel();
  	$gm = new GameModel();
  	$nm = new NewsModel();
  	$pm = new PulsesModel();
  	$dm = new DevelopmentModel();
  	$email = new EmailHelper();
  	
  	$current_tick = $gm->get_current_tick();
		$orders = $fm->get_fleet_orders($player_name, $fleet);
		$target_name = $orders["target"];
		
		// check to verify you can recall
		$jammed = $pm->is_shield_active($target_name, "Command Jammer", $current_tick);
		if ($jammed) {
			show_error("Cannot Recall Fleet.  Recall order is jammed.");
			return;
		}
		
		if ($current_tick > $orders["depart"]) {
			show_error("Cannot Recall Fleet.  Already Completed Mission.");
			return;
		}

		if ($fm->is_move_order($player_name, $fleet) ) {
			show_error("Cannot Recall Fleet from a move mission.");
			return;
		}		
		
		if ($current_tick > $orders["arrive"]) {
			$return_ticks = $orders["arrive"] - $orders["launch"];
			if ($return_ticks > 1 && $dm->does_player_know_development($player_name, "Teleportation")) $return_ticks = 1;

			$new_launch_tick = $orders["launch"];
			$new_arrive_tick = $orders["arrive"];
			$new_depart_tick = $current_tick;
			$new_return_tick = $current_tick + $return_ticks;
		} else {
			$return_ticks = $current_tick - $orders["launch"];
			if ($return_ticks > 1 && $dm->does_player_know_development($player_name, "Teleportation")) $return_ticks = 1;

			$new_launch_tick = $orders["launch"];
			$new_arrive_tick = $current_tick;
			$new_depart_tick = $current_tick;
			$new_return_tick = $current_tick + $return_ticks;
		}

		$fm->change_fleet_orders($player_name, $fleet, $new_launch_tick, $new_arrive_tick, $new_depart_tick, $new_return_tick);	
		
		show_info("$fleet has been recalled, it will be back on tick $new_return_tick");
		
		$subject = "You have recalled $fleet";
		$text = "You have recalled $fleet.  It will be back on tick $new_return_tick";
		$nm->add_new_news($player_name, 'player', 'launch', $subject, $text);
		// If appropriate, also send an email
		$email->send_recall_email($player_name, $player_name, $target_name, $fleet);

		$subject = "$player_name has recalled $fleet from $target_name";
		$text = "$player_name has recalled $fleet.";
		$nm->add_new_news($target_name, 'player', 'launch', $subject, $text);
		// If appropriate, also send an email
		$email->send_recall_email($target_name, $player_name, $target_name, $fleet);


		// If there are any launch monitors of this player or the target, notify the person monitoring
		$subject = "$player_name recalled $fleet from $target_name";
		$text = "$player_name recalled $fleet from $target_name";

		$sm = new ScansModel();
		$watchers = $sm->get_all_active_launch_monitors_by_target($target_name, $current_tick);
		for ($i=0;$i<count($watchers);$i++) {
			$nm->add_new_news($watchers[$i]["player_name"], 'player', 'launch', $subject, $text);
		}
		
		$watchers = $sm->get_all_active_launch_monitors_by_target($player_name, $current_tick);
		for ($i=0;$i<count($watchers);$i++) {
			$nm->add_new_news($watchers[$i]["player_name"], 'player', 'launch', $subject, $text);
		}
		
	}
	
	function move_bombs() {
		$bm = new BombsModel();
  	$player_name=$_SESSION['player_name'];
  	$from_home = $_REQUEST['from_home'];
  	$from_1 = $_REQUEST['from_1'];
 		$from_2 = $_REQUEST['from_2'];
 		$from_3 = $_REQUEST['from_3'];

 		$bombs_from_home = $_REQUEST['bombs_from_home'];
 		$bombs_from_fleet1 = $_REQUEST['bombs_from_fleet1'];
 		$bombs_from_fleet2 = $_REQUEST['bombs_from_fleet2'];
 		$bombs_from_fleet3 = $_REQUEST['bombs_from_fleet3'];
		
 		$poison_bombs_from_home = $_REQUEST['poison_bombs_from_home'];
 		$poison_bombs_from_fleet1 = $_REQUEST['poison_bombs_from_fleet1'];
 		$poison_bombs_from_fleet2 = $_REQUEST['poison_bombs_from_fleet2'];
 		$poison_bombs_from_fleet3 = $_REQUEST['poison_bombs_from_fleet3'];
		
		$bomb_locations = $bm->get_bomb_locations($player_name, 'Bomb');
		$bombs_at_home = $bomb_locations["home"];
		$bombs_at_fleet1 = $bomb_locations["fleet1"];
		$bombs_at_fleet2 = $bomb_locations["fleet2"];
		$bombs_at_fleet3 = $bomb_locations["fleet3"];
		$poison_bomb_locations = $bm->get_bomb_locations($player_name, 'Poison Bomb');
		$poison_bombs_at_home = $poison_bomb_locations["home"];
		$poison_bombs_at_fleet1 = $poison_bomb_locations["fleet1"];
		$poison_bombs_at_fleet2 = $poison_bomb_locations["fleet2"];
		$poison_bombs_at_fleet3 = $poison_bomb_locations["fleet3"];
		
		// Note you can load bombs from home anywhere, but bombs in fleet can only go home.

		if ( strcmp($from_home, "nowhere") != 0)  {
			if ($bombs_from_home > $bombs_at_home) { show_error("Not Enough Bombs to load"); return;	}
			if ($poison_bombs_from_home > $poison_bombs_at_home) { show_error("Not Enough Poision Bombs to load"); return;	}
			// Find out where the bombs are going...
			if ( strcmp($from_home, "to_1") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_home, $bombs_from_home, $bombs_at_fleet1, "home", "fleet1", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_home, $poison_bombs_from_home, $poison_bombs_at_fleet1, "home", "fleet1", "Poison Bomb");
			} else if ( strcmp($from_home, "to_2") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_home, $bombs_from_home, $bombs_at_fleet2, "home", "fleet2", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_home, $poison_bombs_from_home, $poison_bombs_at_fleet2, "home", "fleet2", "Poison Bomb");
			} else if ( strcmp($from_home, "to_3") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_home, $bombs_from_home, $bombs_at_fleet3, "home", "fleet3", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_home, $poison_bombs_from_home, $poison_bombs_at_fleet3, "home", "fleet3", "Poison Bomb");
			}	
		}
		
		if ( strcmp($from_1, "nowhere") != 0)  {
			if ($bombs_from_fleet1 > $bombs_at_fleet1) { show_error("Not Enough Bombs to unload"); return;	}
			if ($poison_bombs_from_fleet1 > $poison_bombs_at_fleet1) { show_error("Not Enough Poision Bombs to unload"); return; }
			// Find out where the bombs are going...
			if ( strcmp($from_1, "to_home") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_fleet1, $bombs_from_fleet1, $bombs_at_home, "fleet1", "home", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_fleet1, $poison_bombs_from_fleet1, $poison_bombs_at_home, "fleet1", "home", "Poison Bomb");
			}
		}

		if ( strcmp($from_2, "nowhere") != 0)  {
			if ($bombs_from_fleet2 > $bombs_at_fleet2) { show_error("Not Enough Bombs to unload"); return;	}
			if ($poison_bombs_from_fleet2 > $poison_bombs_at_fleet2) { show_error("Not Enough Poision Bombs to unload"); return; }
			// Find out where the bombs are going...
			if ( strcmp($from_2, "to_home") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_fleet2, $bombs_from_fleet2, $bombs_at_home, "fleet2", "home", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_fleet2, $poison_bombs_from_fleet2, $poison_bombs_at_home, "fleet2", "home", "Poison Bomb");
			} 	
		}
		
		if ( strcmp($from_3, "nowhere") != 0)  {
			if ($bombs_from_fleet2 > $bombs_at_fleet2) { show_error("Not Enough Bombs to unload"); return;	}
			if ($poison_bombs_from_fleet3 > $poison_bombs_at_fleet3) { show_error("Not Enough Poision Bombs to unload"); return; }
			// Find out where the bombs are going...
			if ( strcmp($from_3, "to_home") == 0) {
				$this->move_bombs_to_fleet($player_name, $bombs_at_fleet3, $bombs_from_fleet3, $bombs_at_home, "fleet3", "home", "Bomb");
				$this->move_bombs_to_fleet($player_name, $poison_bombs_at_fleet3, $poison_bombs_from_fleet3, $poison_bombs_at_home, "fleet3", "home", "Poison Bomb");
			} 	
		}
	}
	
	function move_bombs_to_fleet($player_name, $number_available, $number_moved, $number_at_destination, $from, $to, $type) {
		if ($number_moved == 0) return;
		$bm = new BombsModel();

		// First add or make a new row
//		if ($number_at_destination == 0) $bm->make_new_row($player_name, $type, $to, $number_moved);
		if ($bm->get_number_bombs_at_location($player_name, $type, $to) < 0)  $bm->make_new_row($player_name, $type, $to, $number_moved);
		else $bm->add($player_name, $type, $to, $number_moved);
		
		// Next remove or delete old row
		if ($number_moved == $number_available) $bm->remove_row($player_name, $type, $from);
		else $bm->subtract($player_name, $type, $from, $number_moved);
		
	}
}

?>