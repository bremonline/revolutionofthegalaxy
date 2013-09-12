<?php
	require_once('view_fns.php5'); 
	require_once('game_model.php5'); 

class AdminController {
	function check_actions() {
		$gm = new GameModel();

  	$action=$_REQUEST['action'];
 		if      (strcmp ( $action, 'promote_player') == 0) $this->promote_player(); 
 		else if (strcmp ( $action, 'reset_game') == 0) $gm->reset_game();
 		else if (strcmp ( $action, 'advance_tick') == 0) $gm->advance_multiple_ticks();
			
	}
	
	function promote_player() {
  	$promoted_player=$_REQUEST['promoted_player'];
  	$admin_type=$_REQUEST['admin_type'];

		$pd = new PlayerData;
		$status = $pd->promote($promoted_player, $admin_type);
		if ($status == true) {
			show_info("Successfully promoted: $promoted_player to $admin_type");
		} else {
			show_error("Could not promote: $promoted_player to $admin_type");	
		}
	}
}

?>