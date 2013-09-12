<?php
	require_once('news_model.php5');
	require_once('pulses_model.php5');

class PulseCalculator {

	function cross_shield($current_tick) {
		$pm = new PulsesModel();
		// First find all targets that are about to be attacked.
		$conn = db_connect();	
		$query = "select distinct(target_name) from player_orders where arrival_tick = $current_tick + 1 and depart_tick > $current_tick";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
		// For each target, see if they have any shields currently active
			if ($pm->is_shield_active($row->target_name, "Electromagnetic Shield", $current_tick) ) {
				$this->trigger_electromagnetic_shield_effects($row->target_name, $current_tick);
			}
			if ($pm->is_shield_active($row->target_name, "Microwave Shield", $current_tick) ) {
				$this->trigger_microwave_shield_effects($row->target_name, $current_tick);
			}
		}
	}

	function trigger_electromagnetic_shield_effects($shield_owner_name, $current_tick) {
		$nm = new NewsModel();
		$pm = new PulsesModel();
		$dm = new DevelopmentModel();
		$victims = $pm->get_pulse_victims($shield_owner_name, $current_tick);

		// Finally, fire it
		$players_involved = array();
		$text = "The fleets listed below were all affected: <br />\n";
		$modulators = $pm->get_number_pulses($shield_owner_name, "Modulator") + 11;
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

		$subject = "Your Electromagnetic Shield was triggered";
		$nm->add_new_news($shield_owner_name, 'player', 'items', $subject, $text);


		$subject = "Some of your forces were destroyed by an Electromagnetic Shield";
		foreach($players_involved as $involved_player => $true_value) {
			$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
		}		
		
	}

	function trigger_microwave_shield_effects($shield_owner_name, $current_tick) {
		$nm = new NewsModel();
		$pm = new PulsesModel();
		$dm = new DevelopmentModel();
		$victims = $pm->get_pulse_victims($shield_owner_name, $current_tick);

		// Finally, fire it
		$players_involved = array();
		$text = "The fleets listed below were all affected: <br />\n";
		$modulators = $pm->get_number_pulses($shield_owner_name, "Modulator") + 11;
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

		$subject = "Your Microwave Shield was triggered";
		$nm->add_new_news($shield_owner_name, 'player', 'items', $subject, $text);


		$subject = "Some of your forces were destroyed by a Microwave Shield";
		foreach($players_involved as $involved_player => $true_value) {
			$nm->add_new_news($involved_player, 'player', 'items', $subject, $text);		
		}		
		
	}
	
	//  Note these functions were copied from the pulse_controller section.  They should be combined at some time but not now

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