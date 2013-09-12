<?php
	require_once('news_model.php5');
	require_once('fleet_model.php5');
	require_once('fleet_data.php5');

class BattleCalculator {

	function battle_calculator($current_tick) {
		$nm = new NewsModel();
		$fm = new FleetModel();
		
		$target_list = $this->get_all_targets($current_tick);
		$targets = "";
		foreach ($target_list as $target_name => $player_details) {
			$targets = $targets . " " . $target_name; // For the universe news

			$attackers = "";
			$defenders = "";
			$total_attack = new FleetData();
			$total_defense = new FleetData();
			
			
			// The following loop goes over each fleet arriving and adds them to the totals and the fleet list
			$player_list = array();
			foreach ($player_details as $player_and_fleet => $fleet_details_string) {
				$fd = new FleetData();
				$player_fleet = split("/", $player_and_fleet);
				array_push($player_list, $player_fleet[0]); // Adds the player name to the array
				
				$fleet_details = split("/", $fleet_details_string);
				$this->add_fleet_to_battle($fd, $fleet_details);
				
				if (strcmp($fleet_details[0], "attack") == 0) {
					$attackers = $attackers . " " . $player_and_fleet . ":" . 
								$this->show_fleet_attributes($fleet_details) . "<BR />";
					$this->add_fleet_to_battle($total_attack, $fleet_details); // Adds to TOTAL
				} else {
					$defenders = $defenders . " " . $player_and_fleet . ":" .
								$this->show_fleet_attributes($fleet_details) . "<BR />";
					$this->add_fleet_to_battle($total_defense, $fleet_details);
				}
			}
			
			$defenders = $this->add_targets_fleet_to_defender($target_name, "home", $defenders, $total_defense);
			if (! $fm->is_active_fleet_orders($target_name, "fleet1") )  {
				$defenders = $this->add_targets_fleet_to_defender($target_name, "fleet1", $defenders, $total_defense);
			}
			if (! $fm->is_active_fleet_orders($target_name, "fleet2") )  {
				$defenders = $this->add_targets_fleet_to_defender($target_name, "fleet2", $defenders, $total_defense);
			}
			if (! $fm->is_active_fleet_orders($target_name, "fleet3") )  {
				$defenders = $this->add_targets_fleet_to_defender($target_name, "fleet3", $defenders, $total_defense);
			}
			
			
			$attacker_damage = $this->get_damage($total_defense, $total_attack);
			$defender_damage = $this->get_damage($total_attack, $total_defense);
			$attacker_capture = $this->get_creature_capture($total_defense, $total_attack);
			$defender_capture = $this->get_creature_capture($total_attack, $total_defense);
			$structures_captured = $this->get_structure_capture($total_attack, $total_defense);
			
			// The following loop applies damage to each fleet in turn
			foreach ($player_details as $player_and_fleet => $fleet_details_string) {
				

			}			
			
			$this->target_battle_news($target_name, $attackers, $defenders, $total_attack, $total_defense,
				$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd);
			
			foreach ($player_list as $player_name) {
				$this->launcher_battle_news
					($player_name, $target_name, $attackers, $defenders, $total_attack, $total_defense,
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd);
			}
		}
		$this->universe_battle_news($current_tick, $targets);
	}



	function get_all_targets($current_tick) {
		$conn = db_connect();	
		$query = "select * from player_orders where arrival_tick<=$current_tick and depart_tick>$current_tick";
	  $result = $conn->query($query);
	  $target_list = array();
	  $count = 0;
		for ($count=0; $row = $result->fetch_object(); $count++) {

			$fleet_list["{$row->player_name}/{$row->fleet}"] = 
			
			$target_list["$row->target_name"]["{$row->player_name}/{$row->fleet}"] = 
				$row->mission_type . "/" . $this->get_fleet_characteristics($row->player_name, $row->fleet);
		}
		return $target_list;
	}	

	function show_fleet_attributes($fleet_details) {
		return $fleet_details[1] . "A "
		. $fleet_details[2] . "D "
		. $fleet_details[3] . "F "
		. $fleet_details[4] . "i "
		. $fleet_details[5] . "d ";
	}
	
	function add_fleet_to_battle(&$battle, $fleet_details) {			
		$battle->att += $fleet_details[1];
		$battle->def += $fleet_details[2];
		$battle->foc += $fleet_details[3];
		$battle->int += $fleet_details[4];
		$battle->dis += $fleet_details[5];
	}

	function add_targets_fleet_to_defender($target_name, $fleet, $defenders, &$total_defense) {
			$target_home_details_string = $this->get_fleet_characteristics($target_name, $fleet);
			$target_home_details = split("/", $target_home_details_string);
			$defenders = $defenders . " " . $target_name . "/{$fleet}" . ":" 
						. $target_home_details[0] . "A "
						. $target_home_details[1] . "D "
						. $target_home_details[2] . "F "
						. $target_home_details[3] . "i "
						. $target_home_details[4] . "d " . "<BR />";

			$total_defense->att += $target_home_details[0];
			$total_defense->def += $target_home_details[1];
			$total_defense->foc += $target_home_details[2];
			$total_defense->int += $target_home_details[3];
			$total_defense->dis += $target_home_details[4];
			
			return $defenders;
	}
	
	function get_fleet_characteristics($player_name, $fleet) {
		$att = 0;
		$def = 0;
		$foc = 0;
		$int = 0;
		$dis = 0;

		$conn = db_connect();	
		$query = "select * from player_creatures pc, creature_items ci 
			 where pc.player_name='$player_name'
			   and pc.fleet_location='$fleet'
			   and pc.creature=ci.name";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$att += $row->number * $row->attack;	
			$def += $row->number * $row->defense;	
			$foc += $row->number * $row->focus;	
			$int_total += ceil (($row->number * $row->attack * $row->intelligence));	
			$dis_total += ceil (($row->number * $row->defense * $row->discipline));	
		}
			$int=ceil ($int_total/100);
			$dis=ceil ($dis_total/100);
		return "{$att}/{$def}/{$foc}/{$int}/{$dis}"; 
	}

	function new_get_fleet_characteristics($player_name, $fleet) {
		$fd = new FleetData()
		$fd->creatures = array();

		$conn = db_connect();	
		$query = "select * from player_creatures pc, creature_items ci 
			 where pc.player_name='$player_name'
			   and pc.fleet_location='$fleet'
			   and pc.creature=ci.name";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fd->creatures['$row->creature'] = $row->number;
			
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



	
	function get_damage($attack, $defense) {
		$attack_ratio = $attack->att/ ($defense->def + 1);
		if ($attack_ratio > 10.0) $damage = 100;
		else if ($attack_ratio > 7.0) $damage = 80;
		else if ($attack_ratio > 4.0) $damage = 60;
		else if ($attack_ratio > 3.0) $damage = 40;
		else if ($attack_ratio > 2.0) $damage = 20;
		else if ($attack_ratio > 1.0) $damage = 10;
		else if ($attack_ratio > 0.7) $damage = 8;
		else if ($attack_ratio > 0.4) $damage = 6;
		else if ($attack_ratio > 0.3) $damage = 4;
		else if ($attack_ratio > 0.2) $damage = 2;
		else if ($attack_ratio > 0.1) $damage = 1;
		else $damage = 0;
		
		return $damage;
	}

	function get_creature_capture($attack, $defense) {
		$capture_ratio = $attack->int/($defense->dis+1);
		if ($capture_ratio > 10.0) $damage = 50;
		else if ($capture_ratio > 7.0) $damage = 30;
		else if ($capture_ratio > 4.0) $damage = 20;
		else if ($capture_ratio > 3.0) $damage = 15;
		else if ($capture_ratio > 2.0) $damage = 10;
		else if ($capture_ratio > 1.0) $damage = 5;
		else $damage = 0;
		
		return $damage;
	}
	
	function get_structure_capture($attack, $defense) {
		$capture_ratio = ($attack->att - $defense->def) / ($attack->att+1) ;
		$capture_focus = $attack->foc * $capture_ratio;
		
		if ($attack->att > ($defense->def * 10) ) $capture_focus *= 2;
		
		if ($capture_focus < 0) $capture_focus = 0;

		$structures_captured = ceil ($capture_focus / 1000);
		
		return $structures_captured;
	}
	
	
	
	function target_battle_news($target_name, $attackers, $defenders, $attack, $defense,
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd) {
		$nm = new NewsModel();
		$subject = 'Your continent is being attacked';
		$text = " This is a test <BR /><BR />
			You are being targeted.  <BR /><BR />
			If this were an actual attack, you would be in trouble, but its not...<BR />";
		$this->display_attack_table($target_name, $target_name, $attackers, $defenders, $attack, $defense,
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd, 
					$subject, $text);
	}

	function launcher_battle_news($player_name, $target_name, $attackers, $defenders, $attack, $defense, 
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd) {
		$nm = new NewsModel();
		
		$subject = "Your fleet has landed at {$target_name}\'s continent";
		$text = " This is a test <BR /><BR />
			If this were an actual attack, real damage would be dealt, but its not...<BR />";
		$this->display_attack_table($player_name, $target_name, $attackers, $defenders, $attack, $defense,
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd,
					$subject, $text);
	}

	function display_attack_table($player_name, $target_name, $attackers, $defenders, $attack, $defense,
					$attacker_damage, $defender_damage, $attacker_capture, $defender_capture, $structures_captured, $fd,
					$subject, $text) {
		$nm = new NewsModel();
			
		$text = "<TABLE class=\'STD\'>
				<TR><TH class=\'STD\'>Attackers</TH><TH class=\'STD\'>Defenders</TH></TR>
				<TR>
				 <TD class=\'STD\'>
					Att: $attack->att Def: $attack->def Foc: $attack->foc Int: $attack->int Dis: $attack->dis
				 </TD>
				 <TD class=\'STD\'>
					Att: $defense->att Def: $defense->def Foc: $defense->foc Int: $defense->int Dis: $defense->dis
				 </TD>
				</TR>
				<TR><TD class=\'STD\'>$attackers</TD><TD class=\'STD\'>$defenders</TD></TR>";
			if (strcmp($fd->mission, "attack") == 0) {
				 $text = $text . "<TR>
				 <TD class=\'STD\'>" . $fd->get_fleet_info() . "</TD> 
				 <TD class=\'STD\'>&nbsp</TD>
				</TR>";
			} else {
				 $text = $text . "<TR>
				 <TD class=\'STD\'>&nbsp</TD>
				 <TD class=\'STD\'>" . $fd->get_fleet_info() . "</TD> 
				</TR>";
				
			}
			$text = $text . "
				<TR>
				 <TD class=\'STD\'>Attackers Captured: {$attacker_capture}%</TD>
				 <TD class=\'STD\'>Defenders Captured: {$defender_capture}%</TD>
				</TR>
				<TR>
				 <TD class=\'STD\'>Remaining Attackers Killed: {$attacker_damage}%</TD>
				 <TD class=\'STD\'>Remaining Defenders Killed: {$defender_damage}%</TD>
				</TR>
				<TR>
				 <TD class=\'STD\' colspan=\'2\'>Structures Captured: {$structures_captured}</TD>
				</TR>
			</TABLE>
				";
		$nm->add_new_news($player_name, 'player', 'high', $subject, $text);
	}
	

	function universe_battle_news($current_tick, $targets) {
		$nm = new NewsModel();
		$subject = "Tick #{$current_tick} battle report";
		if (strlen($targets) > 0){	
			$text = "The following players are targets of attacks this tick: $targets <BR /><BR />
				At the moment no damage is being done, but the attack is taking place right now";
		} else {
			$text = "There are no battles this tick";
		}	
		$nm->add_new_news("", 'universe', 'status', $subject, $text);
	}

}
?>