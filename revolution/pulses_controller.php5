<?php 
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('bombs_model.php5'); 
	require_once('player_data.php5'); 
	require_once('development_model.php5'); 

class PulsesController {
	function create_pulses() {
		$modulators = floor($_REQUEST["modulators"]);
		$reflectors = floor($_REQUEST["reflectors"]);
		$electromagnetic_pulses = floor($_REQUEST["electromagnetic_pulses"]);
		$microwave_pulses = floor($_REQUEST["microwave_pulses"]);
		$electromagnetic_shields = floor($_REQUEST["electromagnetic_shields"]);
		$microwave_shields = floor($_REQUEST["microwave_shields"]);
		$electromagnetic_blasts = floor($_REQUEST["electromagnetic_blasts"]);
		$microwave_blasts = floor($_REQUEST["microwave_blasts"]);
		$command_jammers = floor($_REQUEST["command_jammers"]);
		
		$this->create_generic_item("Modulator", $modulators);
		$this->create_generic_item("Reflector", $reflectors);
		$this->create_generic_item("Electromagnetic Pulse", $electromagnetic_pulses);
		$this->create_generic_item("Microwave Pulse", $microwave_pulses);
		$this->create_generic_item("Electromagnetic Shield", $electromagnetic_shields);
		$this->create_generic_item("Microwave Shield", $microwave_shields);
		$this->create_generic_item("Electromagnetic Blast", $electromagnetic_blasts);
		$this->create_generic_item("Microwave Blast", $microwave_blasts);
		$this->create_generic_item("Command Jammer", $command_jammers);
	}

	function create_generic_item($type, $number) {
		if ($number < 1) return;
 		$player_name=$_SESSION['player_name'];
		$dm = new DevelopmentModel();

		
		$pm = new PulsesModel();
		$details = $pm->get_pulse_details("$type");
		$mineral_cost = $details["mineral"] * $number;
		$organic_cost = $details["organic"] * $number;
		$energy_cost = $details["energy"] * $number;
		
		// If player knows Energy Conservation, then all items cost no energy
		if ($dm->does_player_know_development($player_name, "Energy Conservation")) $energy_cost = 0;
		
		$ticks = $details["ticks"];

		// Check to see if player can afford it
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		
		if ($pd->mineral < $mineral_cost) {
			show_error("You do not have enough resources");
			return;
		}
		if ($pd->energy < $energy_cost) {
			show_error("You do not have enough resources");
			return;
		}
		if ($pd->organic < $organic_cost) {
			show_error("You do not have enough resources");
			return;
		}
		
		// Check to see that the player has developed the technology to make it
		if ($dm->does_player_know_development($player_name, "$type") )$knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Modulator") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Reflector") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Pulse") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Shield") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Blast") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Pulse") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Shield") == 0) $knows_how = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Blast") == 0) $knows_how = true;
		if (! $knows_how) {
			show_error("You have not developed the required technology");
			return;
		}
		
		// OK take the money away
		$pd->subtract("mineral", $mineral_cost);
		$pd->subtract("energy", $energy_cost);
		$pd->subtract("organic", $organic_cost);
		
		// Now provision the bombs
		$gm = new GameModel();
  	$current_tick = $gm->get_current_tick();
		
		$pm->provision_pulses($player_name, $type, $number, $ticks, $current_tick);	
	}

	function fire_pulse() {
 		$player_name=$_SESSION['player_name'];
		// Make sure that the player is not on vacation
		$pd = new PlayerData();
		if ($pd->is_player_active($player_name) == false) {
			show_error("You may not fire a pulse or blast or activate a shield or jammer while on vacation");
			return;
		}

		$pulse_type = $_REQUEST["pulse_type"];
		
		if (strcmp($pulse_type, "Electromagnetic Pulse") == 0) $this->fire_electromagnetic_pulse();
		if (strcmp($pulse_type, "Microwave Pulse") == 0) $this->fire_microwave_pulse();

		if (strcmp($pulse_type, "Electromagnetic Shield") == 0) $this->activate_electromagnetic_shield();
		if (strcmp($pulse_type, "Microwave Shield") == 0) $this->activate_microwave_shield();
		
		if (strcmp($pulse_type, "Electromagnetic Blast") == 0) $this->fire_electromagnetic_blast();
		if (strcmp($pulse_type, "Microwave Blast") == 0) $this->fire_microwave_blast();


		if (strcmp($pulse_type, "Command Jammer") == 0) $this->activate_command_jammer();
	}
	
	function fire_electromagnetic_blast() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		
 		$player_name=$_SESSION['player_name'];

		$galaxy = $_REQUEST["galaxy"];
		$star = $_REQUEST["star"];
		$planet = $_REQUEST["planet"];
		$continent = $_REQUEST["continent"];

		$current_tick = $gm->get_current_tick();

		$pd = new PlayerData();
		$target_name = $pd->get_player_name_from_location($galaxy, $star, $planet, $continent);

		if (!$target_name) {
			show_error("No Player Found at that location");
			return;
		}
		if ($pd->is_player_active($target_name) == false) {
			show_error("You may not fire a blast at a player on vacation");
			return;
		}

		// Stop a blast if shield is active
		if ($pm->is_shield_active($target_name, "Electromagnetic Shield", $current_tick)) {
			show_error("You may not fire an electromagnetic blast at a player under an electromagnetic shield");
			return;
		}

		$number_blasts = $pm->get_number_pulses($player_name, "Electromagnetic Blast");
		if ($number_blasts <= 0) {
			show_error("No blasts to fire");
			return;
		}
		
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
		
		//  Ok all tests have passed, time to fire the pulse
		
		// First make sure they can't fire another pulse for a while. 6 ticks to be exact
		// Unless player has "Fast Blast"
		$ticks_to_advance = 5;
		if ($dm->does_player_know_development($player_name, "Fast Blasts")) {
			$ticks_to_advance = 0;
		}
		
		$pm->set_pulse_use($player_name, "Electromagnetic Blast", $current_tick + $ticks_to_advance);

		// Then remove a pulse from the inventory.
		
		if ($dm->does_player_know_development($player_name, "Continuous Blast")) {
			show_info("You have Continuous Blast. No Blasts used.");
		} else {
			$pm->update_pulses($player_name, "Electromagnetic Blast", $number_blasts-1);
		}
		
		// Is target immune?
		if ($dm->does_player_know_development($target_name, "Effects Immunity")) {
			// Finally we need to tell everyone what happened.
			$subject = "You fired an Electromagnetic Blast";
			$text = "You fired an Electromagnetic Blast at $target_name.  Unfortunately his creatures are immune to your pulse.  You did no damage.";
			$nm->add_new_news($player_name, 'player', 'items', $subject, $text);
			
			$subject = "$player_name fired an Electromagnetic Blast at your continent";
			$text = "$player_name fired an Electromagnetic Blast at your continent.   Forutnately your creatures are immune to the pulse.";
			$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
			
			show_info("You fired an Electromagnetic Blast at $target_name.  Unfortunately his creatures are immune to your pulse.  You did no damage.");
			return;
		}
		
		// Finally, fire it
				
		$modulators = $pm->get_number_pulses($player_name, "Modulator") + 11;	
		if ($dm->does_player_know_development($player_name, "Advanced Pulses")) {
			$modulators = $modulators * 3;
		}
			
		$reflectors = $pm->get_number_pulses($target_name, "Reflector") + 11;
		if ($dm->does_player_know_development($target_name, "Advanced Pulses")) {
			$reflectors = $reflectors * 3;
		}
		
		$ratio = $modulators / $reflectors;

		$damage = $pm->get_damage($ratio);
		$half_damage = $damage * 0.5;
		
		$fm = new FleetModel();
		$this->damage_creatures_from_electromagnetic($target_name, "home", $damage);
		$damage_text = "All creatures at home were hit!<BR />";
		if (!$fm->is_active_fleet_orders($target_name, "fleet1") ) {
			$this->damage_creatures_from_electromagnetic($target_name, "fleet1", $damage);
			$damage_text = $damage_text . "Fleet1 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet1 was away and not hit!<BR />";				
		}
		if (!$fm->is_active_fleet_orders($target_name, "fleet2") ) {
			$this->damage_creatures_from_electromagnetic($target_name, "fleet2", $damage);
			$damage_text = $damage_text . "Fleet2 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet2 was away and not hit!<BR />";				
		}
		if (!$fm->is_active_fleet_orders($target_name, "fleet3") ) {
			$this->damage_creatures_from_electromagnetic($target_name, "fleet3", $damage);
			$damage_text = $damage_text . "Fleet3 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet3 was away and not hit!<BR />";				
		}
		
		// Finally we need to tell everyone what happened.
		$subject = "You fired an Electromagnetic Blast";
		$text = "You fired an Electromagnetic Blast at $target_name.  The following fleets were hit<BR />" . $damage_text . 
			"It did {$damage}% to all Cybernetic creatures
			and {$half_damage}% to Hybrid creatures.";
		$nm->add_new_news($player_name, 'player', 'items', $subject, $text);
		
		$subject = "$player_name fired an Electromagnetic Blast at your continent";
		$text = "$player_name fired an Electromagnetic Blast at your continent.   The following fleets were hit<BR />" . $damage_text . 
			"It did {$damage}% to all Cybernetic creatures
			and {$half_damage}% to Hybrid creatures.";
		$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
		
		show_info("You fired an Electromagnetic Blast at $target_name.  It did {$damage}% damage.<BR />" . $damage_text);
	}

	function fire_microwave_blast() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		
 		$player_name=$_SESSION['player_name'];

		$galaxy = $_REQUEST["galaxy"];
		$star = $_REQUEST["star"];
		$planet = $_REQUEST["planet"];
		$continent = $_REQUEST["continent"];

		$current_tick = $gm->get_current_tick();

		$pd = new PlayerData();
		$target_name = $pd->get_player_name_from_location($galaxy, $star, $planet, $continent);

		if (!$target_name) {
			show_error("No Player Found at that location");
			return;
		}
		if ($pd->is_player_active($target_name) == false) {
			show_error("You may not fire a blast at a player on vacation");
			return;
		}

		// Stop a blast if shield is active
		if ($pm->is_shield_active($target_name, "Microwave Shield", $current_tick)) {
			show_error("You may not fire a microwave blast at a player under a microwave shield");
			return;
		}

		$number_blasts = $pm->get_number_pulses($player_name, "Microwave Blast");
		if ($number_blasts <= 0) {
			show_error("No blasts to fire");
			return;
		}
		
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
		
		// First make sure they can't fire another pulse for a while. 6 ticks to be exact
		// Unless player has "Fast Blast"
		$ticks_to_advance = 5;
		if ($dm->does_player_know_development($player_name, "Fast Blasts")) {
			$ticks_to_advance = 0;
		}
		$pm->set_pulse_use($player_name, "Microwave Blast", $current_tick + $ticks_to_advance);
		
		// Then remove a pulse from the inventory.
		if ($dm->does_player_know_development($player_name, "Continuous Blast")) {
			show_info("You have Continuous Blast.  No blasts used.");
		} else {
			$pm->update_pulses($player_name, "Microwave Blast", $number_blasts-1);
		}
		
		// Is target immune?
		if ($dm->does_player_know_development($target_name, "Effects Immunity")) {
			// Finally we need to tell everyone what happened.
			$subject = "You fired an Microwave Blast";
			$text = "You fired an Microwave Blast at $target_name.  Unfortunately his creatures are immune to your pulse.  You did no damage.";
			$nm->add_new_news($player_name, 'player', 'items', $subject, $text);
			
			$subject = "$player_name fired an Microwave Blast at your continent";
			$text = "$player_name fired an Microwave Blast at your continent.   Forutnately your creatures are immune to the pulse.";
			$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
			
			show_info("You fired an Microwave Blast at $target_name.  Unfortunately his creatures are immune to your pulse.  You did no damage.");
			return;
		}
		
		
		// Finally, fire it
				
		$modulators = $pm->get_number_pulses($player_name, "Modulator") + 11;	
		if ($dm->does_player_know_development($player_name, "Advanced Pulses")) {
			$modulators = $modulators * 3;
		}
			
		$reflectors = $pm->get_number_pulses($target_name, "Reflector") + 11;
		if ($dm->does_player_know_development($target_name, "Advanced Pulses")) {
			$reflectors = $reflectors * 3;
		}
		
		$ratio = $modulators / $reflectors;

		$damage = $pm->get_damage($ratio);
		$half_damage = $damage * 0.5;
		
		$fm = new FleetModel();
		$damage_text = "All creatures at home were hit!<BR />";
		$this->damage_creatures_from_microwave($target_name, "home", $damage);
		if (!$fm->is_active_fleet_orders($target_name, "fleet1") ) {
			$this->damage_creatures_from_microwave($target_name, "fleet1", $damage);
			$damage_text = $damage_text . "Fleet1 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet1 was away and not hit!<BR />";				
		}
		if (!$fm->is_active_fleet_orders($target_name, "fleet2") ) {
			$this->damage_creatures_from_microwave($target_name, "fleet2", $damage);
			$damage_text = $damage_text . "Fleet2 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet2 was away and not hit!<BR />";				
		}
		if (!$fm->is_active_fleet_orders($target_name, "fleet3") ) {
			$this->damage_creatures_from_microwave($target_name, "fleet3", $damage);
			$damage_text = $damage_text . "Fleet3 was hit!<BR />";
		} else {
			$damage_text = $damage_text . "Fleet3 was away and not hit!<BR />";				
		}
		
		// Finally we need to tell everyone what happened.
		$subject = "You fired a Microwave Blast";
		$text = "You fired a Microwave Blast at $target_name.  The following fleets were hit<BR />" . $damage_text .
			"It did {$damage}% to all Genetic creatures
			and {$half_damage}% to Hybrid creatures.";
		$nm->add_new_news($player_name, 'player', 'items', $subject, $text);
		
		$subject = "$player_name fired a Microwave Blast at your continent";
		$text = "$player_name fired a Microwave Blast at your continent.  The following fleets were hit<BR />" . $damage_text . 
			"It did {$damage}% to all Genetic creatures
			and {$half_damage}% to Hybrid creatures.";
		$nm->add_new_news($target_name, 'player', 'items', $subject, $text);
		
		show_info("You fired a Microwave Blast at $target_name.  It did {$damage}% damage.<BR />" . $damage_text);
		
		
	}


	function fire_electromagnetic_pulse() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		
 		$player_name=$_SESSION['player_name'];

		$number_pulses = $pm->get_number_pulses($player_name, "Electromagnetic Pulse");
		if ($number_pulses <= 0) {
			show_error("No pulses to fire");
			return;
		}
		
		$current_tick = $gm->get_current_tick();
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
		
		// Now find out who is in range
		$victims = $pm->get_pulse_victims($player_name, $current_tick);
		
		if (count($victims) == 0) {
			show_info("No fleets in range.  No pulse used.");
			return;
		}

		// First make sure they can't fire another pulse for a while.
		$pm->set_pulse_use($player_name, "Electromagnetic Pulse", $current_tick);
		
		// Then remove a pulse from the inventory.
		$pm->update_pulses($player_name, "Electromagnetic Pulse", $number_pulses-1);
		
		
		
		// Finally, fire it
		$players_involved = array();
		$text = "The fleets listed below were all affected: <br />\n";
		$modulators = $pm->get_number_pulses($player_name, "Modulator") + 11;	
		if ($dm->does_player_know_development($player_name, "Advanced Pulses")) {
			$modulators = $modulators * 3;
		}
		
		for ($i=0;$i<count($victims);$i++) {
			list($launcher_name, $fleet) = split(':', $victims[$i]);
			$reflectors = $pm->get_number_pulses($launcher_name, "Reflector") + 11;
			if ($dm->does_player_know_development($launcher_name, "Advanced Pulses")) {
				$reflectors = $reflectors * 3;
			}
			
			$ratio = $modulators / $reflectors;
	
			$damage = $pm->get_damage($ratio);
			$half_damage = $damage * 0.5;

			// Is target immune?
			if ($dm->does_player_know_development($launcher_name, "Effects Immunity")) {
				$text = $text . "$launcher_name:$fleet is immune to the pulse<br />\n";
			} else {
				$this->damage_creatures_from_electromagnetic($launcher_name, $fleet, $damage);
				$text = $text . "$launcher_name:$fleet {$damage}%<br />\n";
			}
			
			$players_involved["$launcher_name"] = true;

		}

		$subject = "Your Electromagnetic Pulse was fired";
		$nm->add_new_news($player_name, 'player', 'items', $subject, $text);


		$subject = "Some of your forces were destroyed by an Electromagnetic Pulse";
		foreach($players_involved as $involved_player => $true_value) {
			$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
		}		
		show_info("Your pulse was fired.  " . $text);
	}


	function fire_microwave_pulse() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		$dm = new DevelopmentModel();
		
 		$player_name=$_SESSION['player_name'];

		$number_pulses = $pm->get_number_pulses($player_name, "Microwave Pulse");
		if ($number_pulses <= 0) {
			show_error("No pulses to fire");
			return;
		}
		
		$current_tick = $gm->get_current_tick();
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
		
		// Now find out who is in range
		$victims = $pm->get_pulse_victims($player_name, $current_tick);
		
		if (count($victims) == 0) {
			show_info("No fleets in range.  No pulse used.");
			return;
		}

		// First make sure they can't fire another pulse for a while.
		$pm->set_pulse_use($player_name, "Microwave Pulse", $current_tick);
		
		// Then remove a pulse from the inventory.
		$pm->update_pulses($player_name, "Microwave Pulse", $number_pulses-1);
		
		// Finally, fire it
		$players_involved = array();
		$text = "The fleets listed below were all affected: <br />\n";
		$modulators = $pm->get_number_pulses($player_name, "Modulator") + 11;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses")) {
			$modulators = $modulators * 3;
		}
		for ($i=0;$i<count($victims);$i++) {
			list($launcher_name, $fleet) = split(':', $victims[$i]);
			$reflectors = $pm->get_number_pulses($launcher_name, "Reflector") + 11;
			if ($dm->does_player_know_development($launcher_name, "Advanced Pulses")) {
				$reflectors = $reflectors * 3;
			}

			$ratio = $modulators / $reflectors;
	
			$damage = $pm->get_damage($ratio);
			$half_damage = $damage * 0.5;

			// Is target immune?
			if ($dm->does_player_know_development($launcher_name, "Effects Immunity")) {
				$text = $text . "$launcher_name:$fleet is immune to the pulse<br />\n";
			} else {
				$this->damage_creatures_from_microwave($launcher_name, $fleet, $damage);
				$text = $text . "$launcher_name:$fleet {$damage}%<br />\n";
			}
			
			$players_involved["$launcher_name"] = true;
			
		}

		$subject = "Your Microwave Pulse was fired";
		$nm->add_new_news($player_name, 'player', 'items', $subject, $text);


		$subject = "Some of your forces were destroyed by a Microwave Pulse";
		foreach($players_involved as $involved_player => $true_value) {
			$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
		}		
		
		show_info("Your pulse was fired.  " . $text);
	}

	function activate_electromagnetic_shield() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		
 		$player_name=$_SESSION['player_name'];

		$number_shields = $pm->get_number_pulses($player_name, "Electromagnetic Shield");
		if ($number_shields <= 0) {
			show_error("No shields to activate");
			return;
		}
		
		$current_tick = $gm->get_current_tick();
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
	
		// First make sure they can't fire another pulse for a while.
		// Shields work for 24 ticks
		$shield_duration=24;
		$end_tick = $current_tick + $shield_duration;
		$pm->set_pulse_use($player_name, "Electromagnetic Shield", $end_tick);
		
		// Then remove a pulse from the inventory.
		$pm->update_pulses($player_name, "Electromagnetic Shield", $number_shields-1);

		show_info("Your have activated your Electromagnetic Shield.  It will remain in effect until tick " . $end_tick);

		$subject = "$player_name has activated a shield";
		$text = "$player_name has activated a shield.  It will be in effect until tick ". $end_tick;
		$nm->add_new_news("", "universe", 'items', $subject, $text);		
	}

	function activate_microwave_shield() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		
 		$player_name=$_SESSION['player_name'];

		$number_shields = $pm->get_number_pulses($player_name, "Microwave Shield");
		if ($number_shields <= 0) {
			show_error("No shields to activate");
			return;
		}
		
		$current_tick = $gm->get_current_tick();
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
	
		// First make sure they can't fire another pulse for a while.
		// Shields work for 24 ticks
		$shield_duration=24;
		$end_tick = $current_tick + $shield_duration;
		$pm->set_pulse_use($player_name, "Microwave Shield", $end_tick);
		
		// Then remove a pulse from the inventory.
		$pm->update_pulses($player_name, "Microwave Shield", $number_shields-1);

		show_info("Your have activated your Microwave Shield.  It will remain in effect until tick " . $end_tick);

		$subject = "$player_name has activated a shield";
		$text = "$player_name has activated a shield.  It will be in effect until tick ". $end_tick;
		$nm->add_new_news("", "universe", 'items', $subject, $text);		
	}

	function activate_command_jammer() {
		$pm = new PulsesModel();
		$gm = new GameModel();
		$nm = new NewsModel();
		$fm = new FleetModel();
		
 		$player_name=$_SESSION['player_name'];

		$number_shields = $pm->get_number_pulses($player_name, "Command Jammer");
		if ($number_shields <= 0) {
			show_error("No Command Jammers to activate");
			return;
		}
		
		$current_tick = $gm->get_current_tick();
		if ($pm->has_pulse_been_fired($player_name, $current_tick) ) {
			show_error("You have already used a pulse/blast/shield this tick");
			return;
		}
	
		// First make sure they can't fire another pulse for a while.
		// Shields work for 24 ticks
		$shield_duration=24;
		$end_tick = $current_tick + $shield_duration;
		$pm->set_pulse_use($player_name, "Command Jammer", $end_tick);
		
		// Then remove a pulse from the inventory.
		$pm->update_pulses($player_name, "Command Jammer", $number_shields-1);

		show_info("Your have activated your Command Jammer.  It will remain in effect until tick " . $end_tick);

		$subject = "$player_name has activated a Command Jammer";
		$text = "$player_name has activated a Command Jammer.  It will be in effect until tick ". $end_tick;
		$nm->add_new_news("", "universe", 'items', $subject, $text);		
		
		$incoming_list = $fm->get_incoming($player_name);
		for ($i=0; $i < count($incoming_list); $i++) {
			$launcher_name = $incoming_list[$i]["launcher_name"];
			$fleet = $incoming_list[$i]["fleet"];
			$subject = "Your communications with $fleet has been jammed";
			$text = "$player_name has activated a Command Jammer.  It prevents you from recalling $fleet. It will be in effect until tick ". $end_tick;
			$nm->add_new_news("$launcher_name", "player", 'items', $subject, $text);		
		}
	}

	
	function damage_creatures_from_electromagnetic($player_name, $fleet, $damage) {
		$cm = new CreaturesModel();
		$cyber_damage = 1 - ($damage * 0.01);
		$hybrid_damage = 1 - ($damage * 0.5 * 0.01);
		
		$cm->damage_cybernetic_creatures($player_name, $fleet, $cyber_damage);
		$cm->damage_hybrid_creatures($player_name, $fleet, $hybrid_damage);
		
	}

	function damage_creatures_from_microwave($player_name, $fleet, $damage) {
		$cm = new CreaturesModel();
		$genetic_damage = 1 - ($damage * 0.01);
		$hybrid_damage = 1 - ($damage * 0.5 * 0.01);
		
		$cm->damage_genetic_creatures($player_name, $fleet, $genetic_damage);
		$cm->damage_hybrid_creatures($player_name, $fleet, $hybrid_damage);
		
	}

} 

?>