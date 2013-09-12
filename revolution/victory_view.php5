<?php
	require_once('view_fns.php5'); 
	require_once('game_model.php5'); 
	require_once('development_model.php5'); 
	require_once('alliance_model.php5'); 
	require_once('player_data.php5'); 

class VictoryView {

	function display_victory_view() {
		echo "<TABLE class='STD'>\n";
		echo " <TR>\n";
		$num_mineral_victors = $this->display_victory_condition("Mineral", "Mineral Victory Condition");
		$num_organic_victors = $this->display_victory_condition("Organic", "Organic Victory Condition");
		$num_energy_victors = $this->display_victory_condition("Energy", "Energy Victory Condition");
		echo " </TR>";
		echo " <TR>\n";
		$this->display_resources_per_tick("Mineral", $num_mineral_victors);
		$this->display_resources_per_tick("Organic", $num_organic_victors);
		$this->display_resources_per_tick("Energy", $num_energy_victors);
		echo " </TR>";
		echo "</TABLE>\n";
		echo "<TABLE class='STD' style='border:0px;'>\n";
		echo "<TR>";
		echo "<TD class='STD' style='background-color:#A04020'> Full Victory Condition </TD>";
		echo "<TD class='STD' style='background-color:#807020'> Player is on vacation </TD>";
		echo "<TD class='STD' style='background-color:#20A020'> Player needs more structures </TD>";
		echo "</TR>";
		echo "</TABLE>\n";
	}
	
	function display_victory_condition($resource, $tech) {
		$dm = new DevelopmentModel();
		$pd = new PlayerData();
		$vf = new ViewFunctions();
		$am = new AllianceModel();
						
		$number_victors = 0;
		echo "<TH class='STD' style='vertical-align:top;background-color:000000'>";
		echo "<TABLE class='LIST' style='border:0px;'>\n";

		if ($resource == "Mineral") echo "<TR><TH class='STD' COLSPAN='3'> Victory: $resource <br /> (Need 5000 Extractors)</TH></TR>\n";
		else if ($resource == "Organic") echo "<TR><TH class='STD' COLSPAN='3'> Victory: $resource <br /> (Need 2500 Genetic Labs)</TH></TR>\n";
		else if ($resource == "Energy") echo "<TR><TH class='STD' COLSPAN='3'> Victory: $resource <br /> (Need 1250 Powerplants)</TH></TR>\n";
		else echo "<TR><TH class='STD' COLSPAN='3'> Victory: $resource </TH></TR>\n";

		$player_list = $dm->get_all_players_with_development($tech);
		$count_player_list = count($player_list);
		if ($count_player_list == 0) {
				echo "<TR><TD class='STD' ><I>No Players</I></TD></TR>";
		} else {
				echo "<TR><TH class='STD'>Alliance</TH><TH class='STD'>Player</TH><TH class='STD'># Str.</TH></TR>";
			for ($i=0; $i < $count_player_list; $i++) {
				$player_name = $player_list[$i];
				$pd->db_fill($player_name);
				if ($resource == "Mineral") {
					$structures = $pd->extractor;
					if ($structures >= 5000) { 
						if ($pd->status != 'active') {
							$color = '807020'; $hcolor = 'A09040';
						} else {
							$color = 'A04020'; $hcolor = 'C06040'; 
							$number_victors++;
						}
				} else { 
						$color = '20A020'; $hcolor = '40C040'; 
					}
				} else if ($resource == "Organic") {
					$structures = $pd->genetic_lab;
					if ($structures >= 2500) { 
						if ($pd->status != 'active') {
							$color = '807020'; $hcolor = 'A09040';
						} else {
							$color = 'A04020'; $hcolor = 'C06040'; 
							$number_victors++;
						}
					} else { 
				 		$color = '20A020'; $hcolor = '40C040'; 
					}
				} else if ($resource == "Energy") {
					$structures = $pd->powerplant;
					if ($structures >= 1250) { 
						if ($pd->status != 'active') {
							$color = '807020'; $hcolor = 'A09040';
						} else {
							$color = 'A04020'; $hcolor = 'C06040'; 
							$number_victors++;
						}
					} else { 
						$color = '20A020'; $hcolor = '40C040'; 
					}
				}
				$alliance = $am->get_alliance_of_player($player_name);
				if ($alliance == '') $alliance = '&nbsp;';
				
				echo "<TR>";
				echo "<TD class='STD' style='background-color:$color'>$alliance</TD>";
				$vf->display_id_button("$player_name", $color, $hcolor, "victory_{$player_name}", "", "main_page.php5?main_page.php5?view=profile&profile_name=$player_name");
				echo "<TD class='STD' style='background-color:$color'>$structures</TD>";
				echo "</TR>";
			}
		}
		echo "</TABLE>\n";


		echo "</TH>\n";
		
		return $number_victors;
	}	
	
	function display_resources_per_tick($resource, $number_victors) {
 		$gm = new GameModel();
 		if ($resource == 'Mineral') $game_parameter = 'mineral_per_structure';
 		else if ($resource == 'Organic') $game_parameter = 'organic_per_structure';
 		else if ($resource == 'Energy') $game_parameter = 'energy_per_structure';
 		
 		$starting_resources_per_tick = $gm->get_game_parameter("$game_parameter");
		$rpt = $starting_resources_per_tick - (50 * $number_victors);
		if ($rpt < 0) $rpt = 0;
		echo "<TH class='STD'>$resource Per Structure Per Tick: $rpt</TH>";

	}
	
}
?>