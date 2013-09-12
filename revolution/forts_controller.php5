<?php 
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('forts_model.php5'); 
	require_once('player_data.php5'); 

class FortsController {
	function create_forts() {
 		$player_name=$_SESSION['player_name'];
		$forts = floor($_REQUEST["forts"]);

		$fm = new FortsModel();
		
		$details = $fm->get_fort_details();
		$mineral_cost = $details["mineral"] * $forts;
		$organic_cost = $details["organic"] * $forts;
		$ticks = $details["ticks"];

		// Check to see if you have the factory space to build
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$production = $fm->get_current_bombs_and_forts_in_production($player_name);
		$gm= new GameModel();
		$base_production = $gm->get_game_parameter('base_creature_production');
  	if ($forts + $production > $pd->factory + $base_production) {
  		show_error("You cannot build that many bombs, traps, and forts at one time.");
			return;
  	}

		// Check to see if player can afford it
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		
		if ($pd->mineral < $mineral_cost) {
			show_error("You do not have enough resources");
			return;
		}
		if ($pd->organic < $organic_cost) {
			show_error("You do not have enough resources");
			return;
		}
		
		// OK take the money away
		$pd->subtract("mineral", $mineral_cost);
		$pd->subtract("organic", $organic_cost);
		
		// Now provision the forts
		$gm = new GameModel();
  	$current_tick = $gm->get_current_tick();
		
		if ($forts > 0) {
			$fm->provision_forts($player_name, $forts, $ticks, $current_tick);
		}
	}
}

?>