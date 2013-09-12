<?php
	require_once('../player_data.php5'); 
	require_once('../overview_view.php5'); 
	require_once('../fleet_view.php5'); 

class ChatExtensions {

	function get_top_bar() {
		$player_name = $_SESSION['player_name'];
		$player = new PlayerData();
		$player->db_fill($player_name);
		
		$total_structures = $player->unassigned + $player->extractor + $player->genetic_lab + $player->powerplant + $player->factory;
		$total_allocated = $player->extractor + $player->genetic_lab + $player->powerplant + $player->factory;
		$ratio = ceil (1000* $total_allocated / $total_structures)/10;
		
		$string = "";
		$string .= "<TABLE class='STD' ><TR><TD class='STD'>";
		$string .= "<TABLE class='STATS'>";
		$string .= "<TR>";
		$string .= "<TD class='STATS' style='text-align:left;' id='player_name'> (Revo+ v0.0.0) $player_name of $player->location </TD>";
		$string .= "<TD class='STATS' style='text-align:right;color:#AAA;'>";
		$string .= "Score: <SPAN style='color:#FFF;' id='score'>" . number_format($player->score) . "</SPAN>";
		$string .= "</TD>";
		$string .= "</TR>";
		$string .= "<TR>";
		$string .= "<TD class='STATS' colspan='2' style='color:#AAA;'> ";
		$string .= "Min.: <SPAN style='color:#FFF;' id='mineral'>" . number_format($player->mineral) . "</SPAN>";
		$string .= "Org.: <SPAN style='color:#FFF;' id='organic'>" . number_format($player->organic) . "</SPAN>";
		$string .= "Eng: <SPAN style='color:#FFF;' id='energy'>" . number_format($player->energy) . "</SPAN>";
		$string .= "</TD>";
		$string .= "</TR>";
		$string .= "<TR>";
		$string .= "<TD class='STATS' style='color:#AAA;'> ";
		$string .= "Total: <SPAN style='color:#FFF;' id='total_structures'>"  . number_format($total_structures) . "</SPAN>";
		$string .= "Ratio: <SPAN style='color:#FFF;' id='ratio'>{$ratio}%</SPAN>";
		$string .= "UA: <SPAN style='color:#FFF;' id='unassigned'>"   . number_format($player->unassigned) . "</SPAN>";
		$string .= "Extr: <SPAN style='color:#FFF;' id='extractor'>"   . number_format($player->extractor) . "</SPAN>";
		$string .= "Labs: <SPAN style='color:#FFF;' id='genetic_lab'>"   . number_format($player->genetic_lab) . "</SPAN>";
		$string .= "Power: <SPAN style='color:#FFF;' id='powerplant'>"   . number_format($player->powerplant) . "</SPAN>";
		$string .= "Fac: <SPAN style='color:#FFF;' id='factory'>"   . number_format($player->factory) . "</SPAN>";
		$string .= "<BR />    ";
		$string .= "</TD>";
		$string .= "<TD class='STATS' colspan='2' style='text-align:right;color:#AAA;'> ";
		$string .= "Min. Target Score: <SPAN style='color:#8F8;' id='min_score'>" . number_format($player->score / 2) . "</SPAN>";
		$string .= "| Max Attacker Score: <SPAN style='color:#F88;' id='max_score'>" . number_format($player->score * 2) . "</SPAN>";
		$string .= "</TD>";
		$string .= "</TR>";
		$string .= "</TABLE>";
		$string .= "<TD></TR></TABLE>";

		return $string;
	}
	
	function get_top_bar_wrapped($player_name, $tick) {
		$unique = time();
		$string = $this->get_top_bar();
		
		$new_string =  "<DIV id='top_bar_{$unique}' style='display:none' >$string</DIV>" .
		"<INPUT name='{$player_name}_top_bar' id='send_top_bar' type='Button' value='Top Bar for {$player_name} on Tick $tick'" .
		" onmouseover='TagToTip(\"top_bar_{$unique}\", DELAY, 1000)'/>";
		return htmlentities($new_string, ENT_QUOTES);

	}

	function get_current_items_box() {
		$ov = new OverviewView();
		
		return $ov->get_current_items_display();
	}
	
	function get_current_items_box_wrapped($player_name, $tick) {
		$unique = time();

		$string = $this->get_current_items_box();
		
		$new_string =  "<DIV id='items_{$unique}' style='display:none' >$string</DIV>" .
		 "<INPUT name='{$player_name}_current_items_box' id='send_current_items_box' type='Button' " .
		 " value='Current Items Box for {$player_name} on Tick $tick' onmouseover='TagToTip(\"items_{$unique}\", DELAY, 1000)'/>";
		return htmlentities($new_string, ENT_QUOTES);

	}

	function get_fleet_box() {
		$fv = new FleetView();
		
		return $fv->make_fleets_on_mission_display(false);
	}
	
	function get_fleet_box_wrapped($player_name, $tick) {
		// Need unique id for the message, lets take the # milliseconds
		$unique = time();
		
		$string = $this->get_fleet_box();
		
		
		$new_string =  "<DIV id='fleet_{$unique}' style='display:none' >$string</DIV>" .
		 "<INPUT name='{$player_name}_fleet_box' id='send_fleet_box' type='Button' value='Fleet Box for {$player_name} on Tick $tick'" .
		 " onmouseover='TagToTip(\"fleet_{$unique}\", DELAY, 1000)'/>";
		return htmlentities($new_string, ENT_QUOTES);

	}

	function get_build_box() {
		$ov = new OverviewView();
		
		return $ov->make_current_builds_display();
	}
	
	function get_build_box_wrapped($player_name, $tick) {
		// Need unique id for the message, lets take the # milliseconds
		$unique = time();
		
		$string = $this->get_build_box();
		
		
		$new_string =  "<DIV id='build_{$unique}' style='display:none' >$string</DIV>" .
		 "<INPUT name='{$player_name}_build_box' id='send_build_box' type='Button' value='Build for {$player_name} on Tick $tick'" .
		 " onmouseover='TagToTip(\"build_{$unique}\", DELAY, 1000)'/>";
		return htmlentities($new_string, ENT_QUOTES);

	}


}

?>