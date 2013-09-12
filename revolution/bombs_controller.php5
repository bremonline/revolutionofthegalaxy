<?php 
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('bombs_model.php5'); 
	require_once('player_data.php5'); 

class BombsController {
	function create_bombs() {
		$bombs = floor($_REQUEST["bombs"]);
		$poison_bombs = floor($_REQUEST["poison_bombs"]);
		$traps = floor($_REQUEST["traps"]);
		$psych_traps = floor($_REQUEST["psych_traps"]);
		$this->create_bombs_or_traps("Bomb", $bombs);
		$this->create_bombs_or_traps("Poison Bomb", $poison_bombs);
		$this->create_bombs_or_traps("Trap", $traps);
		$this->create_bombs_or_traps("Psychological Trap", $psych_traps);
		
	}
	
	function create_bombs_or_traps($type, $number) {
		if ($number < 1) return;
 		$player_name=$_SESSION['player_name'];

		
		$bm = new BombsModel();
		$details = $bm->get_bomb_details("$type");
		$mineral_cost = $details["mineral"] * $number;
		$organic_cost = $details["organic"] * $number;
		$ticks = $details["ticks"];

		// Check to see if you have the factory space to build
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$production = $bm->get_current_bombs_and_forts_in_production($player_name);
		$gm= new GameModel();
		$base_production = $gm->get_game_parameter('base_creature_production');
  	if ($number + $production > $pd->factory + $base_production) {
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
		
		// Now provision the bombs
		$gm = new GameModel();
  	$current_tick = $gm->get_current_tick();
		
		
		$bm->provision_bombs($player_name, $type, $number, $ticks, $current_tick);
		
	}
	
	function change_trap_status() {
		$bm = new BombsModel();
  	$player_name=$_SESSION['player_name'];
  	$to_activate_traps = $_REQUEST['to_activate_traps'];
  	$to_deactivate_traps = $_REQUEST['to_deactivate_traps'];
  	$to_activate_psych_traps = $_REQUEST['to_activate_psych_traps'];
  	$to_deactivate_psych_traps = $_REQUEST['to_deactivate_psych_traps'];
  	
  	if ($to_activate_traps > 0) $this->activate_traps($player_name, $to_activate_traps, "Trap");
  	if ($to_deactivate_traps > 0) $this->deactivate_traps($player_name, $to_deactivate_traps, "Trap");
  	if ($to_activate_psych_traps > 0) $this->activate_traps($player_name, $to_activate_psych_traps, "Psychological Trap");
  	if ($to_deactivate_psych_traps > 0) $this->deactivate_traps($player_name, $to_deactivate_psych_traps, "Psychological Trap");
		
	}
	
	function activate_traps($player_name, $to_activate, $type) {
		if ($to_activate == 0) return;
		$bm = new BombsModel();

		// First find out how many are available
		$number_active = $bm->get_number_bombs_at_location($player_name, $type, "active");
		$number_inactive = $bm->get_number_bombs_at_location($player_name, $type, "inactive");

		if ($to_activate > $number_inactive)  $to_activate = $number_inactive;
		
		// First remove the bombs from the inactive list
		if ($to_activate ==  0) return;  // Nothing to activate

		if ($to_activate == $number_inactive)  {
			$bm->remove_row($player_name, $type, "inactive");
		} else {
			$bm->subtract($player_name, $type, "inactive", $to_activate);
		}

	
		// Then add them to the active list
		if ($number_active <= 0) {
			$bm->make_new_row($player_name, $type, "active", $to_activate);
		} else {
			$bm->add($player_name, $type, "active", $to_activate);
		}
	}

	function deactivate_traps($player_name, $to_deactivate, $type) {
		if ($to_deactivate == 0) return;
		$bm = new BombsModel();

		// First find out how many are available
		$number_active = $bm->get_number_bombs_at_location($player_name, $type, "active");
		$number_inactive = $bm->get_number_bombs_at_location($player_name, $type, "inactive");
		
		
		if ($to_deactivate > $number_active)  $to_deactivate = $number_active;

		if ($to_deactivate ==  0) return;  // Nothing to deactivate
		
		// First remove the bombs from the inactive list
		if ($to_deactivate == $number_active)  {
			$bm->remove_row($player_name, $type, "active");
		} else {
			$bm->subtract($player_name, $type, "active", $to_deactivate);
		}
		
		// Then add them to the active list
		if ($number_inactive <= 0) {
			$bm->make_new_row($player_name, $type, "inactive", $to_deactivate);
		} else {
			$bm->add($player_name, $type, "inactive", $to_deactivate);
		}
	}
	
}

?>