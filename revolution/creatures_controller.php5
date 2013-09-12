<?php
	require_once('creatures_model.php5'); 
	require_once('development_model.php5'); 
	require_once('research_model.php5'); 
	require_once('player_data.php5'); 
	require_once('game_model.php5'); 

class CreaturesController {

	function create_creatures() {
  	$player_name=$_SESSION['player_name'];
		$dm = new DevelopmentModel();
		

	  $conn = db_connect();
		$query = "select * from creature_items";
		$result = $conn->query($query);
		$mineral=0;
		$organic=0;
		$total_creatures=0;
		for ($count=0; $row = $result->fetch_object(); $count++) {
			
			$creature_number = floor($_REQUEST["create_{$row->name}"]);
			if ($creature_number > 0) {
				$total_creatures += $creature_number;
				if ($creature_number > 0) {
				// First check to see that they have the right tech to make any creature they are trying to build.
					if (! $dm->does_player_know_development($player_name, $row->development_item) ) {
						show_error("You have not developed the technology to make these creatures");
						return;
					}
					$mineral += $creature_number * $row->mineral; 
					$organic += $creature_number * $row->organic; 
				}
			}
		}
		$pd = new PlayerData();
		$pd->db_fill($player_name);
	
		$cm = new CreaturesModel();
		$production = $cm->get_current_creatures_in_production($player_name);
		$gm= new GameModel();
		$base_creature_production = $gm->get_game_parameter('base_creature_production');
  	if ($total_creatures + $production > $pd->factory + $base_creature_production) {
  		show_error("You cannot build that many creatures at one time.");
			return;
  	}
		
		
		
		if ($mineral > $pd->mineral || $organic > $pd->organic) {
	 		show_error("Not enough resources to produce those creatures") ;
			return;
		}
		$pd->subtract('mineral', $mineral);
		$pd->subtract('organic', $organic);

		$gm = new GameModel();
		$tick_started = $gm->get_current_tick();
		mysqli_data_seek($result, 0); // Resets the result set so we can scroll through to provision
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature_number = floor($_REQUEST["create_{$row->name}"]);
			if ($creature_number > 0) {
				$this->provision_creature($row->name, $creature_number, $row->ticks, $tick_started);
			}
		}
	}
		
	function provision_creature($creature, $number, $ticks, $tick_started){
  	$player_name=$_SESSION['player_name'];
  	

	  $conn = db_connect();
		$query = "insert into player_build values 
			('$player_name', 'creature', '$creature', $number, $tick_started, $ticks, 'building')";
		$result = $conn->query($query);
	}
}
?>