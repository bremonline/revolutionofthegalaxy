<?php
	require_once("view_fns.php5");
	require_once("game_model.php5");
	require_once("player_data.php5");
	
class VacationController {
	function go_on_vacation() {
		$player_name = $_SESSION["player_name"];
		$pd = new PlayerData();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		$pd->put_player_on_vacation($player_name, $current_tick);
		show_info("You are now on vacation until at least tick:" . ($current_tick + 36) );
	}
	
	function reactivate_player() {
		$player_name = $_SESSION["player_name"];
		$pd = new PlayerData();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		$pd->reactivate_player($player_name);
		show_info("You are now off vacation" );
		
	}
}

?>